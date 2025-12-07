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

<rss version="2.0" xmlns:rambler="http://news.rambler.ru" <?php do_action('rss2_ns'); ?>>
    <channel>
        <title>WAS — Журнал потрясающих историй</title>
        <link><?php bloginfo_rss('url') ?></link>
        <description>Яркие истории о героях и мерзавцах, подвигах и пороках. Все самое интересное за 3000 лет истории
            человечества.
        </description>
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
            } ?>
            <item>
                <guid isPermaLink="false"><?php the_guid(); ?></guid>
                <title><?php the_title_rss() ?></title>
                <link><?php the_permalink_rss() ?></link>
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <description><![CDATA[<?= trim(esc_html($description)) ?>]]></description>
                <?php the_category_rss('rss2') ?>
                <author><![CDATA[<?= $author ?>]]></author>
                <?php
                $image = get_image($post->ID);

                if ($image) {
                    echo '<enclosure url="' . $image . '" type="image/jpeg" length="' . filesize(get_attached_file(get_post_thumbnail_id($post->ID))) . '"/>';
                }
                ?>
                <?php rss_enclosure(); ?>
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