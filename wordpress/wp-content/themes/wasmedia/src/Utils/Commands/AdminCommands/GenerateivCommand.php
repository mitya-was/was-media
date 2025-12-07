<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 10/24/18
 * Time: 14:12
 */

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Request;

/**
 * Admin "/generateiv" command
 */
class GenerateivCommand extends AdminCommand {
    /**
     * @var string
     */
    protected $name = 'generateiv';

    /**
     * @var string
     */
    protected $description = 'Generate Instant View For WAS Links';

    /**
     * @var string
     */
    protected $usage = '/generateiv <link>';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $need_mysql = false;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute() {
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));
        $data = ['chat_id' => $chat_id];

        if ($text === '') {
            $text = 'Provide the link to convert: /generateiv <link>';
        } else {

            if (filter_var($text, FILTER_VALIDATE_URL)) {
                $text = "https://t.me/iv?url=" . urlencode($text) . "&rhash=03182467f76764";
            } else {
                $text = 'Provided text is not a valid URL';
            }
        }

        $data['text'] = $text;

        return Request::sendMessage($data);
    }
}
