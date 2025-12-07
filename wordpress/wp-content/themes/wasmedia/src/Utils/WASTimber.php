<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 10/2/2017
 * Time: 16:52
 */

namespace Utils;

use Timber\Post;
use Timber\Site;
use Timber\Timber;
use WP_Post;

class WASTimber extends Site
{

    static function initialize()
    {
        return new self();
    }

    static function timber_content_parts_resolver($post_options)
    {
        global $post;

        $content_part = $post_options['content_part'];

        if ($content_part) {
            $view_path = '/views/template-parts/content-parts/was-' . $content_part . '.twig';
            $file = get_template_directory() . $view_path;

            if (file_exists($file) && isset($post_options['post'])) {
                $post = $post_options['post'];

                setup_postdata($post);

                $result = Timber::compile($view_path, $post_options);

                wp_reset_postdata();
            } else {
                $result = "<!-- Template part not found {$file} -->";
            }
        } else {
            $result = "<!-- Unknown layout {$content_part} -->";
        }

        return $result;
    }

    /**
     * @param WP_Post|Post $post
     *
     * @return string
     */
    static function timber_single_templates_resolver($post, $type = null)
    {
        $template = 'was-single.twig';
        $template_type = $type != null ? $type : $post->post_type;

        if ($template_type != 'post') {
            $template_path = '/views/template-parts/single-parts/was-single-' . $template_type . '.twig';
            $file = get_template_directory() . $template_path;

            if (file_exists($file)) {
                $template = $template_path;
            }
        }

        return $template;
    }

    function __construct()
    {
        add_filter('timber_context', [$this, 'add_to_context']);
        add_filter('get_twig', [$this, 'add_to_twig']);

        parent::__construct();
    }

    function add_to_context($context)
    {
        global $wp;
        global $post;

        $replace_data = [];
        $languages_switcher = "";
        $languages_switcher_container = "";

        $current_lang = function_exists('pll_current_language') ? \pll_current_language() : '';
        $lang_switcher_key = 'was_lang_switcher_' . $current_lang;
        $languages_switcher_cached = get_transient($lang_switcher_key);
        if ($languages_switcher_cached === false) {
            foreach (\pll_the_languages(['raw' => true]) as $language) {

                if ($language["slug"] != $current_lang) {
                    $is_current = "";
                    $languages_switcher_container = "" .
                        "<div class=\"lang-link btn btn-xl btn-invert\">" .
                        "<ul class=\"lang-switcher\">%l_switcher</ul>" .
                        "</div>";
                } else {
                    $is_current = "current-language";
                }

                $languages_switcher .= "
                   <li class='lang-item lang-item-{$language["id"]} lang-item-{$language["slug"]} {$is_current}'>
                           <a href=\"{$language["url"]}\">{$language["name"]}</a>
                   </li>
               ";

                $replace_data = ['%l_switcher' => $languages_switcher];
            }
            $languages_switcher_cached = str_replace(array_keys($replace_data), array_values($replace_data), $languages_switcher_container);
            set_transient($lang_switcher_key, $languages_switcher_cached, HOUR_IN_SECONDS);
        }

        $context["referer"] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $css_main_cached = get_transient('was_css_main_v1');
        if ($css_main_cached === false) {
            $css_main_cached = @file_get_contents(get_template_directory() . "/style.css");
            if ($css_main_cached === false) {
                $css_main_cached = '';
            }
            set_transient('was_css_main_v1', $css_main_cached, HOUR_IN_SECONDS * 6);
        }
        $context["css_main"] = $css_main_cached;
        $context["order_count"] = 0;
        $context["amp_brand_url"] = (!is_string_url("/amp/")) ? 'href="' . esc_url(home_url("/")) . '"' : "";
        $context["admin_ajax_url"] = site_url() . "/wp-admin/admin-ajax.php";
        $tag_cloud_key = 'was_tag_cloud_' . $current_lang . '_9';
        $tag_cloud_cached = get_transient($tag_cloud_key);
        if ($tag_cloud_cached === false) {
            $tag_cloud_cached = get_tags_cloud(9);
            set_transient($tag_cloud_key, $tag_cloud_cached, HOUR_IN_SECONDS);
        }
        $context["tag_cloud_tags"] = $tag_cloud_cached;
        $context["languages_switcher"] = $languages_switcher_cached;
        $context["was_share_content_url"] = home_url(add_query_arg([], $wp->request));
        $context["was_share_content_text"] = urlencode(get_bloginfo("title"));

        if ($post) {
            $carousel_posts = [
                'post__not_in' => [$post->ID],
                'post_type' => 'post',
                'posts_per_page' => 9,
                'post_status' => 'publish'
            ];

            $context["carousel_posts"] = Timber::get_posts($carousel_posts);
        }

        if (is_singular()) {
            $context["post_layout_parts"] = $this->get_post_layout_parts();
            $context["was_share_content_text"] = urlencode(get_the_title());

            $context["post_widgets_tools"] = $this->get_tools_layout_parts();
        }

        return $context;
    }

    /**
     * @param \Twig_Environment $twig
     *
     * @return mixed
     */
    function add_to_twig($twig)
    {
        $twig->addGlobal('order_count', 0);
        $twig->addGlobal('not_in', []);

        return $twig;
    }

    public static function get_post_layout_parts()
    {
        global $post;

        $result = [];
        $error = false;

        if ($post && have_rows('admin_post_layout', $post->ID)) {

            while (have_rows('admin_post_layout', $post->ID)) {
                the_row();

                $layout = get_row_layout();

                if ($layout) {
                    $file = get_template_directory() . '/views/post-layout-parts/post_' . $layout . '.twig';

                    if (file_exists($file)) {
                        array_push($result, [$layout => self::get_layout_variables($layout)]);
                    } else {
                        array_push($result, ["none" => "<!-- Template not found {$file} -->"]);
                    }
                } else {
                    $error = "<!-- Unknown layout {$layout} -->";
                }
            }
        } else {
            $error = "<!-- No layouts in post -->";
        }

        return ($error) ? $error : $result;
    }

    public static function get_tools_layout_parts()
    {
        global $post;

        $result = [];
        $error = false;

        if ($post && have_rows('tools_widgets', $post->ID)) {

            while (have_rows('tools_widgets', $post->ID)) {
                the_row();

                $layout = get_row_layout();

                if ($layout) {
                    $file = get_template_directory() . '/views/tools-widgets/post_' . $layout . '.twig';

                    if (file_exists($file)) {
                        array_push($result, [$layout => self::get_tools_layout_variables($layout)]);
                    } else {
                        array_push($result, ["none" => "<!-- Template not found {$file} -->"]);
                    }
                } else {
                    $error = "<!-- Unknown layout {$layout} -->";
                }
            }
        } else {
            $error = "<!-- No layouts in post -->";
        }

        return ($error) ? $error : $result;
    }

    /**
     * @param $layout - Type of template
     *
     * @return array
     */
    public static function get_layout_variables($layout)
    {
        global $post;

        $variables = [];

        switch ($layout) {

            case "add_text":
                $variables = self::get_text_layout_variables();
                break;

            case "add_gallery":
                $variables = self::get_gallery_layout_variables();
                break;

            case "add_game":
                $variables = self::get_game_layout_variables();
                break;

            case "add_elastic":
                $variables = self::get_elastic_layout_variables();
                break;
        }

        return $variables;
    }

    /**
     * @param $layout - Type of template
     *
     * @return array
     */
    public static function get_tools_layout_variables($layout)
    {
        global $post;

        $variables = [];

        switch ($layout) {

            case "accordion_tool":

                $variables = self::get_tools_widgets_layout_variables();
                break;
        }

        return $variables;
    }

    public static function get_text_layout_variables()
    {
        global $post;

        $main_aside = get_sub_field('main_aside');
        $aside_align = get_sub_field('aside_align');
        $aside_options = get_sub_field('aside_options');
        $text_options = get_sub_field('main_text_options');
        $text_custom_theme_group = get_sub_field('text_custom_theme_group');

        $main_tooltips = [];
        $main_aside_chronology = [];

        $chooser_icons = "";
        $chooser_icons_alt = "";
        $text_custom_style = "";

        $aside_class = "editor_aside_offset editor_aside";

        if ($aside_options == 'incut') {
            $aside_class .= ' incut_aside';

        } elseif ($aside_options == 'icons') {
            $aside_class .= ' icons_aside';

        } elseif ($aside_options == 'chronology') {
            $aside_class .= ' chronology_aside';
        }

        if (get_sub_field('chooser_icons') == 'bookmark') {
            $chooser_icons = twentyseventeen_get_svg(['icon' => 'bookmark', "class" => "icon-xl icon-brand"]);

        } elseif (get_sub_field('chooser_icons') == 'blockquote') {
            $chooser_icons = twentyseventeen_get_svg(['icon' => 'quote-right', "class" => "icon-lg icon-brand"]);

        } elseif (get_sub_field('chooser_icons') == 'underline') {
            $chooser_icons_alt = twentyseventeen_get_svg(['icon' => 'underline', "class" => "icon-brand", "style" => "max-width:150px; width: 100%; height:26px"]);
        }

        if ($aside_align && in_array('left', (array)$aside_align)) {
            $aside_class = $aside_class . ' aside_left ';
        }

        $text_editor_class = "editor_txt";
        $text_editor_class .= ($text_options && in_array('darken', (array)$text_options)) ?
            " darken-theme" :
            "";

        if ($text_options && in_array('custom', (array)$text_options)) {
            $text_editor_class .= ' custom-theme';

            $text_custom_style .= 'color:' . $text_custom_theme_group['text_custom_color'] . ';';

            $text_custom_style .= 'background-color:' . $text_custom_theme_group['text_custom_bg'] . ';';
        }

        $text_layout_class = "layout_main";
        $text_layout_class .= ($main_aside || $aside_options == 'chronology') ? " layout_aside" : "";

        if (have_rows('main_tooltips', $post->ID)) {

            while (have_rows('main_tooltips', $post->ID)) {
                the_row();

                $main_tooltips[] = get_sub_field('tooltip_item');
            }
        }

        if (have_rows('main_aside_chronology', $post->ID)) {

            while (have_rows('main_aside_chronology', $post->ID)) {
                the_row();

                $main_aside_chronology[] = get_sub_field('chronology_content');
            }
        }

        return [
            "text_editor_class" => $text_editor_class,
            "text_custom_style" => $text_custom_style,
            "text_layout_class" => $text_layout_class,
            "aside_options" => $aside_options,
            "aside_class" => $aside_class,
            "chooser_icons" => $chooser_icons,
            "chooser_icons_alt" => $chooser_icons_alt,
            "main_aside" => $main_aside,
            "main_tooltips" => $main_tooltips,
            "main_text" => get_sub_field("main_text"),
            "main_aside_chronology" => $main_aside_chronology
        ];
    }

    public static function get_gallery_layout_variables()
    {
        global $post;

        $gallery = get_sub_field('main_gallery');
        $options = get_sub_field('gallery_options');
        $media_custom_theme_group = get_sub_field('media_custom_theme_group');

        $is_slider = false;
        $is_full_size = false;
        $media_editor_class = " editor_media ";

        $gallery_item_class = " figure ";
        $i_frame_format = " i_video ";
        $full_size_class = " editor_offset editor_";
        $slider_class = " swiper-container swiper-default ";

        $main_i_frame = [];

        if ($options && in_array('fullsize', (array)$options)) {
            $is_full_size = true;
            $gallery_item_class = " fullsize-img ";
            $full_size_class .= 'img-lg';
            $type_image_size = 'full';
            $type_caption_size = 'medium';
            $type_caption_class = 'fullsize-center';
            $slider_class .= " fullsize-slider ";
        } else {

            if ($options && in_array('img-lg', (array)$options)) {
                $full_size_class .= 'img-lg';
                $type_image_size = 'large';
                $type_caption_size = 'large';
                $type_caption_class = 'figcaption';
            } else if ($options && in_array('img-xl', (array)$options)) {
                $full_size_class = 'editor_img-xl';
                $type_image_size = 'x-large';
                $type_caption_size = 'x-large';
                $type_caption_class = 'figcaption';
                $media_editor_class .= 'editor_media__xl';
            } else {
                $full_size_class .= 'default';
                $type_image_size = 'medium';
                $type_caption_size = 'medium';
                $type_caption_class = 'figcaption';
            }
        }

        if ($options && in_array('slider', (array)$options)) {
            $is_slider = true;
            $gallery_item_class = " swiper-slide ";
        }

        if (get_sub_field('config_iframe') == 'square') {
            $i_frame_format = " i_square ";

        } elseif (get_sub_field('config_iframe') == '4/3') {
            $i_frame_format = " i_43 ";

        } elseif (get_sub_field('config_iframe') == 'wide') {
            $i_frame_format = " i_wide ";

        } elseif (get_sub_field('config_iframe') == 'swide') {
            $i_frame_format = " i_swide";
        }

        $media_editor_class .= ($options && in_array('darken', (array)$options)) ? " darken-theme " : "";
        $media_custom_style = "";

        if ($options && in_array('compare', (array)$options)) {
            if ($options && in_array('fullsize', (array)$options)) {
                $media_editor_class .= " juxtapose compare-item ";
            } else {
                $full_size_class .= " juxtapose compare-item ";
            }
        }

        if ($options && in_array('custom', (array)$options)) {

            $media_custom_style .= 'color:' . $media_custom_theme_group['media_custom_color'] . ';';

            $media_custom_style .= 'background-color:' . $media_custom_theme_group['media_custom_bg'] . ';';
        }

        $i_frame_class = " iframeWrapper ";
        $i_frame_class .= $i_frame_format;
        $i_frame_class .= $gallery_item_class;

        if (have_rows('main_iframe', $post->ID)) {

            while (have_rows('main_iframe', $post->ID)) {
                the_row();

                $main_i_frame[] = process_url_for_embeding(get_sub_field('iframe_src'));
            }
        }

        return [
            "media_editor_class" => $media_editor_class,
            "media_custom_style" => $media_custom_style,
            "is_full_size" => $is_full_size,
            "full_size_class" => $full_size_class,
            "main_i_frame" => $main_i_frame,
            "i_frame_class" => $i_frame_class,
            "is_slider" => $is_slider,
            "gallery" => $gallery,
            "gallery_item_class" => $gallery_item_class,
            "type_image_size" => $type_image_size,
            "slider_class" => $slider_class,
            "type_caption_size" => $type_caption_size,
            "type_caption_class" => $type_caption_class
        ];
    }

    public static function get_game_layout_variables()
    {
        global $post;

        $options = get_sub_field('game_options');
        $game_type_prefix = get_sub_field('game_type_prefix');
        $game_label = get_sub_field('game_label');
        $cover_buttons_shape_value = get_sub_field_object('cover_buttons_shape')['value'];
        $cover_buttons_color_value = get_sub_field_object('cover_buttons_color')['value'];
        $cover_buttons_custom_color = get_sub_field('custom_cover_buttons_color');
        $intro_button_text = get_sub_field('game_intro_button_text');
        $intro_cover = get_sub_field('game_intro_cover');

        $button_class = get_sub_field_object('question_theme_buttons')['value'];
        $outro_cover = get_sub_field('game_outro_cover');
        $outro_cover_color = $outro_cover['game_outro_color'];
        $outro_cover_bg = $outro_cover['game_outro_background'];

        $question_cover = get_sub_field('game_question_cover');
        $question_cover_image = $question_cover['game_question_image'];
        $question_cover_bg_color = $question_cover['game_question_bg_color'];
        $question_cover_text_color = $question_cover['game_question_text_color'];

        $outro_btn_text = get_sub_field('game_outro_button_text');
        $get_lottery = get_sub_field('game_lottery');

        $images_size = "main-cover";
        $game_type = "quiz";

        $game_items = [];
        $game_results = [];
        $game_theme_class = [];
        $game_controllers = [];

        $game_editor_class = "";
        $cover_buttons_custom_style = "";
        $intro_cover_src = "";
        $intro_cover_src_set = "";
        $intro_cover_width = "";
        $intro_cover_height = "";
        $question_cover_src = "";

        $is_question_img_background = false;
        $is_categories = false;
        $is_intro_cover = false;
        $is_after_click = false;
        $is_lottery = false;

        if ($options) {

            foreach ($options as $option) {
                array_push($game_controllers, $option);
            }
        }

        if ($is_game_ready = have_rows('game_items', $post->ID) || in_array('custom', $game_controllers)) {

            $button_class .= (in_array('btnlist', $game_controllers)) ? " btn-link" : " btn-block";
            $cover_buttons_custom_style = $cover_buttons_custom_color ? 'style="background-color:' . $cover_buttons_custom_color . ';"' : "";
            $game_type = in_array('custom', $game_controllers) ? $game_type_prefix : $game_type;
            $is_score = in_array('scores', $game_controllers);
            $is_after_click = in_array('notesafteranswer', $game_controllers);
            $is_question_img_background = in_array('questionimgbg', $game_controllers);
            $is_intro_cover = !empty($intro_cover);
            $is_categories = in_array('categories', $game_controllers);
            $is_lottery = in_array('lottery', $game_controllers);

            if ($is_intro_cover) {
                array_push($game_theme_class, "introCover");
            }

            if ($question_cover_image) {
                array_push($game_theme_class, "gameItemImgBg");

                $question_cover_src = custom_image_getter(
                    $question_cover_image['ID'],
                    [
                        "i_thumb_name" => $images_size
                    ]
                );
            }

            if ($is_question_img_background) {
                array_push($game_theme_class, "gameItemImgBg");
            }

            while (have_rows('game_items', $post->ID)) {
                the_row();

                $question_title = get_sub_field('item_question_title');
                $question_text = get_sub_field('item_question_text');
                $media = get_sub_field('item_question_media');
                $notes = get_sub_field('items_notes');

                $question_media_options = $media['item_question_options'];
                $question_media_gallery = $media['item_question_gallery'];
                $question_media_path = $media['item_question_path'];
                $note_txt = $notes['item_question_note'];

                $gameItemClass = $question_media_options && in_array('row', (array)$question_media_options) ? "itemRow" : "itemCol";
                $question_is_druggable = $question_media_options && in_array('druggable', (array)$question_media_options);

                $version_class = "";
                $version_class = $question_is_druggable == true ? " versions-druggable " : "";

                $note_img = $notes['item_question_note_image'] ?
                    custom_image_getter(
                        $notes['item_question_note_image']['ID'],
                        [
                            "i_thumb_name" => $images_size
                        ]
                    ) :
                    "";

                $item_versions = [];
                $question_gallery = [];

                if (!empty($question_media_gallery)) {

                    foreach ($question_media_gallery as $image) {
                        array_push($question_gallery, [
                            'src' => custom_image_getter(
                                $image['ID'],
                                [
                                    "i_thumb_name" => "medium",
                                    "i_sizes" => [
                                        565, null
                                    ]
                                ]
                            ),
                            'alt' => $image['alt']
                        ]);
                    }

                    $question_cover_src = $is_question_img_background ? $question_gallery[0]['src'] : $question_cover_src;
                }

                if (have_rows('items_versions', $post->ID)) {

                    while (have_rows('items_versions', $post->ID)) {
                        the_row();

                        $score = get_sub_field('version_score');
                        $is_true = get_sub_field('version_is_true');
                        $content = get_sub_field('version_text');
                        $value = $is_score || $question_is_druggable ? $score : $is_true;

                        array_push(
                            $item_versions,
                            [
                                "content" => $content,
                                "button_hash" => getGameHash($value, $is_score || $question_is_druggable)
                            ]
                        );
                    }
                }

                array_push(
                    $game_items,
                    [
                        "question_cover_bg_color" => $question_cover_bg_color,
                        "question_cover_text_color" => $question_cover_text_color,
                        "question_cover_src" => $question_cover_src,
                        "question_title" => $question_title,
                        "question_text" => $question_text,
                        "version_class" => $version_class,
                        "question_item_class" => $gameItemClass,
                        "question_gallery" => $question_gallery,
                        "question_note_txt" => $note_txt,
                        "question_note_img" => $note_img,
                        "item_versions" => $item_versions,
                        "question_media_path" => $question_media_path,
                        "is_gif" => in_array("gif", (array)$question_media_options),
                        "is_audio" => in_array("audio", (array)$question_media_options),
                        "is_iframe" => in_array("iframe", (array)$question_media_options),
                        "is_image" => in_array("image", (array)$question_media_options)
                    ]
                );
            }

            $game_editor_class = "gameContainer initGame";
            $game_editor_class .= " " . implode(" ", $game_theme_class);

            if (is_array($intro_cover)) {
                $intro_cover_src_set = custom_src_set_getter($intro_cover['ID'], ["i_thumb_name" => $images_size]);
            }

            $intro_cover_width = $intro_cover['sizes'][$images_size . '-width'];
            $intro_cover_height = $intro_cover['sizes'][$images_size . '-height'];

            $intro_cover_src = custom_image_getter(
                $intro_cover['ID'],
                [
                    "i_thumb_name" => $images_size
                ]
            );

            if (have_rows('game_results')) {

                while (have_rows('game_results')) {
                    the_row();

                    $in_lottery = get_sub_field('results_in_lottery');
                    $game_result_number = get_sub_field('results_number');
                    $game_result_quantity = get_sub_field('results_quantity');
                    $result_image = get_sub_field('results_image');
                    $game_result_text = get_sub_field('results_text');
                    $game_result_snippet = custom_image_getter(
                        $result_image['ID'],
                        [
                            "i_thumb_name" => $result_image['sizes']['og-image']
                        ]
                    );

                    array_push(
                        $game_results,
                        [
                            "game_result_number" => $game_result_number,
                            "game_result_text" => $game_result_text,
                            "game_result_snippet" => $game_result_snippet,
                            "game_result_quantity" => $game_result_quantity,
                            "in_lottery" => $in_lottery
                        ]
                    );
                }
            }
        }

        return [
            "is_game_ready" => $is_game_ready,
            "game_editor_class" => $game_editor_class,
            "game_data_type" => $game_type,
            "game_data_options" => implode(",", $game_controllers),
            "cover_buttons_shape_value" => $cover_buttons_shape_value,
            "cover_buttons_color_value" => $cover_buttons_color_value,
            "cover_buttons_custom_style" => $cover_buttons_custom_style,
            "intro_button_text" => $intro_button_text,
            "is_categories" => $is_categories,
            "is_question_img_background" => $is_question_img_background,
            "is_intro_cover" => $is_intro_cover,
            "intro_img" => [
                "width" => $intro_cover_width,
                "height" => $intro_cover_height,
                "src" => $intro_cover_src,
                "srcset" => $intro_cover_src_set,
                "alt" => $intro_cover['alt']
            ],
            "game_label" => $game_label,
            "game_items" => $game_items,
            "button_class" => $button_class,
            "is_after_click" => $is_after_click,
            "is_lottery" => $is_lottery,
            "get_lottery" => $get_lottery,
            "outro_cover_color" => $outro_cover_color,
            "outro_cover_bg" => $outro_cover_bg,
            "outro_btn_text" => $outro_btn_text,
            "game_results" => $game_results
        ];
    }

    public static function get_elastic_layout_variables()
    {
        $elastic_editor_class = "editor_elastic";
        $elastic_layout_class = "editor_offset editor_";
        $elastic_type = get_sub_field("elastic_type");

        $options = get_sub_field('elastic_options');

        $slider_class = " swiper-container swiper-elastic ";
        $is_slider = false;

        $podcasts = [];
        $posts = [];

        switch ($elastic_type) {

            case "podcast":
                $elastic_layout_class .= 'default';
                $podcast_posts = get_sub_field("elastic_materials");

                if ($podcast_posts) {

                    foreach ($podcast_posts as $podcast_post) {
                        $podcasts[] = get_field("sound_link", $podcast_post->ID);
                    }
                }

                break;

            case "post":

                if ($options && in_array('slider', (array)$options)) {
                    $is_slider = true;
                }

                $elastic_layout_class .= 'img-lg';
                $posts = (array)get_sub_field("elastic_materials");

                break;
        }

        return [
            "elastic_type" => $elastic_type,
            "elastic_editor_class" => $elastic_editor_class,
            "elastic_layout_class" => $elastic_layout_class,
            "podcasts" => $podcasts,
            "slider_class" => $slider_class,
            "is_slider" => $is_slider,
            "posts" => $posts
        ];
    }

    public static function get_tools_widgets_layout_variables()
    {
        global $post;

        $accardion_items = [];
        $tools_class = "editor-tools";
        $tools_layout_class = "layout_main";


        if (have_rows('accordion_item', $post->ID)) {

            while (have_rows('accordion_item', $post->ID)) {
                the_row();

                $header = get_sub_field('accordion_item_header');
                $content = get_sub_field('accordion_item_content');

                array_push($accardion_items, [
                    "accordion_item_header" => $header,
                    "accordion_item_content" => $content
                ]);
            }
        }

        return [
            "tools_class" => $tools_class,
            "tools_layout_class" => $tools_layout_class,
            "accardion_items" => $accardion_items
        ];
    }
}