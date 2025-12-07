<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/wp-content/themes/wasmedia/vendor/autoload.php';

use Utils\SendGrid\Content;
use Utils\SendGrid\Email;
use Utils\SendGrid\Mail;
use Utils\SendGrid;

$lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'EMAIL', FILTER_VALIDATE_EMAIL);

if ($lang) {

    switch ($lang) {

        case "ru":
            $mail_tpl = "d0791307-afee-4627-98de-fc86c882c59e";
            $subject = "Подтверждение подписки на рассылку";
            break;

        case "uk":
            $mail_tpl = "0f18c70b-eb13-4ead-8bd8-5bbd54bcdbde";
            $subject = "Підтвердження підписки на розсилку";
            break;

        default:
            header('Location: https://was.media/', true, 301);
            die;
    }
} else {
    header('Location: https://was.media', true, 301);
    die;
}

if ($email) {
    $hash = md5(uniqid(rand(), true));

    require_once(__DIR__ . "/../wp-config.php");

    global $wp;

    $wp->init();
    $wp->register_globals();
    $wp->send_headers();

    global $wpdb;

    $table = "B_Subscription";
    $sql = "INSERT INTO $table (email,hash,lang) VALUES (%s,%s,%s) ON DUPLICATE KEY UPDATE hash = %s";
    $sql = $wpdb->prepare($sql, $email, $hash, $lang, $hash);

    $wpdb->query($sql);

    $from = new Email("WAS", "team@was.media");
    $to = new Email($email, $email);
    $content = new Content("text/html", $hash);
    $mail = new Mail($from, $subject, $to, $content);

    $mail->setTemplateId($mail_tpl);

    $sg = new SendGrid('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');
    $response = $sg->client->mail()->send()->post($mail);

    setcookie("subs_status", "success", time() + 3600, "/");

    wp_redirect(home_url() . "/" . pll_current_language() . "/newsletter-subscribe", 303);
} else {
    wp_redirect(home_url() . "/" . pll_current_language(), 303);
}