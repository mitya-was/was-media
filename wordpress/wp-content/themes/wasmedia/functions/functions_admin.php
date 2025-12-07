<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 03-Aug-18
 * Time: 12:08
 */

/**
 * @param WP_Admin_Bar $admin_bar
 */
function add_custom_admin_was_navigation($admin_bar) {
    global $pagenow;

    if (is_admin() && ($pagenow === 'post.php' || $pagenow === 'post-new.php')) {
        $admin_bar->add_menu(
            [
                'id'    => 'admin_was_collapser',
                'title' => "<span class=\"ab-icon dashicons dashicons-welcome-view-site\"></span><span>View</span>",
                'href'  => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_minimize',
                'parent' => 'admin_was_collapser',
                'title'  => "<span>Minimize</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_maximize',
                'parent' => 'admin_was_collapser',
                'title'  => "<span>Maximize</span>",
                'href'   => '#'
            ]
        );

        $admin_bar->add_menu(
            [
                'id'    => 'admin_was_custom_nav',
                'title' => "<span class=\"ab-icon dashicons dashicons-location-alt\"></span><span>Навигатор</span>",
                'href'  => '#',
                'meta'  => [
                    'class' => 'pull-right'
                ],
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-content-editor',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#acf-group_58b84c10297bd'>Редактор контента</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-content-editor-items',
                'parent' => 'admin_was_custom_nav-content-editor',
                'title'  => "",
                'href'   => '#',
                'meta'   => [
                    'class' => 'hidden'
                ]
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-title',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#titlewrap'>Заг</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-excerpt',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#postexcerpt'>Лид</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-postimage',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#postimagediv'>Фичер</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-formatdiv',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#formatdiv'>Вид материала</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-article-options',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='.acf-field-58eb667cc00e0'>Настройки статьи</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-alt-cover',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='.acf-field-59d4ae54d9713'>Альтернативный фичер</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-alt-author',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='.acf-field-58cc10376a093'>Кастомный автор</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-similar-materials',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='.acf-field-591322bd94aab'>Похожие материалы</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-categorydiv',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#categorydiv'>Категории</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-tagsdiv-post_tag',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#tagsdiv-post_tag'>Теги</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-wpseo_meta',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#wpseo_meta'>СЕО и Соц. Сети</span>",
                'href'   => '#'
            ]
        );
        $admin_bar->add_menu(
            [
                'id'     => 'admin_was_custom_nav-authordiv',
                'parent' => 'admin_was_custom_nav',
                'title'  => "<span class='abcn_' data-abcn-go='#authordiv'>Автор-пользователь</span>",
                'href'   => '#'
            ]
        );
    }
}

add_action('admin_bar_menu', 'add_custom_admin_was_navigation', 100);

function cookie_expiration_new($expiration, $user_id, $remember) {
    return 360 * DAY_IN_SECONDS;
}

add_filter('auth_cookie_expiration', 'cookie_expiration_new', 20, 3);

/**
 * Get the user's roles
 * @since 1.0.0
 */
function wp_get_current_user_roles() {
    if( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $roles = ( array ) $user->roles;
        return $roles[0];
    }
}

function my_admin_body_class($classes) {
    if (!empty(wp_get_current_user_roles())) {
        return "$classes" . " user-role-" . wp_get_current_user_roles();
    }
}

add_filter( 'admin_body_class', 'my_admin_body_class' );