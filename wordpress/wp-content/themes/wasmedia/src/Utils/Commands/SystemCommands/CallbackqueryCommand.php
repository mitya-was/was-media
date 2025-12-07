<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlineKeyboardCommand.php
 */
class CallbackQueryCommand extends SystemCommand {
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute() {
        $result = [];
        $callback_query = $this->getCallbackQuery();
        $callback_data = $callback_query->getData();
        $chat_id = $callback_query->getMessage()->getChat()->getId();

        switch ($callback_data) {

            case 'menu':
                $result = $this->get_main_menu();
                break;

            case 'new':
                $result = $this->get_new_articles();
                break;

            case 'category':
                $result = $this->get_categories();
                break;

            case 'microformat':
                $result = $this->get_microformats();
                break;

            case 'game':
                $result = $this->get_random_game();
                break;

            case 'random':
                $result = $this->get_random_article();
                break;

            case ((json_decode($callback_data)) instanceof \stdClass):
                $result = $this->process_callback($callback_data);
                break;

            default:
                break;
        }

        $data = [
            'chat_id'    => $chat_id,
            'cache_time' => 5
        ];

        $data = array_replace($data, $result);

        return Request::sendMessage($data);
    }

    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function get_main_menu() {
        $inline_keyboard = new InlineKeyboard();

        $inline_keyboard->addRow(['text' => 'Новое', 'callback_data' => 'new']);
        $inline_keyboard->addRow(['text' => 'Категория', 'callback_data' => 'category']);
        $inline_keyboard->addRow(['text' => 'Микроформат', 'callback_data' => 'microformat']);
        $inline_keyboard->addRow(['text' => 'Случайная игра', 'callback_data' => 'game']);
        $inline_keyboard->addRow(['text' => 'Случайная статья', 'callback_data' => 'random']);

        return ['text' => 'Главное меню:', 'reply_markup' => $inline_keyboard];
    }

    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function get_new_articles() {
        $inline_keyboard = new InlineKeyboard();
        $new_posts = get_posts([
            'lang'           => 'ru',
            'posts_per_page' => 1,
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'orderby'        => 'rand',
            'date_query'     => [
                'after' => date('Y-m-d', strtotime('-10 days'))
            ]
        ]);

        if (count($new_posts) > 0) {
            $text = get_permalink($new_posts[0]);

            $inline_keyboard->addRow(
                ['text' => 'Ещё нового!', 'callback_data' => 'new'],
                ['text' => 'Меню', 'callback_data' => 'menu']
            );
        } else {
            $text = "Извините, но за эту неделю мы не выпустили новых статей :(";
        }

        return ['text' => $text, 'reply_markup' => $inline_keyboard];
    }

    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function get_categories() {
        $inline_keyboard = new InlineKeyboard();
        $tags = get_tags_cloud(9);

        foreach ($tags as $tag) {
            $tagObject = get_term_by('name', mb_ucfirst(strip_tags($tag)), 'post_tag');

            if ($tagObject) {
                $inline_keyboard->addRow(
                    [
                        'text'          => mb_ucfirst(strip_tags($tag)),
                        'callback_data' => json_encode(['slug' => $tagObject->slug])
                    ]
                );
            }
        }

        return ['text' => 'Выберите категорию:', 'reply_markup' => $inline_keyboard];
    }

    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function get_microformats() {
        $random_post = get_posts([
            'lang'           => 'ru',
            'posts_per_page' => 1,
            'post_type'      => 'microformats',
            'post_status'    => 'publish',
            'orderby'        => 'rand'
        ]);
        $inline_keyboard = new InlineKeyboard();

        $inline_keyboard->addRow(
            ['text' => 'Ещё историю!', 'callback_data' => 'microformat'],
            ['text' => 'Меню', 'callback_data' => 'menu']
        );

        return ['text' => get_permalink($random_post[0]), 'reply_markup' => $inline_keyboard];
    }

    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function get_random_game() {
        $random_post = get_posts([
            'lang'           => 'ru',
            'posts_per_page' => 1,
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'category_name'  => ['games'],
            'orderby'        => 'rand'
        ]);
        $inline_keyboard = new InlineKeyboard();

        $inline_keyboard->addRow(
            ['text' => 'Ещё игр!', 'callback_data' => 'game'],
            ['text' => 'Меню', 'callback_data' => 'menu']
        );

        return ['text' => get_permalink($random_post[0]), 'reply_markup' => $inline_keyboard];
    }

    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function get_random_article() {
        $random_post = get_posts([
            'lang'             => 'ru',
            'posts_per_page'   => 1,
            'post_type'        => 'post',
            'post_status'      => 'publish',
            'category__not_in' => ['games'],
            'orderby'          => 'rand'
        ]);
        $inline_keyboard = new InlineKeyboard();

        $inline_keyboard->addRow(
            ['text' => 'Ещё!', 'callback_data' => 'random'],
            ['text' => 'Меню', 'callback_data' => 'menu']
        );

        return ['text' => get_permalink($random_post[0]), 'reply_markup' => $inline_keyboard];
    }

    /**
     * @param $json
     *
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function process_callback($json) {
        $data = json_decode($json);

        if (isset($data->slug)) {
            $random_post = get_posts([
                'lang'           => 'ru',
                'posts_per_page' => 1,
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'tag'            => $data->slug,
                'orderby'        => 'rand'
            ]);

            $inline_keyboard = new InlineKeyboard();

            $inline_keyboard->addRow(
                ['text' => 'Далее!', 'callback_data' => $json],
                ['text' => 'Меню', 'callback_data' => 'menu']
            );

            return ['text' => get_permalink($random_post[0]), 'reply_markup' => $inline_keyboard];
        }

        return [];
    }
}
