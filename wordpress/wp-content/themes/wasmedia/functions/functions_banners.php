<?php

use Timber\Timber;

/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 9/25/2017
 * Time: 15:12
 */

function cptui_register_my_cpts_banners() {

    /**
     * Post Type: Banners.
     */

    $labels = [
        'name'               => __('Banners'),
        'singular_name'      => __('Banner'),
        'add_new'            => __('Add Banner'),
        'add_new_item'       => __('Add New Banner'),
        'edit_item'          => __('Edit Banner'),
        'new_item'           => __('New Banner'),
        'view_item'          => __('View Banner'),
        'search_items'       => __('Search Banners'),
        'not_found'          => __('No Banners Found'),
        'not_found_in_trash' => __('No Banners found in Trash'),
    ];

    $args = [
        'labels'              => $labels,
        'has_archive'         => false,
        'rewrite'             => true,
        'public'              => true,
        'exclude_from_search' => false,
        'hierarchical'        => false,
        'supports'            => [
            'title',
            'excerpt',
            'thumbnail',
            'page-attributes'
        ],
        'menu_icon'           => 'dashicons-images-alt2'
    ];

    register_post_type('banner', $args);
}

add_action('init', 'cptui_register_my_cpts_banners');

function get_external_banners($position) {
    wp_reset_postdata();

    global $post;

    $current_id = is_single($post->ID) ? $post->ID : 0;

    $args = [
        'post_type'      => 'banner',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'cache_results'  => false
    ];

    $query_banners = new WP_Query($args);

    $banner_position = [];
    $banners_for_posts = [];
    $banners_for_main = [];

    while ($query_banners->have_posts()) {
        $query_banners->the_post();

        //Posts relation
        $rel = get_field("banner_posts");
        $rel = ($rel && !is_array($rel)) ? [$rel] : $rel;

        //Filters
        $options = get_field("banner_filter_options");
        $exclude = checkBannerOption("exclude_from", $options);
        $include_categories = checkBannerOption("include_categories", $options);
        $include_tags = checkBannerOption("include_tags", $options);

        //Positioning
        if (have_rows('banner_positioning')) {

            while (have_rows('banner_positioning')) {
                the_row();

                $banner_position["home"] = (array) get_sub_field("home_banner_position");
                $banner_position["posts"] = array_filter(
                    array_merge(
                        (array) get_sub_field("news_banner_position"),
                        (array) get_sub_field("features_banner_position")
                    ),
                    function ($value) {
                        return $value !== "" ? $value : null;
                    }
                );
            }
        }

        //Banners for front page
        if (in_array_r($position, $banner_position)) {
            array_push($banners_for_main, $post);
        }

        //Banners for posts
        if (is_array($rel) && count($rel) > 0 && !empty($banner_position["posts"])) {

            if ($exclude && !in_array($current_id, $rel) && in_array_r($position, $banner_position)) {
                array_push($banners_for_posts, $post);
            }

            if (!$exclude && in_array($current_id, $rel) && in_array_r($position, $banner_position)) {
                array_push($banners_for_posts, $post);
            }
        }

        //Banners for categories
        if (
            $include_categories &&
            array_intersect((array) get_field("banner_categories"), wp_get_post_categories($current_id)) &&
            !in_array($post, $banners_for_posts) &&
            in_array_r($position, $banner_position) &&
            checkIsNotExcluded($exclude, $rel, $current_id)
        ) {
            array_push($banners_for_posts, $post);
        }

        //Banners for tags
        if (
            $include_tags &&
            array_intersect((array) get_field("banner_tags"), wp_get_post_tags($current_id, ['fields' => 'ids'])) &&
            !in_array($post, $banners_for_posts) &&
            in_array_r($position, $banner_position) &&
            checkIsNotExcluded($exclude, $rel, $current_id)
        ) {
            array_push($banners_for_posts, $post);
        }
    }

    if (count($banners_for_posts) > 0 && !is_front_page()) {
        generateRandomBanner($position, $banners_for_posts);
    }

    if (count($banners_for_main) > 0 && is_front_page()) {
        generateRandomBanner($position, $banners_for_main);
    }

    wp_reset_query();
}

/**
 * @param string $optionToCheck
 * @param array  $options
 *
 * @return bool
 */
function checkBannerOption($optionToCheck, $options) {
    return ($options && is_array($options) && in_array($optionToCheck, $options));
}

function checkIsNotExcluded($is_exclude_on, $exclude_list, $post_id) {
    return !($is_exclude_on && in_array($post_id, $exclude_list));
}

function generateRandomBanner($position, $bannersArray) {
    global $post;
    global $banners;

    $banners = [];
    $banners["banners"] = [];
    $banners["position"] = $position;

    foreach ($bannersArray as $value) {
        $post = $value;

        setup_postdata($post);

        if (have_rows('banner_rotation')) {

            while (have_rows('banner_rotation')) {
                the_row();

                $layout = get_row_layout();

                $posts_rotation = get_sub_field("posts_for_banner");
                $posts_rotation_count = count((array) $posts_rotation);

                $collapse_state_admin = get_field("allow_folding");
                $collapse_state = (isset($_COOKIE["jewelry-$position"])) ? $_COOKIE["jewelry-$position"] :
                    $collapse_state_admin;
                $collapse_state = ($collapse_state && $collapse_state_admin !== "") ? "b-collapse-$collapse_state" :
                    "b-collapse-off";

                if ($layout == "add_post" && is_array($posts_rotation) && $posts_rotation_count > 0) {

                    for ($i = 0; $i < $posts_rotation_count; $i++) {
                        $posts_rotation[$i]->banner_script = get_sub_field("banner_script");
                        $posts_rotation[$i]->banner_b_g_color = get_sub_field("banner_b_g_color");
                        $posts_rotation[$i]->banner_title_color = get_sub_field("banner_title_color");
                        $posts_rotation[$i]->banner_folded_title = get_sub_field("banner_folded_title");
                        $posts_rotation[$i]->banner_fold_button_title = get_sub_field("banner_fold_button_title");
                        $posts_rotation[$i]->banner_collapse_class = $collapse_state;
                    }

                    $banners["banners"] = array_merge($banners["banners"], array_values($posts_rotation));
                }

                if ($layout == "add_banner") {
                    array_push(
                        $banners["banners"],
                        [
                            "banner_script"            => get_sub_field("banner_script"),
                            "banner_collapse_class"    => $collapse_state,
                            "banner_b_g_color"         => get_sub_field("banner_b_g_color"),
                            "banner_title_color"       => get_sub_field("banner_title_color"),
                            "banner_folded_title"      => get_sub_field("banner_folded_title"),
                            "banner_fold_button_title" => get_sub_field("banner_fold_button_title"),
                            "banner_href"              => get_sub_field("banner_href"),
                            "image_1920"               => get_sub_field("image_1920"),
                            "image_736"                => get_sub_field("image_736"),
                            "image_320"                => get_sub_field("image_320")
                        ]
                    );
                }
            }
        }
    }

    wp_reset_postdata();

    if (count($banners) > 0) {
        renderRandomBanner($banners, $post);
    }
}

function in_array_r($needle, $haystack, $strict = false) {

    foreach ($haystack as $item) {

        if (($strict ? $item === $needle : $item == $needle) ||
            (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

function renderRandomBanner($banners, $post) {
    global $post;

    $banner_collapse_class = "jewelry-push";
    $banner_wrapper_class = "post-wrap post-full wrap-random jewelry-content";
    $random_index = rand(0, (count($banners["banners"]) - 1));
    $random_banner = $banners["banners"][$random_index];
    $position = $banners["position"];

    if ($random_banner instanceof WP_Post && ($post->ID != $random_banner->ID || is_home())) :
        $post = $random_banner;

        setup_postdata($random_banner);

        $thumb_src = "";
        $thumb_srcset = "";
        $thumb_width = "";
        $thumb_height = "";
        $thumb_alt = "";

        $post_id_attr = "post-" . $post->ID;
        $post_class = join(" ", get_post_class('h-entry'));
        $post_link = get_the_permalink();
        $post_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "full");
        $post_title = strip_tags(get_the_title());
        $banner_script = $post->banner_script;
        $banner_background_color = $post->banner_b_g_color;
        $banner_title_color = $post->banner_title_color;
        $banner_collapse_class .= " $post->banner_collapse_class ";
        $banner_folded_title = $post->banner_folded_title;
        $banner_fold_button_title = $post->banner_fold_button_title;

        if ($post_thumb) {
            $thumb_src = custom_image_getter(
                $post_thumb[0],
                [

                    "dont_add_sizes" => true,
                    "i_thumb_name"   => "header-banner"
                ]
            );
            $thumb_width = $post_thumb[1];
            $thumb_height = $post_thumb[2];

            $thumb_srcset = custom_image_getter(
                    $post_thumb[0],
                    [
                        "dont_add_sizes" => true,
                        "i_thumb_name"   => "header-banner",
                        "i_sizes"        => [
                            2880,
                            195
                        ]
                    ]
                ) . " 2x";
            $thumb_srcset .= ", " . custom_image_getter(
                    $post_thumb[0],
                    [
                        "dont_add_sizes" => true,
                        "i_thumb_name"   => "header-banner",
                        "i_sizes"        => [
                            1920,
                            130
                        ]
                    ]
                ) . " 1920w";
            $thumb_srcset .= ", " . custom_image_getter(
                    $post_thumb[0],
                    [
                        "dont_add_sizes" => true,
                        "i_sizes"        => [
                            1104,
                            195
                        ]
                    ]
                ) . " 2x";
            $thumb_srcset .= ", " . custom_image_getter(
                    $post_thumb[0],
                    [
                        "dont_add_sizes" => true,
                        "i_sizes"        => [
                            736,
                            130
                        ]
                    ]
                ) . " 736w";
            $thumb_srcset .= ", " . custom_image_getter(
                    $post_thumb[0],
                    [
                        "dont_add_sizes" => true,
                        "i_sizes"        => [
                            480,
                            195
                        ]
                    ]
                ) . " 2x";
            $thumb_srcset .= ", " . custom_image_getter(
                    $post_thumb[0],
                    [
                        "dont_add_sizes" => true,
                        "i_sizes"        => [
                            320,
                            130
                        ]
                    ]
                ) . " 320w";
        }

        $banner_wrapper_class .= " $position ";
        $banner_wrapper_class .= " jewelry-post ";
        $banner_wrapper_style = "background-color: $banner_background_color;";
        $banner_font_color_style = "color: $banner_title_color;";
        $banner_folded_title = ($banner_folded_title) ? $banner_folded_title : $post_title ?>

        <div class="<?= $banner_collapse_class ?>" style="<?= $banner_wrapper_style ?>">
            <button type="button" class="btn btn-sm btn-none jewelry-btn" style="<?=
            $banner_font_color_style
            ?>">
                <span class="jewelry-btn__title"
                      aria-hidden="true"><?= $banner_fold_button_title ?></span><span
                        class="jewelry-btn__close"></span>
            </button>
            <div class="jewelry-title" style="<?= $banner_font_color_style ?>"><?= $banner_folded_title ?></div>
        </div>
        <div class="<?= $banner_wrapper_class ?>">
            <article id='<?= $post_id_attr ?>' class=' <?= $post_class ?>' itemscope=''
                     itemtype='http://schema.org/BlogPosting'>
                <a class='u-url thumb-lnk' href='<?= $post_link ?>' itemprop='url' rel='bookmark'>

                    <img src='<?= $thumb_src ?>' data-srcset='<?= $thumb_srcset ?>' class='u-featured wp-post-image'
                         itemprop='image' width='<?= $thumb_width ?>' height='<?= $thumb_height ?>'
                         alt='<?= $thumb_alt ?>'/>
                    <section class='post-info-wrapper'>
                        <header class='entry-header'>
                            <a href='<?= $post_link ?>' class='u-url' itemprop='url' rel='bookmark'>
                                <h2 class='p-name entry-title' itemprop='headline'>
                                    <span itemprop='name'>
                                        <?= $post_title ?>
                                    </span>
                                </h2>
                            </a>
                        </header>
                    </section>
                    <div class='count-block'>
                        <?php //TODO countBlock()
                        ?>
                    </div>
                </a>
            </article>
        </div>
        <?= $banner_script ?>

    <?php elseif ($random_banner instanceof WP_Post && $post->ID == $random_banner->ID) : ?>
        <?php unset($banners["banners"][$random_index]);

        $banners["banners"] = array_merge($banners["banners"]);

        renderRandomBanner($banners, $post); ?>
    <?php endif; ?>

    <?php if (is_array($random_banner)) :
        $targeting = "dataLayer.push({event: 'gaEv', eventCategory: 'Banner', eventAction: '$position', eventLabel: 'BannerClick'})";

        $banner_collapse_class_ = $random_banner["banner_collapse_class"];
        $banner_collapse_class .= " $banner_collapse_class_ ";
        $banner_background_color = $random_banner["banner_b_g_color"];
        $banner_wrapper_style = "background-color: $banner_background_color;";
        $banner_title_color = $random_banner["banner_title_color"];
        $banner_font_color_style = "color: $banner_title_color";
        $banner_folded_title = $random_banner["banner_folded_title"];
        $banner_fold_button_title = $random_banner["banner_fold_button_title"];

        $banner_script = $random_banner["banner_script"];
        $banner_1920 = $random_banner["image_1920"];
        $banner_736 = $random_banner["image_736"];
        $banner_320 = $random_banner["image_320"];

        $src = custom_image_getter(
            $banner_1920['url'],
            [
                "dont_add_sizes" => true,
                "i_thumb_name"   => "header-banner"
            ]
        );

        $srcset = custom_image_getter(
                $banner_1920['url'],
                [
                    "dont_add_sizes" => true,
                    "i_thumb_name"   => "header-banner",
                    "i_sizes"        => [
                        2880,
                        195
                    ]
                ]
            ) . " 2x";
        $srcset .= ", " . custom_image_getter(
                $banner_1920['url'],
                [
                    "dont_add_sizes" => true,
                    "i_thumb_name"   => "header-banner",
                    "i_sizes"        => [
                        1920,
                        130
                    ]
                ]
            ) . " 1920w";
        $srcset .= ", " . custom_image_getter(
                $banner_736['url'],
                [
                    "dont_add_sizes" => true,
                    "i_sizes"        => [
                        1104,
                        195
                    ]
                ]
            ) . " 2x";
        $srcset .= ", " . custom_image_getter(
                $banner_736['url'],
                [
                    "dont_add_sizes" => true,
                    "i_sizes"        => [
                        736,
                        130
                    ]
                ]
            ) . " 736w";
        $srcset .= ", " . custom_image_getter(
                $banner_320['url'],
                [
                    "fit"            => "auto",
                    "dont_add_sizes" => true,
                    "i_sizes"        => [
                        480,
                        195
                    ]
                ]
            ) . " 2x";
        $srcset .= ", " . custom_image_getter(
                $banner_320['url'],
                [

                    "dont_add_sizes" => true,
                    "fit"            => "auto",
                    "i_sizes"        => [
                        320,
                        130
                    ]
                ]
            ) . " 320w";

        $banner_wrapper_class .= " $position ";
        $random_banner_link = $random_banner["banner_href"]; ?>

        <div class="<?= $banner_collapse_class ?>" style="<?= $banner_wrapper_style ?>">
            <button type="button" class="btn btn-sm btn-none jewelry-btn"
                    style="<?= $banner_font_color_style
                    ?>">
                <span class="jewelry-btn__close" aria-hidden="true"></span><span
                        class="jewelry-btn__title"><?= $banner_fold_button_title ?></span>
            </button>
            <div class="jewelry-title" style="<?= $banner_font_color_style ?>"><?= $banner_folded_title ?></div>
        </div>

        <div class="<?= $banner_wrapper_class ?>" style="<?= $banner_wrapper_style ?>">
            <a rel='nofollow noopener' target='_blank' href='<?= $random_banner_link ?>'
               onclick="<?= $targeting ?>">
                <img width='100%' src='<?= $src ?>' data-srcset='<?= $srcset ?>'/>
            </a>
        </div>
        <?= $banner_script ?>

    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
<?php } ?>