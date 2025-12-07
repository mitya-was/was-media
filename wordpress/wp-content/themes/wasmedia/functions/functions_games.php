<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 7/18/2017
 * Time: 12:22
 */

use Curl\Curl;
use Utils\Response;

/**
 * @param $val
 * @param $isScore
 *
 * @return string
 */

function getGameHash($val, $isScore) {

    if (!$isScore) {
        $keyPart = 0;

        while ((($key = pow(rand(1, rand(1, 15)) * 3, 2)) % 3) == 0) {
            $keyPart = ($val) ? $key : ($key - 1);
            break;
        }

        $hash = str_pad(rand(0000, 9999), 4, 0, STR_PAD_LEFT) . $keyPart . strlen((string)$keyPart);
    } else {
        $hash = $val;
    }

    return $hash;
}

function get_game_stats() {
    $response = new Response();
    $appId = $statType = false;
    //$statOptions = [];

    $response->failure();

    if (isset($_GET["appId"]) && $_GET["appId"] != "") {
        $appId = intval($_GET["appId"]);
    }

    if (isset($_GET["statType"]) && $_GET["statType"] != "") {
        $statType = ($_GET["statType"]) ? "scores" : "boolean";
    }

    try {
        $curl = new Curl();
        $curl->setHeader("Content-Type", "application/json");
        $curl->get("http://localhost:8080/stats/was/{$statType}/{$appId}");

        if (isset(($curl->response)->success) && (($curl->response)->success === true) && isset(($curl->response)->data)) {
            $response->success();
            $response->setData(($curl->response)->data);
        } else {
            $response->setError(($curl->response)->error);
        }

        $curl->close();

    } catch (ErrorException $e) {
        $response->setError($e->getMessage());
    }

    echo $response->toJSON();

    die();
}

add_action('wp_ajax_get_game_stats', 'get_game_stats');
add_action('wp_ajax_nopriv_get_game_stats', 'get_game_stats');

function collect_game_stats() {
    $response = new Response();
    $gameData = json_decode(file_get_contents("php://input"));

    $response->failure();

    /** @var array $gameData */
    if (is_array($gameData) && count($gameData) > 0) {

        $gameData = array_map(
            function ($item) {
                return [
                    "id"            => "",
                    "appId"         => $item->appId,
                    "sessionId"     => $item->sessionId,
                    "questionIndex" => $item->alphaIndex,
                    "correctIndex"  => $item->bravoIndex,
                    "userAnswer"    => $item->charlieIndex,
                    "answerTime"    => $item->deltaIndex,
                    "isWin"         => $item->echoIndex
                ];
            },
            $gameData
        );

        try {
            $curl = new Curl();
            $curl->setHeader("Content-Type", "application/json");
            $curl->post('http://localhost:8080/stats/save/was', json_encode($gameData));

            if (isset(($curl->response)->success) && isset(($curl->response)->data)) {
                $response->success();
                $response->setData(($curl->response)->data);
            }

            $curl->close();

        } catch (ErrorException $e) {
            $response->setError($e->getMessage());
        }
    }

    echo $response->toJSON();

    die();
}

add_action('wp_ajax_collect_game_stats', 'collect_game_stats');
add_action('wp_ajax_nopriv_collect_game_stats', 'collect_game_stats');

function reset_game_stats() {
    $response = new Response();

    $response->failure();

    if (isset($_POST["post_id"]) && $_POST["post_id"] != "") {

        try {
            $curl = new Curl();

            $curl->delete("http://localhost:8080/stats/delete/was/{$_POST['post_id']}");

            if (isset(($curl->response)->success) && isset(($curl->response)->data)) {
                $response->success();
                $response->setData(($curl->response)->data);
            }

            $curl->close();

        } catch (ErrorException $e) {
            $response->setError($e->getMessage());
        }
    }

    echo $response->toJSON();

    die();
}

add_action('wp_ajax_reset_game_stats', 'reset_game_stats');

/**
 * @param WP_Post $post
 */
function reset_game_statistics($post) {

    if (in_category(["games"], $post)) : ?>
        <div class="reset_game_statistics">
            <span id="was_reset_game" class="button">Reset Game Statistics</span>
            <span id="was_reset_game_message"></span>
        </div>
    <?php endif;
}

add_action('post_submitbox_misc_actions', 'reset_game_statistics');

function get_game_items(){

	$resp = [];
	$gameData = json_decode(file_get_contents("php://input"));

	if ($gameData->postId && have_rows('admin_post_layout', $gameData->postId)) {

		while (have_rows('admin_post_layout', $gameData->postId)) {
			the_row();

			$layout = get_row_layout();

			if ($layout == 'add_game')
			{
				$game_items = get_sub_field_object('game_items');
				$resp['questions'] = $game_items['value'];

				$game_custom_options = get_sub_field_object('game_custom_options');


				if(count($values = $game_custom_options['value'])){

					foreach ($values as $value) {

						$multipleValue = (explode(':', $value['value']));

						if (count($multipleValue) == 1){
							$resp['game_custom_options'][]['value'] = $multipleValue[0];
						} elseif (count($multipleValue) == 2){
							$resp['game_custom_options'][][$multipleValue[0]] = $multipleValue[1];
						}

					}

				}

			}
		}
	}

	$response = new Response();

	if (count($resp) > 0){
		$response->setData($resp);
		$response->success();
		die($response->toJSON());
	} else {
		$response->failure();
		die($response->toJSON());
	}

}

add_action('wp_ajax_game_items', 'get_game_items');
add_action('wp_ajax_nopriv_game_items', 'get_game_items');



function set_allowed_http_origins( $origins ) {
	$hosts = ['localhost', 'dev.was.media.lc', 'localhost:3000', 'localhost:3001'];
	foreach ( $hosts as $host ) {
		$origins = array_merge( $origins, array( 'http://' . $host, 'https://' . $host ) );
	}
	return array_unique( $origins );

}

add_filter( 'allowed_http_origins', 'set_allowed_http_origins' );
