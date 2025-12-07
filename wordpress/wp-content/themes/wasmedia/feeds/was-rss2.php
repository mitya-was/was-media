<?php
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

function generate_content() {
    $content = "";
    $WASTimber = new \Utils\WASTimber();
    $context = $WASTimber->get_post_layout_parts();

    if (is_array($context)) {

        foreach ($context as $part) {

            foreach ($part as $part_name => $part_variables) {

                if ($part_name != 'none' && $part_name == 'add_text') {
                    ob_start();

                    Timber::render(
                        'post-layout-parts/post_' . $part_name . '.twig',
                        [
                            'variables' => $part_variables
                        ]
                    );

                    $content .= esc_html(strip_tags(ob_get_clean()));
                }
            }
        }
    }

    return $content;
}

header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
$more = 1;
$frequency = '1';
$duration = 'hourly';
$date = get_lastpostmodified('GMT');

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
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
     xmlns:yandex="http://news.yandex.ru"
    <?php
    /**
     * Fires at the end of the RSS root to add namespaces.
     *
     * @since 2.0.0
     */
    do_action('rss2_ns');
    ?>
>

    <channel>
        <title>WAS — Журнал потрясающих историй</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml"/>
        <link><?php bloginfo_rss('url') ?></link>
        <description>Яркие истории о героях и мерзавцах, подвигах и пороках. Все самое интересное за 3000 лет истории
            человечества.
        </description>
        <yandex:logo><?= content_url() . '/uploads/2017/03/was-share.jpg' ?></yandex:logo>
        <lastBuildDate><?= $date ? mysql2date('D, d M Y H:i:s +0000', $date, false) : date('D, d M Y H:i:s +0000') ?></lastBuildDate>
        <language><?php bloginfo_rss('language'); ?></language>
        <sy:updatePeriod><?= apply_filters('rss_update_period', $duration) ?></sy:updatePeriod>
        <sy:updateFrequency><?= apply_filters('rss_update_frequency', $frequency) ?></sy:updateFrequency>
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
            ?>
            <item>
                <title><?php the_title_rss() ?></title>
                <link><?php the_permalink_rss() ?></link>

                <?php if (get_comments_number() || comments_open()) : ?>
                    <comments>
                        <?php comments_link_feed(); ?>
                    </comments>
                <?php endif; ?>

                <pubDate><?= mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false) ?></pubDate>
                <?php
                $image = get_image($post->ID);

                if ($image) {
                    echo '<enclosure url="' . $image . '" type="image/jpeg" />';
                }
                ?>
                <yandex:full-text><?= generate_content(); ?></yandex:full-text>
                <dc:creator><![CDATA[<?php $author ?>]]></dc:creator>
                <?php the_category_rss('rss2') ?>
                <guid isPermaLink="false"><?php the_guid(); ?></guid>
                <description><![CDATA[<?= $description ?>]]></description>

                <?php if (get_comments_number() || comments_open()) : ?>
                    <wfw:commentRss><?= esc_url(get_post_comments_feed_link(null, 'rss2')) ?></wfw:commentRss>
                    <slash:comments><?= get_comments_number() ?></slash:comments>
                <?php endif; ?>
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