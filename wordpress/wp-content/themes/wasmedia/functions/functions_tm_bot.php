<?php

/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 1/12/2018
 * Time: 19:06
 */

use Utils\TelegramBot;

if (isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"] === "/was-bot-endpoint") {
    TelegramBot::init();
    die();
}