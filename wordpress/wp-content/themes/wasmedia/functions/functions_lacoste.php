<?php

use Utils\LacosteConnector;

function collect_lacoste_users() {
	$lacosteData = get_object_vars(json_decode(file_get_contents("php://input")));

    if (isset($lacosteData) && $lacosteData) {
        new LacosteConnector($lacosteData);
    }
}

add_action('wp_ajax_collect_lacoste_users', 'collect_lacoste_users');
add_action('wp_ajax_nopriv_collect_lacoste_users', 'collect_lacoste_users');

function add_allowed_lacoste_origins($origins) {
    $origins = [];
//	$origins[] = 'https://www.winter-punch.birdinflight.com/';
//	$origins[] = 'https://www.winter-punch.birdinflight.com';
//	$origins[] = 'https://winter-punch.birdinflight.com/';
//	$origins[] = 'https://winter-punch.birdinflight.com';
//	$origins[] = 'http://www.winter-punch.birdinflight.com/';
//	$origins[] = 'http://www.winter-punch.birdinflight.com';
//	$origins[] = 'http://winter-punch.birdinflight.com/';
//	$origins[] = 'http://winter-punch.birdinflight.com';

    return $origins;
}

//add_filter('allowed_http_origins', 'add_allowed_lacoste_origins');