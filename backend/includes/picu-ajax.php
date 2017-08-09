<?php
/**
 * Save selection from the client as post meta data
 * This function will be called from the front end (client view)
 *
 * @since 0.4.0
 *
 * @return string
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function picu_send_selection() {

	// Nonce check!
	if ( ! check_ajax_referer( 'picu-ajax-security', 'security', false ) ) {
		$return = array(
			'message' => __( '<strong>Error:</strong> Nonce check failed.<br />Refresh your browser window.', 'picu' ),
			'button_text' => __( 'OK', 'picu' )
		);
		wp_send_json_error( $return );
		exit;
	}

	// Sanitize and validate post id
	$postid = sanitize_key( $_POST['postid'] );
	// Does this collection exist?
	if ( ! is_string( get_post_status( $postid ) ) ) {
		$return = array(
			'message' => __( 'Error: Post id is not set.', 'picu' ),
			'button_text' => __( 'OK', 'picu' )
		);
		wp_send_json_error( $return );
		exit;
	}

	// Check post status / collection is already approved
	$poststatus = get_post_status( $postid );
	// Post status needs to be 'publish' or 'sent'. In all other cases, a selection may not be saved
	if ( 'publish' != $poststatus AND 'sent' != $poststatus ) {
		$return = array(
			'message' => __( 'Error: Collection is already approved.', 'picu' ),
			'button_text' => __( 'OK', 'picu' )
		);
		wp_send_json_error( $return );
		exit;
	}

	// Sanitize selection
	if ( isset( $_POST['selection'] ) AND ! empty( $_POST['selection'] ) ) {
		$temp = $_POST['selection'];
		$selection = array();
		foreach ( $temp as $id ) {
			// Ids must be integer values, handing them over as strings
			$selection[] = strval( intval( $id ) );
		}
	}
	else {
		$selection = false;
	}

	// Sanitize approval message
	if ( isset( $_POST['intent'] ) AND $_POST['intent'] == 'approve' ) {
		$approval_message = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", stripslashes( $_POST['approval_message'] ) ) ) );
	}
	// If intent is not set, we make a temporary save
	else {
		$approval_message = 'temporary save';
	}

	// Prepare array, that is saved as post meta
	$save = array(
		'selection' => $selection,
		'approval_message' => $approval_message,
		'time' => time()
	);

	// Save selection, send result back to the user
	if ( is_array( $selection ) AND count( $selection ) > 0 ) {

		// Use add instead of update to create multiple entries (make multiple clients possible)
		$response = update_post_meta( $postid, '_picu_collection_selection', $save );

		if ( $response >= 1 ) {

			// Switch collection status to "approved"
			if ( isset( $_POST['intent'] ) AND $_POST['intent'] == 'approve' ) {

				// Set the post status to "approved". (Do not set to approved when allowing multiple clients.)
				picu_update_post_status( $postid, 'approved' );

				// Inform the photographer about the approval
				picu_send_approval_mail( $postid, $approval_message );

			}

			$return = array(
				'message' => __( 'Your selection was saved.', 'picu' ),
				'button_text' => __( 'OK', 'picu' )
			);
			wp_send_json_success( $return );

		}
		else {

			$return = array(
				'message' => __( 'Error. Your selection could not be saved.', 'picu' ),
				'button_text' => __( 'OK', 'picu' )
			);
			wp_send_json_error( $return );

		}

	}
	else {

		$return = array(
			'message' => __( 'Please select at least one image.', 'picu' ),
			'button_text' => __( 'OK', 'picu' )
		);
		wp_send_json_error( $return );
	}

	exit;
}

add_action( 'wp_ajax_picu_send_selection', 'picu_send_selection' );
add_action( 'wp_ajax_nopriv_picu_send_selection', 'picu_send_selection' );