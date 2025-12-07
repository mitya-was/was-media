<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/wp-content/themes/wasmedia/vendor/autoload.php';

use Utils\SendGrid;

////////////////////////////////////////////////////
// Add recipients #
// POST /contactdb/recipients #
function add_recipients($email) {
    global $sg;

    $request_body = json_decode('[
		{
			"email": "' . $email . '"
		}
	]');
    $response = $sg->client->contactdb()->recipients()->post($request_body);

    $response->mybody = false;

    if ($response->statusCode() == 201) {
        $response->mybody = json_decode($response->body());
    }

    return $response;
}

// Add a Single Recipient to a List #
// POST /contactdb/lists/{list_id}/recipients/{recipient_id} #
function add_recipient_to_list($recipient, $list) {
    global $sg;

    $response = $sg->client->contactdb()->lists()->_($list)->recipients()->_($recipient)->post();

    return $response;
}

function show_error($redirect_url = "") {
    setcookie("subs_status", "error", time() + 3600);
    header("Location: " . (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $redirect_url);

    die;
}

$hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);
$admin = "team@was.media";

require_once(__DIR__ . "/../wp-config.php");

global $wp;

$wp->init();
$wp->register_globals();
$wp->send_headers();

global $wpdb;

$table = "B_Subscription";
$data = $wpdb->get_row("SELECT * FROM $table WHERE hash = '{$hash}'");

if (is_object($data) && isset($data->ID) && $data->ID) {

    if ($data->lang) {

        switch ($data->lang) {
            case "ru":
                $list = 1131381;
                break;

            case "uk":
                $list = 1795078;
                break;

            default:
                header('Location: https://was.media/', true, 301);
                die;
        }
    } else {
        header('Location: https://was.media/', true, 301);
        die;
    }

$sendgrid_key = getenv('SENDGRID_API_KEY') ?: '';

if (!$sendgrid_key) {
    error_log('SendGrid API key is not set');
    show_error("/" . pll_current_language());
}

$sg = new SendGrid($sendgrid_key);

    if ($data->email) {
        $response = add_recipients($data->email);

        if ($response->mybody) {

            if ($response->mybody->error_count > 0) {
                show_error("/" . pll_current_language());

            } elseif (isset($response->mybody->persisted_recipients) && is_array($response->mybody->persisted_recipients) && count($response->mybody->persisted_recipients)) {
                $recipient = $response->mybody->persisted_recipients[0];
                $response2 = add_recipient_to_list($recipient, $list);

                if ($response2->_status_code != 201) {
                    show_error("/" . pll_current_language());
                }
            }
        }

        setcookie("subs_status", "success", time() + 3600, "/");

        $wpdb->delete($table, array('ID' => $data->ID));

        wp_redirect(home_url() . "/" . pll_current_language() . "/", 303);
    } else {
        show_error();
    }
    die;
}

//TODO Add redirection script + redirection timer
echo "Hash not found or possible subscription duplication. Please contact support by <a href='mailto:{$admin}'>e-mail</a>";