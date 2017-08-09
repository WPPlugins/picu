<?php
/**
 * Picu front end template
 *
 * @since 0.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Returns template file
 *
 * @since 0.3.0
 */

function picu_load_template( $single_template ) {

	global $post;

	if ( $post->post_type == 'picu_collection' ) {
		if ( file_exists( PICU_PATH . 'frontend/picu-app.php' ) ) {
			$single_template = PICU_PATH . 'frontend/picu-app.php';
		}
	}

	return $single_template;
}

add_filter( 'single_template', 'picu_load_template' );


/**
 * Gather picu body classes
 *
 * @since 0.7.3
 */
function get_picu_body_classes() {

	$picu_body_classes = array();

	/**
	 * Add filter to modify body classes
	 */
	$picu_body_classes = apply_filters( 'picu_body_classes', $picu_body_classes );

	return $picu_body_classes;

}


/**
 * Echo picu body classes
 *
 * @since 0.7.3
 */
function picu_body_classes() {

	$picu_body_classes = get_picu_body_classes();

	if ( count( $picu_body_classes ) > 0 ) {
		echo sprintf( ' class="%s"', implode( ' ', $picu_body_classes ) );
	}

}


/**
 * Load backbone templates
 *
 * @since 0.7.2
 */
function picu_load_backbone_templates() {

	$templates = array(
		'collection-info'	=> PICU_PATH . 'frontend/js/templates/picu-collection-info.php',
		'status-bar'		=> PICU_PATH . 'frontend/js/templates/picu-status-bar.php',
		'gallery-item'		=> PICU_PATH . 'frontend/js/templates/picu-gallery-item.php',
		'lightbox'			=> PICU_PATH . 'frontend/js/templates/picu-lightbox.php',
		'send-selection'	=> PICU_PATH . 'frontend/js/templates/picu-send-selection.php',
		'approved'			=> PICU_PATH . 'frontend/js/templates/picu-approved.php'
	);

	$templates = apply_filters( 'picu_load_backbone_templates', $templates );

	return $templates;
}


/**
 * Load collections, models & views
 *
 * @since 0.7.2
 */
function picu_load_cmv() {

	$cmv = array(
		'picu-state' =>				PICU_URL . 'frontend/js/models/picu-state.js',
		'collection-info-view' =>	PICU_URL . 'frontend/js/views/collection-info-view.js',
		'status-bar-view' =>		PICU_URL . 'frontend/js/views/status-bar-view.js',
		'single-image' =>			PICU_URL . 'frontend/js/models/single-image.js',
		'picu-collection' =>		PICU_URL . 'frontend/js/collections/picu-collection.js',
		'gallery-view' =>			PICU_URL . 'frontend/js/views/gallery-view.js',
		'single-image-view' =>		PICU_URL . 'frontend/js/views/single-image-view.js',
		'lightbox-view' =>			PICU_URL . 'frontend/js/views/lightbox-view.js',
		'send-selection-view' =>	PICU_URL . 'frontend/js/views/send-selection-view.js',
		'approved-view' =>			PICU_URL . 'frontend/js/views/approved-view.js'
	);

	$cmv = apply_filters( 'picu_load_cmv', $cmv );

	return $cmv;
}


/**
 * Load front end stylesheet
 *
 * @since 0.7.0
 */
function picu_load_styles() {

	$styles = array(
		'picu' => PICU_URL . 'frontend/css/picu-dark.css',
		'picu-print' => PICU_URL . 'frontend/css/picu-print.css',
	);

	$styles = apply_filters( 'picu_load_styles', $styles );

	$styles_output = '';

	foreach( $styles as $name => $url ) {
		$styles_output .= "\n\t\t" . '<link href="' . $url . '" rel="stylesheet" media="';
		if ( 'picu-print' == $name ) {
			$styles_output .= 'print';
		}
		else {
			$styles_output .= 'screen';
		}
		$styles_output .= '" />';
	}

	$styles_output = apply_filters( 'picu_styles_output', $styles_output );

	echo $styles_output . "\n";
}


/**
 * Load stylesheet depending on settings
 *
 * @since 0.7.0
 */
function picu_theme_options( $styles ) {

	$options = get_option( 'picu_settings' );

	if ( isset( $options['theme'] ) AND $options['theme'] == 'light' ) {
		$styles['picu'] = PICU_URL . 'frontend/css/picu-light.css';
	}

	return $styles;
}

add_filter( 'picu_load_styles', 'picu_theme_options', 10, 1 );


/**
 * Get images for the picu gallery
 *
 * @since 0.3.0
 *
 * @param $content, the collection content
 * @return string, javascript objects, containing the image collection
 *
 */

function picu_get_images() {

	$post = get_post();

	// Get image IDs
	$include = get_post_meta( $post->ID, '_picu_collection_gallery_ids', true );

	// Check if there are any images to build a selection from
	if ( !empty( $include ) ) {

		// Set up attachment requests
		$orderby = 'post__in';
		$order = 'ASC';
		$id = $post->ID;

		// Load attachments
		$_attachments = get_posts( array( 'include' => $include, 'post_status' => 'any', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}

		// If we don't get attachments, this is as far as we go
		if ( empty( $attachments ) ) {
			return '[ ]';
		}

		// Prepare backbone image collection
		$i = 0;
		$imgnum = 1;
		$image_collection = '';
		$orientation = '';

		// Create image objects
		foreach ( $attachments as $id => $attachment ) {

			// Calculate image orientiation
			$image_meta = wp_get_attachment_metadata( $id );

			if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
				$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
			}

			// Get image description
			if ( trim( $attachment->post_excerpt ) ) {
				$image_description = wptexturize( $attachment->post_excerpt );
			} else {
				$image_description = '';
			}

			// Get attachment URLs
			$image_name = wp_get_attachment_image_src( $attachment->ID, 'full' );
			$image_path = wp_get_attachment_image_src( $attachment->ID, 'picu-large' );
			$image_path_small = wp_get_attachment_image_src( $attachment->ID, 'picu-small' );

			if ( function_exists( 'wp_get_attachment_image_srcset' ) ) {
				$image_path_srcset = wp_get_attachment_image_srcset( $attachment->ID, 'picu-large' );
				$image_path_small_srcset = wp_get_attachment_image_srcset( $attachment->ID, 'picu-small' );
			}
			else {
				$image_path_srcset = '';
				$image_path_small_srcset = '';
			}

			// Set selected attribute, if a selection has been (auto) saved
			$temp = get_post_meta( $post->ID, '_picu_collection_selection', true );

			if ( ! empty( $temp ) ) {
				$selection = $temp["selection"];
				$selected = ( in_array( $attachment->ID, $selection ) ) ? 'true' : 'false';
			} else {
				$selected = 'false';
			}

			$image_title = basename( $image_name[0] );

			$current_image = array(
				'number' => $imgnum,
				'imageID' => $attachment->ID,
				'title' => $image_title,
				'description' => $image_description,
				'imagePath' => $image_path[0],
				'imagePath_small' => $image_path_small[0],
				'imagePath_srcset' => $image_path_srcset,
				'imagePath_small_srcset' => $image_path_small_srcset,
				'orientation' => $orientation,
				'selected' => $selected
			);

			/*
			 * Add filter to modify each individual image
			 */
			$current_image = apply_filters( 'picu_single_image_data', $current_image );

			$image_collection .= '{
				"number": ' . $current_image['number'] . ',
				"imageID": "' . $current_image['imageID'] . '",
				"title": "' . $current_image['title'] . '",
				"description": "' . $current_image['description'] . '",
				"imagePath": "' . $current_image['imagePath'] . '",
				"imagePath_small": "' . $current_image['imagePath_small'] . '",
				"imagePath_srcset": "' . $current_image['imagePath_srcset'] . '",
				"imagePath_small_srcset": "' . $current_image['imagePath_small_srcset'] . '",
				"orientation": "' . $current_image['orientation'] . '",
				"selected": ' .  $current_image['selected'] . '
			},';

			$imgnum++;
		}

		// Remove last comma
		$image_collection = rtrim( $image_collection, ", \t\n" );

		// Remove whitespace (space, tab and/or newline)
		$image_object = preg_replace( "/\s+/", ' ', $image_collection );

		return '[ ' . $image_object . ' ]';

	}
	else {
		return '[ ]';
	}
}


/**
 * Create AppState JSON object
 *
 * @since 0.6.0
 *
 * @param $id, collection id
 * @return string, json
 *
 */

function picu_get_app_state() {

	$post = get_post();
	$id = $post->ID;

	$description = wpautop ( htmlspecialchars( get_post_meta( $id, '_picu_collection_description', true ), ENT_QUOTES, 'UTF-8' ) );

	$state = array(
		'nonce' => wp_create_nonce( 'picu-ajax-security' ),
		'postid' => get_the_ID( $id ),
		'poststatus' => get_post_status( $id ),
		'title' => get_the_title( $id ),
		'date' => get_the_date( get_option( 'date_format' ), $id ),
		'description' => preg_replace( "/\r|\n/", "", $description ),
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'error-msg-no-imgs' => __( '<h2>No images found</h2><p>It seems there are no images in this collection.</p>', 'picu' ),
		'error-msg-filter' => __( 'yoyo', 'picu' )
	);


	/**
	 * Add filter to extend on app state parameters
	 */
	$state = apply_filters( 'picu_app_state', $state );

	$app_state = json_encode( $state, 64 ); // 64 = JSON_UNESCAPED_SLASHES

	return $app_state;

}