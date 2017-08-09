<?php
/**
 * Fix stuff from third parties
 *
 * @since 1.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Remove picu collections from Yoast xml sitemaps
 *
 * @since 0.9.4
 */
function picu_remove_from_wpseo_sitemap( $exclude = false, $post_type ) {

	if ( 'picu_collection' == $post_type ) {
		return true;

	}
	return false;
}

add_filter( 'wpseo_sitemap_exclude_post_type', 'picu_remove_from_wpseo_sitemap', 10, 2 );


/**
 * Remove picu collections from Yoast xml sitemap options
 *
 * @since 1.1.0
 */
function picu_remove_from_wpseo_sitemap_options( $post_types ) {

	if ( isset( $post_types['picu_collection'] ) ) {
		unset( $post_types['picu_collection'] );
	}

	return $post_types;
}

add_filter( 'wpseo_sitemaps_supported_post_types', 'picu_remove_from_wpseo_sitemap_options', 10, 2 );


/**
 * Remove picu images from attachment sitemap
 *
 * @package picu
 * @since 1.1.0
 */
function picu_remove_attachments_from_yoast_sitemap( $output, $url ) {

	if ( isset( $url['images'][0]['src'] ) AND strpos( $url['images'][0]['src'], '/picu/' ) ) {
		return '';
	}
	else {
		return $output;
	}

}

add_filter( 'wpseo_sitemap_url', 'picu_remove_attachments_from_yoast_sitemap', 10, 2 );


// /**
//  * Remove Yoast meta box from picu collections
//  *
//  * @since 1.1.0
//  */
// function picu_remove_yoast_metabox() {
// 	 remove_meta_box( 'wpseo_meta', 'picu_collection', 'normal' );
// }
//
// add_action( 'add_meta_boxes', 'picu_remove_yoast_metabox', 99 );
//
//
// /**
//  * Dequeue Yoast script on collection edit screen
//  *
//  * @since 1.1.0
//  */
// function picu_dequeue_scripts() {
// 	global $post;
//
// 	if ( function_exists( 'get_current_screen' ) ) {
// 		$current_screen = get_current_screen();
// 	}
//
// 	// Prevent conflicts in case no current_screen is set
// 	if ( empty( $current_screen ) ) {
// 		return;
// 	}
//
// 	if ( ( 'picu_collection' == $current_screen->post_type ) ) {
// 		wp_dequeue_script( 'yoast-seo-post-scraper' );
// 	}
//
// }
//
// add_action( 'wp_print_scripts', 'picu_dequeue_scripts', 100 );


/**
 * Remove Yoast collumns on collection overview screen
 *
 * @since 1.1.0
 */
function picu_remove_yoast_columns( $columns ) {

	unset( $columns['wpseo-score'] );
	unset( $columns['wpseo-title'] );
	unset( $columns['wpseo-metadesc'] );
	unset( $columns['wpseo-focuskw'] );
	unset( $columns['wpseo-score-readability'] );

	return $columns;
}

add_filter ( 'manage_edit-picu_collection_columns', 'picu_remove_yoast_columns' );


// Enable for testing purposes only!
// add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');