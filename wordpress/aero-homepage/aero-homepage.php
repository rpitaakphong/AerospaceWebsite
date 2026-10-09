<?php
/**
 * Plugin Name: AERO Full-Code Homepage
 * Description: Full-code homepage2 with automatic WordPress News. Leaves the original homepage and theme header/footer untouched.
 * Version: 1.0.2
 * Author: Aerospace Engineering, ISE, Chulalongkorn University
 * Requires PHP: 7.4
 */
if (!defined('ABSPATH')) { exit; }
define('AERO_HOME_VERSION', '1.0.2');
define('AERO_HOME_TEMPLATE', 'aero-full-code-homepage.php');

// Activation only creates a draft. Front-page assignment is a separate, reviewed step.
function aero_home_activate() {
    $page = get_page_by_path('homepage2', OBJECT, 'page');
    if ($page instanceof WP_Post) {
        // Never silently take over an unrelated existing page.
        if (get_post_meta($page->ID, '_aero_full_code_homepage', true)) {
            update_option('aero_home_page_id', $page->ID, false);
        }
        return;
    }
    $id = wp_insert_post(array(
        'post_title' => 'homepage2', 'post_name' => 'homepage2',
        'post_type' => 'page', 'post_status' => 'draft',
        'post_content' => '<!-- Full-code homepage maintained in the AERO Full-Code Homepage plugin. -->',
    ), true);
    if (is_wp_error($id)) { return; }
    update_post_meta($id, '_aero_full_code_homepage', 1);
    update_post_meta($id, '_wp_page_template', AERO_HOME_TEMPLATE);
    update_option('aero_home_page_id', $id, false);
    if (false === get_option('aero_home_previous_front_settings', false)) {
        add_option('aero_home_previous_front_settings', array(
            'show_on_front' => get_option('show_on_front'),
            'page_on_front' => (int) get_option('page_on_front'),
            'page_for_posts' => (int) get_option('page_for_posts'),
        ), '', false);
    }
}
register_activation_hook(__FILE__, 'aero_home_activate');

// WordPress changes the old Home page permalink to /home/ after the switch.
// Preserve the existing menu entries but keep their Home destination at the site root.
function aero_home_menu_destination($items) {
    $new_id = (int) get_option('aero_home_page_id');
    if (!$new_id || (int) get_option('page_on_front') !== $new_id) { return $items; }
    $previous = get_option('aero_home_previous_front_settings', array());
    $old_id = (int) ($previous['page_on_front'] ?? 0);
    foreach ($items as $item) {
        if ($old_id && 'page' === $item->object && (int) $item->object_id === $old_id) {
            $item->url = home_url('/');
            $item->classes = array_values(array_diff($item->classes, array('current-menu-item','current_page_item')));
            $item->current = is_front_page();
            if ($item->current) { $item->classes[] = 'current-menu-item'; }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'aero_home_menu_destination');

function aero_home_is_page() {
    $id = (int) get_option('aero_home_page_id');
    return $id && is_page($id) && get_post_meta($id, '_aero_full_code_homepage', true);
}
add_filter('theme_page_templates', function ($templates) {
    $templates[AERO_HOME_TEMPLATE] = 'AERO Full-Code Homepage';
    return $templates;
});
add_filter('template_include', function ($template) {
    return aero_home_is_page() ? __DIR__ . '/templates/homepage.php' : $template;
}, 99);
add_filter('body_class', function ($classes) {
    if (aero_home_is_page()) { $classes[] = 'aero-homepage2'; }
    return $classes;
});
add_filter('document_title_parts', function ($parts) {
    if (aero_home_is_page()) { $parts['title'] = 'Aerospace Engineering | Chulalongkorn University'; }
    return $parts;
});
add_action('wp_enqueue_scripts', function () {
    if (!aero_home_is_page()) { return; }
    wp_enqueue_style('aero-home-fonts', 'https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap', array(), null);
    wp_enqueue_style('aero-home', plugins_url('assets/homepage.css', __FILE__), array(), AERO_HOME_VERSION);
    wp_enqueue_script('aero-home', plugins_url('assets/homepage.js', __FILE__), array(), AERO_HOME_VERSION, true);
}, 100);

function aero_home_asset($file) {
    return plugins_url('assets/' . $file, __FILE__);
}

// Render real published posts on the server; no frozen fallback cards or browser API dependence.
function aero_home_news() {
    $category = get_term_by('slug', 'news', 'category');
    if (!$category) { $category = get_term_by('name', 'News', 'category'); }
    if (!$category || is_wp_error($category)) {
        echo '<p class="news-empty">Programme news will appear here when available.</p>';
        return;
    }
    $news = new WP_Query(array(
        'post_type' => 'post', 'post_status' => 'publish',
        'cat' => (int) $category->term_id, 'posts_per_page' => 3,
        'orderby' => array('date' => 'DESC', 'ID' => 'DESC'),
        'ignore_sticky_posts' => true, 'no_found_rows' => true,
    ));
    while ($news->have_posts()) {
        $news->the_post();
        ?>
        <a class="news-card" href="<?php echo esc_url(get_permalink()); ?>">
            <div class="news-card__image">
                <?php if (has_post_thumbnail()) {
                    echo get_the_post_thumbnail(get_the_ID(), 'large', array('loading' => 'lazy', 'decoding' => 'async'));
                } else { ?>
                    <span class="news-image-placeholder" aria-hidden="true">AEROSPACE / NEWS</span>
                <?php } ?>
            </div>
            <div class="news-card__body">
                <span class="small-label">NEWS / <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d M Y')); ?></time></span>
                <h3><?php echo esc_html(get_the_title()); ?></h3>
                <span class="card-arrow">Read story <span aria-hidden="true">↗</span></span>
            </div>
        </a>
        <?php
    }
    if (!$news->post_count) { echo '<p class="news-empty">Programme news will appear here when available.</p>'; }
    wp_reset_postdata();
}
