<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 7/6/2017
 * Time: 11:49
 */

/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 * @package WordPress
 */

use Timber\Timber;

/** WP_Query $wp_query */
global $wp_query;
/** WP_Post $post */
global $post;

$args = $wp_query->query_vars;
$args['post_status'] = 'publish';
$wp_query = new WP_Query($args);

function get_image($id) {
    $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');

    if (is_array($thumbnail)) {
        $thumbnail = $thumbnail[0];
    }

    return $thumbnail;
}

function generate_dzen_content() {
    $content = "";

    if (have_rows('admin_post_layout')) {

        while (have_rows('admin_post_layout')) {
            the_row();

            $part_name = get_row_layout();

            if ($part_name && ($part_name == 'add_text' || $part_name == 'add_gallery')) {
                $variables = null;

                switch ($part_name) {
                    case 'add_text':
                        $variables = (new \Utils\WASTimber())->get_text_layout_variables();
                        break;

                    case 'add_gallery':
                        $variables = ['gallery' => get_sub_field('main_gallery')];
                        break;
                }

                ob_start();

                Timber::render(
                    'feeds/dzen/' . $part_name . '.twig',
                    [
                        'variables' => $variables
                    ]
                );

                $content .= ob_get_clean();
            }
        }
    }

    return $content;
}

header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);

echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';

/**
 * Fires between the xml and rss tags in a feed.
 *
 * @since 4.0.0
 *
 * @param string $context Type of feed. Possible values include 'rss2', 'rss2-comments',
 *                        'rdf', 'atom', and 'atom-comments'.
 */
do_action('rss_tag_pre', 'rss2');
?>

<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:media="http://search.yahoo.com/mrss/"
    <?php do_action('rss2_ns'); ?>>
    <channel>
        <title>WAS — Журнал потрясающих историй</title>
        <link><?php bloginfo_rss('url') ?></link>
        <description>Яркие истории о героях и мерзавцах, подвигах и пороках. Все самое интересное за 3000 лет истории
            человечества.
        </description>
        <language>ru</language>
        <?php
        /**
         * Fires at the end of the RSS2 Feed Header.
         *
         * @since 2.0.0
         */
        do_action('rss2_head');

        while (have_posts()) {
            the_post();

            $description = "";
            $YDescription = get_post_meta($post->ID, "_yoast_wpseo_metadesc", true);
            $excerpt = get_the_excerpt($post);

            switch (true) {
                case !empty($YDescription):
                    $description = $YDescription;
                    break;

                case !empty($excerpt):
                    $description = $excerpt;
                    break;
            }

            $author = "";
            $acfAuthor = get_field('post_author', $post->ID);
            $wpAuthor = get_the_author();

            switch (true) {
                case !empty($acfAuthor):
                    $author = $acfAuthor;
                    break;

                case !empty($wpAuthor):
                    $author = $wpAuthor;
                    break;
            }

            $options = get_field("article_options", $post->ID);
            $mediaRating = (is_array($options) && in_array("adult", $options)) ? "adult" : "nonadult";
            ?>
            <item>
                <title><?php the_title_rss() ?></title>
                <link><?php the_permalink_rss() ?></link>
                <amplink><?php the_permalink_rss() ?><?= "amp" ?></amplink>
                <guid isPermaLink="false"><?php the_guid(); ?></guid>
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <media:rating scheme="urn:simple"><?= $mediaRating ?></media:rating>
                <author><![CDATA[<?= $author ?>]]></author>
                <?php the_category_rss('rss2') ?>
                <?php
                $image = get_image($post->ID);

                if ($image) {
                    echo '<enclosure url="' . $image . '" type="image/jpeg" length="' . filesize(get_attached_file(get_post_thumbnail_id($post->ID))) . '"/>';
                }
                ?>
                <?php rss_enclosure(); ?>
                <description><![CDATA[<?= trim(esc_html($description)) ?>]]></description>
                <content:encoded><![CDATA[<?= generate_dzen_content() ?>]]></content:encoded>
                <?php
                /**
                 * Fires at the end of each RSS2 feed item.
                 *
                 * @since 2.0.0
                 */
                do_action('rss2_item');
                ?>
            </item>
        <?php } ?>
    </channel>
</rss>