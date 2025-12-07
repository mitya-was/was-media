<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/11/2017
 * Time: 17:01
 */

use Utils\ElectionsSheetsConnector;
use Utils\GoogleSheetsConnector;

function collect_lottery_users() {
    $lotteryData = json_decode(file_get_contents("php://input"));

    if ($lotteryData) {
        new GoogleSheetsConnector($lotteryData);
    }
}

add_action('wp_ajax_collect_lottery_users', 'collect_lottery_users');
add_action('wp_ajax_nopriv_collect_lottery_users', 'collect_lottery_users');

function collect_elections_users() {
    $electionsData = json_decode(file_get_contents("php://input"));

    if ($electionsData) {
        new ElectionsSheetsConnector($electionsData);
    }
}

add_action('wp_ajax_collect_elections_users', 'collect_elections_users');
add_action('wp_ajax_nopriv_collect_elections_users', 'collect_elections_users');