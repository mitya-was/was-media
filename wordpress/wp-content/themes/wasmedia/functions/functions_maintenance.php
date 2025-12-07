<?php
$url = $_SERVER['REQUEST_URI'];

if (IS_DEV && !is_user_logged_in() && !preg_match("/(\bwaslogin\b)/s", $url) && file_exists(WP_CONTENT_DIR . '/maintenance.php')) {
    require_once(WP_CONTENT_DIR . '/maintenance.php');

    die();
}