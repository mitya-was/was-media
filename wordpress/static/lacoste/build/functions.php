<?php
if (!defined('ROOTPATH')) {
	define('ROOTPATH', dirname(__FILE__) . '/../../../');
}

if (file_exists(ROOTPATH . 'wp-config.php')) {
	require_once(ROOTPATH . 'wp-config.php');
}

function getSnippetPath($default = '')
{
	return getByNameGetParam('url', $default);
}

function getOgUrlPath()
{
	if (isset($_GET['picture'])) {
		$url = isset($_GET['picture']) ? get_site_url() . '/static/lacoste/build?picture=' . $_GET['picture'] : get_site_url() . '/static/lacoste/build/';
	} else {
		$url = isset($_GET['postId']) ? get_site_url() . '/static/lacoste/build?postId=' . $_GET['postId'] : get_site_url() . '/static/lacoste/build/';
	}
	return $url;
}

function getLangIso($default = '')
{
	return getByNameGetParam('lang', $default);
}

function getDescription($default = '')
{
	return substr(getByNameGetParam('description', $default), 0, 200);
}

function getTitle($default = '')
{

	return getByNameGetParam('title', $default);
}

function getByNameGetParam($name, $default = '')
{
	global $wpdb;
	global $table_prefix;
	$tableLacoste = $table_prefix . 'game_lacoste';

	if (isset($_GET['picture'])) {
		$id_post = $wpdb->get_var("SELECT id_post FROM " . $tableLacoste . " WHERE id_image = '" . $_GET['picture'] . "'");
	} else {
		$id_post = $_GET['postId'];
	}
	$post = get_post($id_post);
	if ($name == 'description') {
		$post_desc = get_post_meta((int)$id_post, '_yoast_wpseo_metadesc', true);
		return $post_desc ? $post_desc : $default;
	} elseif ($name == 'title') {
		return $post->post_title ? $post->post_title : $default;
	} elseif ($name == 'lang') {
		$lang = pll_get_post_language($id_post);
		return $lang == 'uk' ? 'uk-UK' : $default;
	} elseif ($name == 'url') {
		if (isset($_GET['picture'])) {
			$url_image = $wpdb->get_var("SELECT url FROM " . $tableLacoste . " WHERE id_image = '" . $_GET['picture'] . "'");
		} else {
			$url_image = $wpdb->get_var("SELECT url FROM " . $tableLacoste . " WHERE id_post = '" . $_GET['postId'] . "'");
		}
		return $url_image ? $url_image : $default;
	}
}

function actionImageHandler()
{
	tryRedirect();

	if (isset($_POST['file']) && $_POST['file'] != '' && isset($_GET['picture']) && $_GET['picture'] != '') {
		createImage($_POST['file'], $_GET['picture'], $_GET['url']);
	} elseif (isset($_GET['delete']) && $_GET['delete'] != '') {
		deleteImage($_GET['delete']);
	}
}

function tryRedirect()
{
	global $wpdb;
	global $table_prefix;
	$tableLacoste = $table_prefix . 'game_lacoste';

	if (isset($_GET['picture'])) {
		$id_post = $wpdb->get_var("SELECT id_post FROM " . $tableLacoste . " WHERE id_image = '" . $_GET['picture'] . "'");
	} else {
		$id_post = $_GET['postId'];
	}

	if (isset($id_post) && $id_post) {
		$lang = pll_get_post_language($id_post);
	}

	if (isset($_SERVER['HTTP_REFERER'])) {
		$ref = $_SERVER['HTTP_REFERER'];
		if (stripos($ref, "facebook.com") == true) {
			if (isset($lang) && $lang == 'uk') {
				header("Location: https://was.media/uk/2019-12-24-moda-100-rokiv/");
			} else {
				header("Location: https://was.media/2019-12-09-igra-moda-100-let/");
			}
			die();
		}
	}
}

function createImage($data, $name, $url)
{
	global $wpdb;
	global $table_prefix;
	$tableLacoste = $table_prefix . 'game_lacoste';

	try {
		$img = theImageFromB64($data);
		if (!empty($img['file']) && !empty($img['type'])) {
			file_put_contents(ROOTPATH . "wp-content/uploads/static/lacoste-dress-up-6/snippets/{$name}.{$img['type']}", $img['file']);
			if (isset($_GET['postId']) && $_GET['postId'] > 0) {
				$wpdb->insert($tableLacoste, ['id_post' => $_GET['postId'], 'id_image' => $name, 'url' => $url]);
			}
		}
	} catch (Exception $e) {
		die($e);
	}
}

function deleteImage($name)
{
	$files = glob(ROOTPATH . "wp-content/uploads/static/lacoste-dress-up-6/snippets/{$name}.*");
	foreach ($files as $file) {
		unlink($file);
	}
}

function theImageFromB64($data)
{
	$result = [];
	$dataArray = explode(',', $data);
	$file = base64_decode($dataArray[1]);
	if ($data === false) {
		throw new \Exception('base64_decode failed');
	}
	$result['file'] = $file;
	$result['type'] = 'png';
	return $result;
}

?>
