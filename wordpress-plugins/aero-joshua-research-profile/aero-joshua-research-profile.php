<?php
/**
 * Plugin Name: AERO Joshua Staubs Research Profile
 * Description: Adds the self-contained Joshua Staubs research profile and creates an unpublished review page on activation.
 * Version: 1.0.1
 * Author: Aerospace Engineering Program, Chulalongkorn University
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AERO_JOSHUA_PROFILE_SHORTCODE = 'aero_joshua_staubs_profile';
const AERO_JOSHUA_PROFILE_META_KEY  = '_aero_joshua_profile_page';

/**
 * Return only the embeddable profile section from the supplied standalone HTML.
 */
function aero_joshua_profile_markup() {
	static $markup = null;

	if ( null !== $markup ) {
		return $markup;
	}

	$file = plugin_dir_path( __FILE__ ) . 'profile.html';
	$html = is_readable( $file ) ? file_get_contents( $file ) : '';

	$start = $html ? strpos( $html, '<section id="js-research-profile"' ) : false;
	$end   = $html ? strrpos( $html, '</section>' ) : false;

	if ( false === $start || false === $end || $end < $start ) {
		$markup = '<p>Research profile content is unavailable.</p>';
		return $markup;
	}

	$markup = substr( $html, $start, $end + strlen( '</section>' ) - $start );

	// The site header already carries the program identity, so remove the
	// duplicate brand panel from the profile hero.
	$markup = preg_replace( '/<div class="js-brand">.*?<\/div>/s', '', $markup, 1 );

	// Let the hero copy sit at the bottom after removing the brand row. Enlarge
	// the desktop background slightly to crop the dark source-image edge while
	// retaining cover behavior at tablet and mobile breakpoints.
	$overrides = '#js-research-profile .js-top-inner{display:flex;align-items:flex-end;padding-top:0}'
		. '#js-research-profile .js-top{background-size:calc(100% + 176px) auto;background-position:center center}'
		. '@media(max-width:820px){#js-research-profile .js-top{background-size:cover}}';
	$markup    = str_replace( '</style>', $overrides . '</style>', $markup );

	return $markup;
}
add_shortcode( AERO_JOSHUA_PROFILE_SHORTCODE, 'aero_joshua_profile_markup' );

/**
 * Create one draft review page. Never overwrite or publish an existing page.
 */
function aero_joshua_profile_activate() {
	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'draft', 'pending', 'private', 'publish' ),
			'meta_key'       => AERO_JOSHUA_PROFILE_META_KEY,
			'meta_value'     => '1',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( $existing ) {
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_title'   => 'Joshua Staubs — Research Profile',
			'post_name'    => 'joshua-staubs-research-profile',
			'post_content' => '[' . AERO_JOSHUA_PROFILE_SHORTCODE . ']',
		),
		true
	);

	if ( ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, AERO_JOSHUA_PROFILE_META_KEY, '1' );
		update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
	}
}
register_activation_hook( __FILE__, 'aero_joshua_profile_activate' );
