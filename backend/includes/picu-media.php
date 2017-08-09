<?php
/**
 * Picu media handling
 *
 * @since 0.4.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Delete all attached media when a collection is deleted
 *
 * @since 0.4.0
 */
function picu_delete_attached_media( $post_id ) {

	if ( 'picu_collection' != get_post_type( $post_id ) )
		return;

	$args = array(
		'post_type' => 'attachment',
		'posts_per_page' => -1,
		'post_status' => 'any',
		'post_parent' => $post_id
	);

	// Temporarily remove our own attachment filter so we actually get anything
	remove_action( 'pre_get_posts', 'picu_exclude_collection_images_from_library' );

	$attachments = new WP_Query( $args );

	// Now that our query is finished, re-add our filter
	add_action( 'pre_get_posts', 'picu_exclude_collection_images_from_library' );

	foreach ( $attachments->posts as $attachment ) {
		if ( false === wp_delete_attachment( $attachment->ID, true ) ) {
			// TODO Log failure to delete attachment
		}
	}
}

add_action( 'before_delete_post', 'picu_delete_attached_media' );


/**
 * Remove a collection's upload folder, when the collection is removed
 *
 * @since 0.5.0
 */
function picu_delete_upload_folder( $post_id ) {

	// Stop if we are not deleting a collection
	if ( 'picu_collection' != get_post_type( $post_id ) )
		return;

	// Get the default upload directory info
	$upload_dir = wp_upload_dir();

	// Add our custom subdirectory info to the default base
	$picu_upload_dir = trailingslashit( $upload_dir['basedir'] ) . "picu/collections/$post_id";

	// Check if the path we get actually is a directory
	if ( is_dir( $picu_upload_dir ) ) {

		// Get folder contents
		$folder_content = array_diff( scandir( $picu_upload_dir ), array( '..', '.' ) );

		// Check if directory is empty
		if ( 0 == count( $folder_content ) ) {
			// Remove that directory
			rmdir( $picu_upload_dir );
		}
		// TODO: Tell user that a folder could not be deleted, because it was not empty
	}

}
add_action( 'deleted_post', 'picu_delete_upload_folder' );


/**
 * Exclude our attachments from media library
 *
 * Don't display images attached to a collection on any
 * queries other than our own.
 *
 * @since 0.5.0
 */
function picu_exclude_collection_images_from_library( $query ) {

	global $pagenow;

	// Stop if we are not on an admin panel
	if ( ! is_admin() ) {
		return;
	}

	// Stop if we are on the wrong admin page
	if ( $pagenow == 'post.php' || $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) {
		return;
	}

	// Remove our action from pre_get_posts to avoid infinite loop
	// (WP_Query would also trigger pre_get_posts filter)
	remove_action( 'pre_get_posts', 'picu_exclude_collection_images_from_library' );

	// Query all collections (CPT 'picu_collection')
	$collections_query = new WP_Query(
		array(
			'post_type' => 'picu_collection',
			'posts_per_page' => -1,
			'post_status' => array( 'any', 'trash' ),
			'fields' => 'ids'
		)
	);

	if ( empty( $collections_query->posts ) )
		return $query;

	$attachment_query = new WP_Query(
		array(
			'post_type' => 'attachment',
			'posts_per_page' => -1,
			'post_status' => 'any',
			'post_parent__in' => $collections_query->posts,
			'fields' => 'ids'
		)
	);

	// Now that our query is finished, we re-add our function to pre_get_posts
	add_action( 'pre_get_posts', 'picu_exclude_collection_images_from_library' );

	// Exclude all found attachments from every query
	$query->query_vars['post__not_in'] = $attachment_query->posts;

	return $query;
}

add_action( 'pre_get_posts', 'picu_exclude_collection_images_from_library' );


/**
 * Fix the media count (dropdown)
 *
 * @since 0.5.0
 */
function picu_fix_media_count( $counts ) {

	global $pagenow;

	if ( 'upload.php' != $pagenow )
		return $counts;

	// Remove our action form pre_get_posts to avoid infinite loop
	// (WP_Query would also trigger pre_get_posts filter)
	remove_action( 'pre_get_posts', 'picu_exclude_collection_images_from_library' );

	// Query all collections (CPT 'picu_collection')
	$collections_query = new WP_Query(
		array(
			'post_type' => 'picu_collection',
			'post_status' => array( 'any', 'trash' ),
			'posts_per_page' => -1,
			'fields' => 'ids'
		)
	);

	if ( empty( $collections_query->posts ) )
		return $counts;

	$attachment_query = new WP_Query(
		array(
			'post_type' => 'attachment',
			'posts_per_page' => -1,
			'post_status' => 'any',
			'post_parent__in' => $collections_query->posts,
			'fields' => 'ids'
		)
	);

	// Now that our query is finished, we re-add our function to pre_get_posts
	add_action( 'pre_get_posts', 'picu_exclude_collection_images_from_library' );

	if ( ! empty( $counts->{'image/jpeg'} ) ) {
		$counts->{'image/jpeg'} = $counts->{'image/jpeg'} - $attachment_query->found_posts;
	}

	return $counts;
}

add_filter( 'wp_count_attachments', 'picu_fix_media_count' );


/**
 * Redirect picu attachment pages to homepage
 *
 * @since 1.1.0
 */

function picu_attachment_redirect() {
	global $post;

	if ( isset( $post->post_parent ) AND 'picu_collection' == get_post_type( $post->post_parent ) AND 'attachment' == $post->post_type ) {
		wp_redirect( get_bloginfo( 'url' ), 301 );
	}
}

add_action( 'template_redirect', 'picu_attachment_redirect' );


/**
 * Filter image upload directory
 *
 * @since 0.5.0
 */
function picu_custom_upload_dir( $path ) {

	// When uploading, the file gets sent to upload_async.php, so we need to take the $_POST query in order to be able to get the post ID we need.
	if ( ! isset( $_POST['post_id'] ) || $_POST['post_id'] < 0 )
		return $path;

	$post_id = $_POST['post_id'];
	$post_type = get_post_type( $post_id );

	// Check if we are uploading from the user-edit.php page.
	if ( $post_type == 'picu_collection' ) {

		// Define upload path (subdir from /uploads/)
		$customdir = '/picu/collections/' . $post_id;

		if ( !empty( $path['error'] ) ) {
			return $path;
		}

		$new_subdir = $customdir;
		$path['path'] = str_replace( $path['subdir'], $new_subdir, $path['path'] );
		$path['url'] = str_replace( $path['subdir'], $new_subdir, $path['url'] );
		$path['subdir'] = $new_subdir;

		return $path;

	} else {
		// We are not uploading from a collection, so go ahead with the default path
		return $path;
	}
}


function picu_upload_prefilter( $file ) {
	add_filter( 'upload_dir', 'picu_custom_upload_dir' );
	return $file;
}

add_filter( 'wp_handle_upload_prefilter', 'picu_upload_prefilter' );


function picu_upload_postfilter( $fileinfo ) {
	remove_filter( 'upload_dir', 'picu_custom_upload_dir' );
	return $fileinfo;
}

add_filter( 'wp_handle_upload', 'picu_upload_postfilter' );


/**
 * Register custom image sizes
 *
 * @since 0.5.0
 */
function picu_image_sizes() {

	add_image_size( 'picu-thumbnail', 180, 180, true );
	add_image_size( 'picu-small', 300, 300, false );

	// Large
	$picu_large_image_size = apply_filters( 'picu_large_image_size', array(
		'width' => 3000,
		'height' => 2000
	) );

	add_image_size( 'picu-large', $picu_large_image_size['width'], $picu_large_image_size['height'], false );

	// Sizes added here must be added to picu_image_sizes_filter(), too

}

add_action( 'init', 'picu_image_sizes' );


/**
 * Announce our custom image sizes
 *
 * They might now even be used in our custom media frame
 *
 * @since 0.8.0
 */
function picu_show_image_sizes($sizes) {
	$sizes['picu-small'] = __( 'picu Small', 'picu' );
	$sizes['picu-large'] = __( 'picu Large', 'picu' );

	return $sizes;
}

add_filter( 'image_size_names_choose', 'picu_show_image_sizes' );


/**
 * Only use our custom image sizes for picu collections
 *
 * @since 0.5.0
 */
function picu_custom_image_sizes_filter( $sizes ) {

	// Check if a 'post_id' is set and abort otherwise
	if ( !isset( $_REQUEST['post_id'] ) ) {
		return $sizes;
	}

	// Get the post type from the HTTP Request
	$post_type = get_post_type( $_REQUEST['post_id'] );

	if ( 'picu_collection' == $post_type ) {
		$sizes = array(
			'picu-small',
			'picu-large',
			'picu-thumbnail'
		);
	}

	$sizes = apply_filters( 'picu_intermediate_image_sizes', $sizes );

	return $sizes;

}

add_filter( 'intermediate_image_sizes', 'picu_custom_image_sizes_filter' );


/**
 * Prevent generation of our custom image sizes everywhere else
 *
 * @since 0.5.0
 */
function picu_default_image_sizes_filter( $sizes ) {

	// Check if a 'post_id' is set and abort otherwise
	if ( !isset( $_REQUEST['post_id'] ) ) {
		return $sizes;
	}

	// Get the post type from the HTTP request
	$post_type = get_post_type( $_REQUEST['post_id'] );

	if ( 'picu_collection' != $post_type ) {
		unset( $sizes['picu-thumbnail'] );
		unset( $sizes['picu-small'] );
		unset( $sizes['picu-large'] );
	}

	return $sizes;
}

add_filter( 'intermediate_image_sizes_advanced', 'picu_default_image_sizes_filter' );


/**
 * Add our own picu-thumbnail size as "thumbnail"
 * to the attachment metadata
 *
 * @since 0.5.0
 */
function picu_metadata_attachment( $metadata ) {

	// Check if a 'post_id' is set and abort otherwise
	if ( !isset( $_REQUEST['post_id'] ) ) {
		return $metadata;
	}

	// Get post type from the HTTP request
	$post_type = get_post_type( $_REQUEST['post_id'] );

	if ( 'picu_collection' == $post_type ) {
		$metadata['sizes']['thumbnail'] = array(
			'file' => $metadata['sizes']['picu-thumbnail']['file'],
			'width' => $metadata['sizes']['picu-thumbnail']['width'],
			'height' => $metadata['sizes']['picu-thumbnail']['height'],
			'mime-type' => $metadata['sizes']['picu-thumbnail']['mime-type']
		);
	}

	return $metadata;

}

add_filter( 'wp_generate_attachment_metadata', 'picu_metadata_attachment' );