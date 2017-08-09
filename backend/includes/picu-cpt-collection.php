<?php
/**
 * Custom Post Type "Collections"
 *
 * @since 0.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Register Custom Post Type 'picu_collection'
 *
 * @since 0.1.0
 */
function picu_register_cpt_collection() {

	$labels = array(
		'name' => __( 'Collections', 'picu' ),
		'singular_name' => __( 'Collection', 'picu' ),
		'add_new' => __( 'New Collection', 'picu' ),
		'add_new_item' => __( 'New Collection', 'picu' ),
		'edit_item' => __( 'Edit Collection', 'picu' ),
		'new_item' => __( 'New Collection', 'picu' ),
		'view_item' => __( 'View Collection', 'picu' ),
		'search_items' => __( 'Search Collections', 'picu' ),
		'not_found' => __( 'No Collection Found', 'picu' ),
		'not_found_in_trash' => __( 'No Collection Found in Trash', 'picu' ),
		'parent_item_colon' => __( 'Parent Collection', 'picu' ),
		'menu_name' => __( 'All Collections', 'picu' ),
		'filter_items_list' => __( 'Filter collections list', 'picu' ),
		'items_list_navigation' => __( 'Collections list navigation', 'picu' ),
		'items_list' => __( 'Collections list', 'picu' )
	);

	$picu_collection_slug = apply_filters( 'picu_collection_slug', _x( 'collections', 'picu collections slug', 'picu' ) );

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'supports' => array( 'title' ),
		'public' => true,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_in_menu'  => 'picu',
		'menu_position' => 1,
		'publicly_queryable' => true,
		'exclude_from_search' => true,
		'has_archive' => false,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => array(
			'slug' => $picu_collection_slug,
			'with_front' => false
		),
		'capabilities' => array(
			'edit_post'          => picu_capability(),
			'read_post'          => picu_capability(),
			'delete_post'        => picu_capability(),
			'edit_posts'         => picu_capability(),
			'edit_others_posts'  => picu_capability(),
			'delete_posts'       => picu_capability(),
			'publish_posts'      => picu_capability(),
			'read_private_posts' => picu_capability()
),
	);

	register_post_type( 'picu_collection', $args );

}

add_action( 'init', 'picu_register_cpt_collection' );


/**
 * Register custom post statuses
 *
 * @since 0.1.0
 */
function picu_collection_post_status() {

	register_post_status( 'sent', array(
		'label' => _x( 'Sent', 'post status name', 'picu' ),
		'public' => true,
		'exclude_from_search' => false,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'label_count' => _n_noop( 'Sent <span class="count">(%s)</span>', 'Sent <span class="count">(%s)</span>', 'picu' ),
	) );

	register_post_status( 'approved', array(
		'label' => _x( 'Approved', 'post status name', 'picu' ),
		'public' => true,
		'exclude_from_search' => false,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'label_count' => _n_noop( 'Approved <span class="count">(%s)</span>', 'Approved <span class="count">(%s)</span>', 'picu' ),
	) );
}

add_action( 'init', 'picu_collection_post_status' );


/**
 * Add a unique post slug to new collections
 *
 * @since 0.1.0
 */
function picu_add_unique_post_slug( $slug, $post_ID, $post_status, $post_type ) {

	// We only want our hashed slugs for post-type "collection"
	if ( $post_type == 'picu_collection' ) {

		// Load the post object into a variable
		$post = get_post( $post_ID );

		// Abort if post is saved automatically
		if ( $post_status == 'auto-draft' ) {
			return $slug;
		}

		// Check if post_name is either empty or different than the slug
		if ( empty( $post->post_name ) ) {
			// Generate a custom slug (md5 hash from the post id)
			$slug = substr( md5( $post_ID ), 0, 5 );
		}

	}
	return $slug;

}
add_filter( 'wp_unique_post_slug', 'picu_add_unique_post_slug', 10, 4 );


/**
 * Get our custom link to a collection, even before it is saved for the first time
 *
 * @return a string with the full url to a collection
 */
function get_draft_permalink( $post_id ) {

	// Load sample permalink and post-name into separate variables
	list( $permalink, $postname ) = get_sample_permalink( $post_id );

	// Check if post-name has not been set yet
	if ( empty( $postname ) ) {
		// Generate the post-name that will be used later on
		$postname = substr( md5( $post_id ), 0, 5 );
	}

	// Replace the placeholder in our URL
	return str_replace( '%pagename%', $postname, $permalink );

}


/**
 * Add the collection status to the collection list view
 *
 * @since 0.3.2
 */
function picu_collection_add_status_admin_column( $columns ) {

	// Add our custom columns to the default $columns
	$columns['selection'] = _x( 'Selection', 'column header', 'picu' );
	$columns['approval_status'] = _x( 'Status', 'column header', 'picu' );

	// Return all columns including our custom column
	return $columns;
}

add_filter( 'manage_picu_collection_posts_columns', 'picu_collection_add_status_admin_column', 10 );


/**
 * Add content to our admin column
 *
 * @since 0.3.2
 */
function picu_column_collection_status( $column, $post_id ) {

	// Check that we are in the right column for approval status
	if ( $column == 'approval_status' ) {
		$post_status = get_post_status();

		// Status URL, which will be linked to from the column
		$status_url = add_query_arg( 'post_status', $post_status );

		// Define the status messages for our column
		// depending on the status of each collection
		switch ( $post_status ) {
			case 'approved':
				$title_description = __( 'This collection has been approved.', 'picu' );
				$status_class = 'picu-admin-status-approved';
				break;
			case 'sent':
				$title_description = __( 'This collection has been sent to the client for approval.', 'picu' );
				$status_class = 'picu-admin-status-sent';
				break;
			case 'publish':
				$title_description = __( 'This collection has been published but not yet sent to the client.', 'picu' );
				$status_class = 'picu-admin-status-publish';
				break;
			case 'draft':
				$title_description = __( 'This collection is a draft, which means it cannot be publicly accessed', 'picu' );
				$status_class = 'picu-admin-status-draft';
				break;
			default:
				$title_description = __( 'This collection is in the trash. You can either restore or permanently delete it.', 'picu' );
				$status_class = 'picu-admin-status-publish';
		}

		// Construct the final output
		echo '<a href="'.$status_url.'" class="picu-admin-status '.$status_class.'" title="'.$title_description.'"></a>';

	}

	if ( $column == 'selection' ) {
		$total_images = get_post_meta( $post_id, '_picu_collection_gallery_ids', true );
		$selected = get_post_meta( $post_id, '_picu_collection_selection', false );
		if ( isset( $selected[0]['selection'] ) ) { echo count( $selected[0]['selection'] ); } else { echo '0'; }
		echo ' / ' . count( explode( ',', $total_images ) );
	}
}

add_action( 'manage_picu_collection_posts_custom_column' , 'picu_column_collection_status', 10, 2 );


/**
 * Remove "Proteced" prefix from password protected collection title
 *
 * @since 0.9.0
 */
function picu_remove_protected_prefix( $title_text ) {
	global $post;
	if ( 'picu_collection' == $post->post_type ) {
		return '%s';
	}
	else {
		return $title_text;
	}
}

add_filter( 'protected_title_format', 'picu_remove_protected_prefix' );


