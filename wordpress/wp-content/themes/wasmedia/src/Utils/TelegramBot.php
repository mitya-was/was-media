<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 1/12/2018
 * Time: 18:57
 */

namespace Utils;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class TelegramBot {

    private const BOT_USERNAME = 'wasmedia_bot';

    private $mysql_credentials = [];

    private $admins = [
        540348737,
        210509277,
        112383284,
        260200241
    ];

    private $commands_paths = [
        __DIR__ . "/Commands/"
    ];

    public static function setWebHook() {
        $bot_api_key = getenv('TELEGRAM_BOT_KEY') ?: '';
        $bot_username = getenv('TELEGRAM_BOT_USERNAME') ?: self::BOT_USERNAME;
        $hook_url = getenv('TELEGRAM_BOT_HOOK') ?: 'https://was.media/was-bot-endpoint';

        try {
            $telegram = new Telegram($bot_api_key, $bot_username);
            $result = $telegram->setWebhook($hook_url);

            if ($result->isOk()) {
                echo $result->getDescription();
            }

        } catch (TelegramException $e) {
            echo $e->getMessage();
        }
    }

    public static function unSetWebHook() {
        $bot_api_key = getenv('TELEGRAM_BOT_KEY') ?: '';
        $bot_username = getenv('TELEGRAM_BOT_USERNAME') ?: self::BOT_USERNAME;

        try {
            $telegram = new Telegram($bot_api_key, $bot_username);
            $result = $telegram->deleteWebhook();

            if ($result->isOk()) {
                echo $result->getDescription();
            }

        } catch (TelegramException $e) {
            echo $e->getMessage();
        }
    }

    public static function init() {
        return new self();
    }

    public function __construct() {

        $bot_api_key = getenv('TELEGRAM_BOT_KEY') ?: '';
        $bot_username = getenv('TELEGRAM_BOT_USERNAME') ?: self::BOT_USERNAME;

        $this->mysql_credentials = [
            'host'     => getenv('TELEGRAM_DB_HOST') ?: 'localhost',
            'port'     => getenv('TELEGRAM_DB_PORT') ?: 3306,
            'user'     => getenv('TELEGRAM_DB_USER') ?: 'was',
            'password' => getenv('TELEGRAM_DB_PASSWORD') ?: '',
            'database' => getenv('TELEGRAM_DB_NAME') ?: 'was'
        ];

        try {
            $telegram = new Telegram($bot_api_key, $bot_username);

            $telegram->enableMySql($this->mysql_credentials, "was_bot_");
            $telegram->setDownloadPath(wp_get_upload_dir()["basedir"] . "/was-bot/download");
            $telegram->setUploadPath(wp_get_upload_dir()["basedir"] . "/was-bot/upload");
            $telegram->addCommandsPaths($this->commands_paths);
            $telegram->enableAdmins($this->admins);

            $telegram->setCommandConfig(
                'sendtochannel',
                [
                    'your_channel' => [
                        '@WAS',
                        '@WAS Ua'
                    ]
                ]
            );

            $telegram->enableLimiter();

            $telegram->handle();
        } catch (TelegramException $e) {
            //echo $e->getMessage();
        }
    }
}