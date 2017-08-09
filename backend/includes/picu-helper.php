<?php
/**
 * Picu Helper functions
 *
 * @since 0.5.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Add additional body classes for admin screens
 *
 * @since 0.3.2
 */
function picu_admin_body_class( $admin_body_class ) {

	// Get current admin screen
	$current_screen = get_current_screen();

	// Check if we are on a 'post.php' page
	if ( $current_screen->base == 'post' ) {
		// Add new class to the array;
		$admin_body_class .= ' post-status-' . get_post_status();
	}

	// return the array
	return $admin_body_class;

}

add_filter( 'admin_body_class', 'picu_admin_body_class' );


/**
 * Change the post_status
 *
 * @since 0.3.0
 */
function picu_update_post_status( $post_id, $status ) {

	if ( $status != 'sent' && $status != 'approved' && $status != 'publish' ) {
		return $post_id;
	}

	$post_contents = array(
		'ID' => $post_id,
		'post_status' => $status
	);

	// Remove our mail and save function so we don't get trapped in a loop
	remove_action( 'save_post_picu_collection', 'picu_collection_send_mail' );

	// Update the post, which calls save_post again
	wp_update_post( $post_contents );

	// Re-add the function for mail and save after save_post has fired
	add_action( 'save_post_picu_collection', 'picu_collection_send_mail' );

}


/**
 * Send email to client and update collection history
 * This function will be used from the backend (photographer's view)
 *
 * @since 0.1.0
 */
function picu_collection_send_mail( $post_id, $post ) {

	// Only go ahead if our button was clicked
	if ( ! isset( $_POST['picu_sendmail'] ) )
		return $post_id;

	// Check if nonce is set
	if ( ! isset( $_POST['picu_collection_metabox_nonce'] ) )
		return $post_id;

	// Verify nonce
	if ( ! wp_verify_nonce( $_POST['picu_collection_metabox_nonce'], 'picu_collection_metabox' ) )
		return $post_id;

	// Abort if no title is set
	if ( ! $post->post_title )
		return $post_id;

	// Abort if there are no photos
	if ( empty( $_REQUEST['picu_gallery_ids'] ) )
		return $post_id;

	// Abort if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	// Abort if the user doesn't have permissions
	if ( ! current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	// Abort sending if there are error notifications
	$notifications = get_option( '_' . get_current_user_id() . '_picu_notifications' );

	if ( is_array( $notifications ) ) {
		foreach( $notifications as $notification ) {
			if ( strpos( $notification['type'], 'error' ) )  {
				return $post_id;
			}
		}
	}

	// Only send mail, if this method is selected
	if ( 'picu-send-email' == $_POST['picu_collection_share_method'] ) {

		// Abort if no email address is set
		if ( empty( $_REQUEST['picu_collection_email_address'] ) )
			return $post_id;

		// Abort if no description is set
		if ( empty( $_REQUEST['picu_collection_description'] ) )
			return $post_id;

		// Get recipient
		$email['recipient'] = sanitize_email( $_POST['picu_collection_email_address'] );

		// Get Subject
		$email['subject'] = get_the_title( $post_id );

		// Get collection URL
		$url = get_draft_permalink( $post_id );

		// Get reply-to information
		$author_email = get_the_author_meta( 'user_email', $post->post_author );
		$email['reply'] = get_the_author_meta( 'display_name', $post->post_author ) . ' <' . $author_email . '>';
		$email['cc'] = $author_email;

		// Compose mail message
		$mail_content['title'] = $email['subject'];
		$mail_content['url'] = $url;
		$mail_content['user_email'] = $author_email;
		$mail_content['button_text'] = __( 'View Images', 'picu' );
		$mail_content['footer_notice'] = __( 'Not sure why your received this Email?<br>Please <a href="mailto:{user_email}?Subject=Re: {title}">let us know</a>.', 'picu' );

		// Get the description the photographer has entered
		$description = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", stripslashes( $_POST['picu_collection_description'] ) ) ) );

		if ( empty( $description ) ) { // Define default Text
			$mail_content['message_text'] = sprintf( __( "Dear Client,\n\nFind the images of our photo-shoot \"%s\" here:\n%s\n\nPlease select the ones you like and send your selection back to us.\nWe will start post-production as soon as we have your approval.\n\nSincerely,\n%s", 'picu' ), $email['title'], $url, $user_name  );
		} else { // Use entered description if there is one
			$mail_content['message_text'] = $description;
		}

		// Combine the mail content with the email template
		$email['content'] = picu_get_email_template( $mail_content );

		// Send mail and check if sending is successful
		if ( picu_send_email( $email ) == true ) {

			// Set the post status to "sent"
			picu_update_post_status( $post_id, 'sent' );

			// Update collection history
			picu_update_collection_history( $post_id, 'sent', $email['recipient'] );

			// Add success notification
			picu_add_notification( 'picu_mail_sent', 'notice notice-success is-dismissible', __( 'The collection was sent to the client.', 'picu' ) );

		} else {
			// If an error occured, save that in the collection history
			picu_update_collection_history( $post_id, 'error', 'email sending error' );

			// Add error notification
			picu_add_notification( 'picu_mail_error', 'notice notice-error is-dismissible', __( 'There was an error sending the email.', 'picu' ) );
		}

	}
	else {
		// Set the post status to "sent"
		picu_update_post_status( $post_id, 'sent' );

		// Update collection history
		picu_update_collection_history( $post_id, 'sent', 'manually' );

		// Add success notification
		picu_add_notification( 'picu_mail_sent', 'notice notice-success is-dismissible', __( 'The collection is ready! Make sure to send the link to your client:', 'picu' ) . ' <input type="text" value="' . get_draft_permalink( $post_id ) . '" />' );
	}
}

add_action( 'save_post_picu_collection', 'picu_collection_send_mail', 10, 2 );


/**
 * Send email to photographer when collection has been approved
 *
 * @since 0.7.1
 */
function picu_send_approval_mail( $post_id, $approval_message = '' ) {

	// Get post object
	$post = get_post( $post_id );

	// Send mail to the collection author
	$email['recipient'] = get_the_author_meta( 'user_email', $post->post_author );

	// Subject is...
	$email['subject'] = sprintf( __( 'Collection "%s" approved' , 'picu' ), sanitize_text_field( $post->post_title ) );

	// Get reply-to information
	$client_email = get_post_meta( $post_id, '_picu_collection_email_address', true );
	if ( is_email ( $client_email ) ) {
		$email['reply'] = $client_email;
	}

	// Compose message
	$url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );

	$message = sprintf( __( "Your collection \"%s\" has been approved.\n\n", 'picu' ), sanitize_text_field( $post->post_title ) );

	// Add approval message, if there is one
	if ( ! empty( $approval_message ) ) {
		$message .= sprintf( __( "Your client has added the following message:\n%s\n\n", 'picu' ), $approval_message );
	}

	$mail_content['title'] = $email['subject'];
	$mail_content['url'] = $url;
	$mail_content['button_text'] = __( 'View Selection', 'picu' );
	$mail_content['message_text'] = $message;

	$email['content'] = picu_get_email_template( $mail_content );

	if ( picu_send_email( $email ) === true ) {
		// Update collection history
		picu_update_collection_history( $post_id, 'approved' );
	}
	else {
		// Update collection history
		picu_update_collection_history( $post_id, 'error', 'approval error' );
	}


}


/**
 * One function to send all picu emails
 *
 * @since 0.9.4
 *
 * @param $email - array with options
 * $email['recipient'] - email address
 * $email['subject'] - string
 * $email['content'] - email body
 * $email['cc'] = email address
 * $email['reply'] - email address
 *
 * Return true, when email was sent
 */
function picu_send_email( $email ) {

	// Check if we want to sent HTML emails
	$options = get_option( 'picu_settings' );

	if ( isset( $options['send_html_mails'] ) AND 'on' != $options['send_html_mails'] ) {
		$email['headers'][] = "Content-Type: text/plain; charset=UTF-8";
	} else {
		$email['headers'][] = "Content-Type: text/html; charset=UTF-8";
	}

	// Set "CC" header
	if ( isset( $email['cc'] ) AND is_email( $email['cc'] ) ) {
		$email['headers'][] = "CC: " . $email['cc'] . "\r\n";
	}
	// Set "From" header
	$blog_url = parse_url( get_bloginfo( 'url' ) );
	$blog_url = $blog_url['host'];
	$email['headers'][] = "From: " . get_bloginfo( 'name' ) . " <no-reply@" . $blog_url . ">\r\n";

	// Set "Reply-To" header
	if ( isset( $email['reply'] ) AND ! empty( $email['reply'] ) ) {
		$email['headers'][] = "Reply-To: " . $email['reply'] . "\r\n";
	}

	// Remove html entities from subject
	$email['subject'] = html_entity_decode( $email['subject'], ENT_QUOTES, 'UTF-8' );

	// Sent email
	if ( wp_mail( $email['recipient'], $email['subject'], $email['content'], $email['headers'] ) == true ) {
		return true;
	}

	return false;

}


/**
 * Update picu collection history
 *
 * @since 0.9.4
 *
 * @param $post_id
 * @param $event, string - sent, reopened, approved
 * @param $data, string or array - additional data
 */

function picu_update_collection_history( $post_id, $event, $data = NULL ) {

	// Load existing history
	$existing_history = get_post_meta( $post_id, '_picu_collection_history' );

	// Create new history array
	$time = time();
	$new_history["$time"] = array(
		'event' => $event,
		'data' => $data
	);

	// Merge arrays
	if ( is_array( $existing_history ) AND 0 < count( $existing_history ) ) {
		$history = $existing_history[0] + $new_history;
	}
	else {
		$history = $new_history;
	}

	// Save updated history
	update_post_meta( $post_id, '_picu_collection_history', $history );
}


/**
 * Get picu collection history event time
 *
 * @since 0.9.4
 *
 * @param $post_id
 * @param $event, string - sent, reopened, approved
 */
function picu_get_collection_history_event_time( $post_id, $event ) {

	$picu_collection_history = get_post_meta( $post_id, '_picu_collection_history', false );

	// Check if history exists and if it contains anything
	if ( is_array( $picu_collection_history ) AND 0 < count( $picu_collection_history ) ) {

		// Get timestamps
		$keys = array_keys( $picu_collection_history[0] );

		// Check at which timestamp our event existing_history
		foreach( $picu_collection_history[0] as $key => $temp ) {

			// Check all events, get the most recent one
			if ( isset( $temp['event'] ) AND $event == $temp['event'] ) {
				$time = $key; // The final time will be the last $event in the history
			}
		}

		// Check if it is a valid timestamp
		if ( isset( $time ) AND is_numeric( $time ) ) {
			return $time;
		}
	}

	return false;
}
