<?php
/**
 * Plugin Name: AERO Capstone Project Showcase
 * Description: Publishes the Aerospace Capstone Project archive and adds it to the primary navigation.
 * Version: 1.0.10
 * Author: Aerospace Engineering, ISE, Chulalongkorn University
 */

if (!defined('ABSPATH')) {
    exit;
}

const AERO_CAPSTONE_PAGE_OPTION = 'aero_capstone_page_id';
const AERO_CAPSTONE_VERSION = '1.0.10';

/**
 * Create (or reuse) the public page and add it as a level-one menu item.
 */
function aero_capstone_activate() {
    $page = get_page_by_path('capstone-project', OBJECT, 'page');

    $page_data = array(
        'post_title'   => 'Capstone Project',
        'post_name'    => 'capstone-project',
        'post_content' => '<!-- wp:shortcode -->[aero_capstone_showcase]<!-- /wp:shortcode -->',
        'post_status'  => 'publish',
        'post_type'    => 'page',
    );

    if ($page instanceof WP_Post) {
        $page_data['ID'] = $page->ID;
        $page_id = wp_update_post($page_data, true);
    } else {
        $page_id = wp_insert_post($page_data, true);
    }

    if (is_wp_error($page_id) || !$page_id) {
        return;
    }

    update_option(AERO_CAPSTONE_PAGE_OPTION, (int) $page_id, false);
    aero_capstone_add_to_primary_menu((int) $page_id);
}
register_activation_hook(__FILE__, 'aero_capstone_activate');

/**
 * Locate the assigned header menu and add the page at the top level.
 */
function aero_capstone_add_to_primary_menu($page_id) {
    $locations = get_nav_menu_locations();
    $preferred_locations = array('primary', 'menu-1', 'main-menu', 'header-menu', 'top');
    $menu_id = 0;

    foreach ($preferred_locations as $location) {
        if (!empty($locations[$location])) {
            $menu_id = (int) $locations[$location];
            break;
        }
    }

    if (!$menu_id && !empty($locations)) {
        $menu_id = (int) reset($locations);
    }

    if (!$menu_id) {
        $menus = wp_get_nav_menus();
        if (!empty($menus)) {
            $menu_id = (int) $menus[0]->term_id;
        }
    }

    if (!$menu_id) {
        return;
    }

    foreach (wp_get_nav_menu_items($menu_id) ?: array() as $item) {
        if ('page' === $item->object && (int) $item->object_id === $page_id) {
            if ((int) $item->menu_item_parent !== 0) {
                wp_update_nav_menu_item($menu_id, $item->ID, array(
                    'menu-item-title'     => 'Capstone Project',
                    'menu-item-object'    => 'page',
                    'menu-item-object-id' => $page_id,
                    'menu-item-type'      => 'post_type',
                    'menu-item-parent-id' => 0,
                    'menu-item-status'    => 'publish',
                ));
            }
            return;
        }
    }

    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title'     => 'Capstone Project',
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $page_id,
        'menu-item-type'      => 'post_type',
        'menu-item-parent-id' => 0,
        'menu-item-status'    => 'publish',
    ));
}

function aero_capstone_is_showcase_page() {
    $page_id = (int) get_option(AERO_CAPSTONE_PAGE_OPTION);
    return $page_id && is_page($page_id);
}

function aero_capstone_body_class($classes) {
    if (aero_capstone_is_showcase_page()) {
        $classes[] = 'aero-capstone-page';
    }
    return $classes;
}
add_filter('body_class', 'aero_capstone_body_class');

/**
 * Present the Capstone page as the primary desktop navigation call to action.
 */
function aero_capstone_menu_button_styles() {
    ?>
    <style id="aero-capstone-menu-button-styles">
        @media (min-width: 1025px) {
            .elementor-nav-menu--main a.elementor-item[href*="/capstone-project"] {
                box-sizing: border-box;
                margin-left: 0;
                padding: 8px 8px !important;
                border-radius: 999px;
                background: #b5122b !important;
                color: #ffffff !important;
                line-height: 1.2;
                white-space: nowrap;
                box-shadow: 0 5px 14px rgba(181, 18, 43, 0.2);
                transition: background-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
            }

            .elementor-nav-menu--main a.elementor-item[href*="/capstone-project"]:hover,
            .elementor-nav-menu--main a.elementor-item[href*="/capstone-project"]:focus,
            .elementor-nav-menu--main a.elementor-item[href*="/capstone-project"].elementor-item-active {
                background: #8f0e22 !important;
                color: #ffffff !important;
                box-shadow: 0 7px 18px rgba(143, 14, 34, 0.25);
            }

            .elementor-nav-menu--main a.elementor-item[href*="/capstone-project"]:hover {
                transform: translateY(-1px);
            }

            .elementor-nav-menu--main a.elementor-item[href*="/capstone-project"]:focus-visible {
                outline: 3px solid rgba(181, 18, 43, 0.28);
                outline-offset: 3px;
            }

            .elementor-nav-menu--main a.elementor-item[href*="/capstone-project"]::before,
            .elementor-nav-menu--main a.elementor-item[href*="/capstone-project"]::after {
                display: none !important;
            }
        }

        @media (min-width: 1025px) and (max-width: 1399px) {
            .elementor-widget-nav-menu:has(.elementor-nav-menu--main a.elementor-item[href*="/capstone-project"]) {
                width: calc(100% + 48px);
                min-width: calc(100% + 48px);
                max-width: none;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'aero_capstone_menu_button_styles', 100);

/**
 * Embed the isolated archive so the active theme and Elementor cannot alter it.
 */
function aero_capstone_shortcode() {
    $src = add_query_arg('ver', AERO_CAPSTONE_VERSION, plugins_url('showcase/index.html', __FILE__));

    ob_start();
    ?>
    <div class="aero-capstone-embed">
        <iframe
            id="aero-capstone-frame"
            src="<?php echo esc_url($src); ?>"
            title="Aerospace Capstone Project Showcase"
            loading="eager"
            allow="fullscreen"
        ></iframe>
    </div>
    <script>
    (function () {
        var frame = document.getElementById('aero-capstone-frame');
        if (!frame) return;
        window.addEventListener('message', function (event) {
            if (event.source !== frame.contentWindow || !event.data || event.data.type !== 'aero-capstone-height') return;
            var height = Number(event.data.height);
            if (Number.isFinite(height) && height > 500) frame.style.height = Math.ceil(height) + 'px';
        });
    }());
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('aero_capstone_showcase', 'aero_capstone_shortcode');

function aero_capstone_styles() {
    if (!aero_capstone_is_showcase_page()) {
        return;
    }
    ?>
    <style id="aero-capstone-page-styles">
        .aero-capstone-page .entry-title,
        .aero-capstone-page .page-header,
        .aero-capstone-page .post-thumbnail { display: none !important; }
        .aero-capstone-page .site-content,
        .aero-capstone-page .content-area,
        .aero-capstone-page .site-main,
        .aero-capstone-page article,
        .aero-capstone-page .entry-content { max-width: none !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .aero-capstone-embed { width: 100vw; margin-left: calc(50% - 50vw); line-height: 0; overflow: hidden; }
        .aero-capstone-embed iframe { display: block; width: 100%; min-height: 100vh; border: 0; background: #ffffff; }
    </style>
    <?php
}
add_action('wp_head', 'aero_capstone_styles', 99);
