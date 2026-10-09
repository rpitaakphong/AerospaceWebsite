<?php
if (!defined('ABSPATH')) { exit; }
// Use the active theme's normal header/footer, which retain the existing Elementor locations.
get_header();
while (have_posts()) { the_post(); }
require __DIR__ . '/content.php';
get_footer();
