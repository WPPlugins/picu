<?php
/**
 * Picu edit collection
 *
 * Add our custom metabox, which replaces the default publish actions
 * Also load our custom colleciton edit screen and all its fields
 *
 * @since 0.8.0
 * @package picu
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Register our metabox
 *
 * @since 0.3.0
 */
function picu_add_metabox() {
	add_meta_box(
		'picu-submit-metabox',
		__( 'Proofing Status', 'picu' ),
		'picu_collection_metabox',
		'picu_collection',
		'side',
		'high'
	);
}

add_action( 'add_meta_boxes', 'picu_add_metabox' );


/**
 * Construct the metabox
 *
 * @since 0.3.0
 */
function picu_collection_metabox( $post ) {

	// Add a nonce field so we can check for it later
	wp_nonce_field( 'picu_collection_metabox', 'picu_collection_metabox_nonce' );

	// Load meta infos
	$picu_collection_gallery_ids = get_post_meta( $post->ID, '_picu_collection_gallery_ids', true );
	$picu_collection_share_method = get_post_meta( $post->ID, '_picu_collection_share_method', true );

	// Load name of the photographer
	$user_name = get_the_author_meta( 'display_name', $post->post_author );

	// Get set and apporval times
	$sent_time = picu_get_collection_history_event_time( $post->ID, 'sent' );
	$approval_time = picu_get_collection_history_event_time( $post->ID, 'approved' );

	?>

	<div class="picu-submit-metabox-inside">
		<div class="proof-status">
			<span class="status create<?php if ( 'sent' == get_post_status() OR 'approved' == get_post_status() ) { echo ' done'; } else { echo ' active'; } ?>"><span class="status-label"><?php _e( 'Create collection', 'picu' ); ?></span></span>
			<span class="status waiting<?php if ( 'sent' == get_post_status() ) { echo ' active'; } elseif ( 'approved' == get_post_status() ) { echo ' done'; } ?>"><span class="status-label"><?php _e( 'Waiting for approval', 'picu' ); ?></span><?php if ( 'sent' == get_post_status() OR 'approved' == get_post_status() ) { echo '<span class="date">' . __( 'sent', 'picu' ) . ': ' . date( get_option( 'date_format' ), $sent_time ) . '</span>'; } ?></span>
			<span class="status approved<?php if ( 'approved' == get_post_status() ) { echo ' active'; } ?>"><span class="status-label"><?php _e( 'Approved', 'picu' ); ?></span> <?php if ( 'approved' == get_post_status() ) { echo '<span class="date">' . date( get_option( 'date_format' ), $approval_time ) . '</span>'; } ?></span>
		</div>
	</div>

	<div class="picu-post-options"><?php if ( 'approved' == get_post_status() OR (  'sent' == get_post_status() AND empty( $post->post_password ) ) ) {

	} elseif ( 'sent' == get_post_status() AND ! empty( $post->post_password ) ) { ?>
		<div class="picu-option-item">
			<a class="picu-option-password-protected" href="#picu-password"><span class="dashicons dashicons-lock"></span><?php _e( 'Show Password', 'picu' ); ?></a>
			<div id="picu-password" class="picu-option-content is-hidden">
				<input type="text" id="post_password" name="post_password" maxlength="20" value="<?php echo $post->post_password; ?>" disabled="disabled" />
			</div>
		</div>
	<?php } else { ?>
		<div class="picu-option-item">
			<a href="#picu-password"><span class="dashicons dashicons-<?php if ( empty( $post->post_password) ) { echo 'un'; } ?>lock"></span><?php _e( 'Password Protection', 'picu' ); ?></a>
			<div id="picu-password" class="picu-option-content is-hidden">
				<div class="picu-password-wrap">
					<label for="post_password"><?php _e( 'Enter Password', 'picu' ); ?>:</label> <input type="text" id="post_password" name="post_password" maxlength="20" value="<?php echo $post->post_password; ?>" />
					<a href="#" class="picu-remove-password js-picu-remove-password is-hidden"><?php _e( 'Empty Password Field', 'picu' ); ?></a>
				</div>
				<p class="picu-hint js-picu-password-hint-send<?php if ( 'picu-copy-link' == $picu_collection_share_method ) { echo ' is-hidden'; } ?>"><?php _e( 'The password will be sent to the client with the email.', 'picu' ); ?></p>
				<p class="picu-hint js-picu-password-hint-rememeber<?php if ( empty( $picu_collection_share_method ) OR 'picu-send-email' == $picu_collection_share_method ) { echo ' is-hidden'; } ?>"><?php _e( 'Don\'t forget to sent the password to your client!', 'picu' ); ?></p>
			</div>
		</div>
	<?php } ?></div><!-- .picu-post-options -->

	<div id="submitpost">
		<div id="major-publishing-actions">
			<?php
			if ( 'sent' == get_post_status() ) {
			?>
				<a class="button js-picu-edit" href="<?php print wp_nonce_url( admin_url( "post.php?post=" . $post->ID . "&action=edit" ), 'picu_collection_reopen_' . $post->ID, 'reopen' ); ?>"><?php _e( 'Edit Collection', 'picu' ); ?></a>
				<a class="button" href="<?php echo admin_url( 'post.php?picu_duplicate_collection=' . $post->ID ) ?>"><?php _e( 'Duplicate', 'picu' ); ?></a>

				<div class="picu-modal picu-warning js-picu-warning is-hidden">
					<div class="picu-modal-inner">
						<div class="picu-modal-content">
							<h3><?php _e( 'Caution!', 'picu' ); ?></h3><p><?php _e ( 'You already sent this collection to the client.', 'picu' ); ?></p><p><strong><?php _e ( 'Are you sure you want to make changes?', 'picu' ); ?></strong></p>
							<div class="picu-modal-actions">
								<a class="button button-primary" href="<?php print wp_nonce_url( admin_url( "post.php?post=" . $post->ID . "&action=edit" ), 'picu_collection_reopen_' . $post->ID, 'reopen' ); ?>"><?php _e( 'Yes, I am sure', 'picu' ); ?></a> <a class="button js-picu-cancel-modal" href="#"><?php _e( 'Cancel', 'picu' ); ?></a>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			// The wrapping #submitbox is needed for the submit buttons to work
			elseif ( 'approved' != get_post_status() ) {
			// Adds a submit button to save the collection without sending
			$other_attributes = array( 'id' => 'save-post' );
			submit_button( __( 'Save', 'picu' ), 'save-draft large', 'publish', false, $other_attributes );
			?>
			<span class="spinner"></span>
			<?php
			// Adds a submit button to publish and send the collection to the client
			$other_attributes = array( 'id' => 'publish' );
			submit_button( __( 'Send to Client', 'picu' ), 'primary large', 'picu_sendmail', false, $other_attributes );
			}
			else { ?>
				<a class="button" href="<?php echo admin_url( 'post.php?picu_duplicate_collection=' . $post->ID ) ?>"><?php _e( 'Duplicate', 'picu' ); ?></a>
			<?php }
			?>
			<div class="clear"></div>
		</div>
	</div>
<?php
}


/**
 * Construct main edit screen: media uploader and share options
 *
 * @since 0.8.0
 */
function picu_main_edit_screen( $post ) {

	if ( $post->post_type != 'picu_collection' ) {
		return;
	}

	// Load the IDs of all uploaded images into an array
	$gallery_data = get_post_meta( $post->ID, '_picu_collection_gallery_ids', true );
	$gallery_image_ids = explode( ',', $gallery_data );
	$gallery_image_count = count( $gallery_image_ids );

	// Load the IDs of the approved images in to an array
	$selection_data = get_post_meta( $post->ID, '_picu_collection_selection', true );

	if ( ! empty( $selection_data ) ) {
		$selection_image_ids = $selection_data['selection'];
		$selection_image_count = count( $selection_data['selection'] );
	}

	$gallery_class = '';
	if ( ! empty( $gallery_data ) ) {
		$gallery_class = ' picu-gallery-has-images';
	}
	if ( 10 < $gallery_image_count ) {
		$gallery_class .= ' is-collapsible js-collapsed';
	}

	// Prepare variable to hold our file names
	$img_filenames = '';
	// Get separator, first check if it is defined in a constant, then filter it
	$filename_separator = ( defined( 'PICU_FILENAME_SEPARATOR' ) ) ? PICU_FILENAME_SEPARATOR : ' ';
	$filename_separator = apply_filters( 'picu_filename_separator', $filename_separator );

	if ( ! empty( $selection_image_ids ) ) {
		// Loop through our IDs to get the file names
		foreach ( $selection_image_ids as $selection_image_id ) {
			// Load file paths to original files
			$img_files = wp_get_attachment_image_src( $selection_image_id, 'full' );
			// Get file names into our string, space-separated
			$img_filenames .= pathinfo( $img_files[0], PATHINFO_FILENAME ) . $filename_separator;
		}
		$img_filenames = trim( $img_filenames );
	}

	// Load meta infos
	$picu_collection_share_method = get_post_meta( $post->ID, '_picu_collection_share_method', true );
	$picu_collection_email_address = get_post_meta( $post->ID, '_picu_collection_email_address', true );
	$picu_collection_description = get_post_meta( $post->ID, '_picu_collection_description', true );
	$picu_collection_selection = get_post_meta( $post->ID, '_picu_collection_selection', false );
	$picu_collection_gallery_ids = get_post_meta( $post->ID, '_picu_collection_gallery_ids', true );

	// Load name of the photographer
	$user_name = get_the_author_meta( 'display_name', $post->post_author );

	/*
	 * Display approved view
	 */
	if ( 'approved' == get_post_status() ) {
	?>

	<div class="postbox picu-postbox picu-gallery-has-images">
		<div class="picu-postbox-inner">
			<h2><?php _e( 'Approval Summary', 'picu' ); ?></h2>
			<div class="picu-approval-info">
				<ul>
					<li><?php echo sprintf( __( '%s of %s images have been approved', 'picu' ), count( $picu_collection_selection[0]['selection'] ), count( explode( ',', $picu_collection_gallery_ids ) ) ); ?></li>
					<?php if ( $picu_collection_selection[0]['approval_message'] ) { ?>
					<li><?php _e( 'Your client added a message', 'picu' ); ?>:<br />
						<div class="picu-approval-message"><?php echo wpautop( $picu_collection_selection[0]['approval_message'] ); ?></div>
					</li>
					<?php } ?>
				</ul>
			</div>
			<div class="picu-toolbar">
				<div class="picu-view-switch">
					<a class="grid-view js-grid-view active" href="#grid"><?php _e( 'Grid View', 'picu' ); ?></a>
					<a class="list-view js-list-view" href="#list"><?php _e( 'List View', 'picu' ); ?></a>
				</div><!-- .picu-view-switch -->
				<div class="picu-filter">
					<label><?php _e( 'Show', 'picu' ); ?>:</label>
					<select>
						<option value="all"><?php _e( 'all', 'picu' ); ?></option>
						<option value="selected"><?php _e( 'approved', 'picu' ); ?></option>
						<option value="not-selected"><?php _e( 'not approved', 'picu' ); ?></option>
					</select>
				</div><!-- .picu-filter -->
				<div class="picu-copy">
					<!-- <a class="button button-primary js-copy-file-names" href="#" data-clip="<?php echo $img_filenames; ?>"><?php _e( 'Copy Filenames', 'picu' ); ?></a> -->
					<label for="picu-copy-filenames"><?php _e( 'Copy Filenames', 'picu' ); ?>:</label>
					<input id="picu-copy-filenames" type="text" value="<?php echo $img_filenames; ?>" />
					<span class="button button-primary picu-copy-to-clipboard" role="button" tabindex="0" data-clipboard-text="<?php echo $img_filenames; ?>"><?php _e( 'Copy Filenames', 'picu' ); ?></span>
				</div><!-- .picu-copy -->
			</div><!-- .picu-toolbar -->

			<table class="picu-selection-overview-table js-picu-selection-overview-table">
				<thead>
					<tr>
						<th><?php _e( 'Approved', 'picu' ); ?></th>
						<th><?php _e( 'Thumbnail', 'picu' ); ?></th>
						<th><?php _e( 'File', 'picu' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $gallery_image_ids as $gallery_image_id ) {
					$file = wp_get_attachment_image_src( $gallery_image_id, 'full' );
					$filename = pathinfo( $file[0], PATHINFO_BASENAME );
					$image = wp_get_attachment_image_src( $gallery_image_id, 'picu-small' );
					$selected = ( in_array( $gallery_image_id, $selection_image_ids ) ) ? ' class="selected"' : '';
					echo '<tr' . $selected . '><td>';
					echo '</td><td class="thumb">';
					echo '<img src="' . $image[0] . '" alt="' . $filename . '" />';
					echo '</td><td>';
					echo $filename;
					echo '</td></tr>'. PHP_EOL;
				}
				?>
				</tbody>
			</table>

			<div class="picu-gallery-thumbnails js-picu-selection-overview-grid">
			<?php
			// Loop through all uploaded images
			foreach ( $gallery_image_ids as $gallery_image_id ) {

				// Load filename
				$img_name = wp_get_attachment_image_src( $gallery_image_id, 'full' );

				// Define the attributes to output with our thumbnails
				$attr = array(
					'title' => basename( $img_name[0] ),
					'draggable' => 'false'
				);

				// Define a variable for CSS classes for approved images
				$approved_class = '';

				// Add the CSS class for approved imgs, when image is approved
				if ( ! empty( $selection_data ) ) {
					if ( in_array( $gallery_image_id, $selection_image_ids ) ) {
						$approved_class = 'class="picu-selection-approved"';
					}
				}

				// Construct the image markup
				echo '<figure ' . $approved_class . '>';
					echo '<div class="picu-gallery-thumbnail-box">';
						echo '<div class="picu-gallery-thumbnail-box-inner">';
							echo wp_get_attachment_image( $gallery_image_id, 'picu-small', 0, $attr );
						echo '</div>';
					echo '</div>';
				echo '</figure>';

			}
			?>
			</div><!-- .picu-gallery-thumbnails -->
		</div><!-- .picu-postbox-inner -->
	</div><!-- .postbox.picu-postbox -->
	<?php
	} // Display approved view

	/*
	 * Display sent view
	 */
	elseif ( 'sent' == get_post_status() ) {
	?>
	<div class="postbox picu-postbox <?php echo $gallery_class; ?>">
		<div class="picu-postbox-inner">
			<?php
			// Output the upload / dropzone if the collection wasn't sent already
			//if ( 'approved' != get_post_status() && 'sent' != get_post_status() AND $gallery_class != 'picu-gallery-has-images'  ) {
			?>
			<h2><?php _e( 'Collection', 'picu' ); ?></h2><?php

			if ( ! empty( $gallery_image_ids ) ) {
				echo '<div class="picu-gallery-thumbnails">';

				// Loop through all uploaded images
				foreach ( $gallery_image_ids as $gallery_image_id ) {

					// Load filename
					$img_name = wp_get_attachment_image_src( $gallery_image_id, 'full' );

					// Define the attributes to output with our thumbnails
					$attr = array(
						'title' => basename( $img_name[0] ),
						'draggable' => 'false'
					);

					// Define a variable for CSS classes for approved images
					$approved_class = '';

					// Add the CSS class for approved imgs, when image is approved
					if ( ! empty( $selection_data ) ) {
						if ( in_array( $gallery_image_id, $selection_image_ids ) ) {
							$approved_class = 'class="picu-selection-approved"';
						}
					}

					// Construct the image markup
					echo '<figure ' . $approved_class . '>';
					echo '<div class="picu-gallery-thumbnail-box">';
					echo '<div class="picu-gallery-thumbnail-box-inner">';
					echo wp_get_attachment_image( $gallery_image_id, 'picu-small', 0, $attr );
					echo '</div></div></figure>';
				}

				echo '</div><!-- .picu-gallery-thumbnails -->';
			}

			// Only show toggle, when there are more than 10 images
			if ( 10 < $gallery_image_count ) {
			echo '<div class="toggle-picu-gallery-height"><a href="#" class="js-toggle-picu-gallery-height"><span class="show">' . __( 'Show all images', 'picu' ) . '</span><span class="hide">' . __( 'Hide images', 'picu' ) . '</span></a></div><!-- .toggle-picu-gallery-height -->';
			}
		?>
		</div><!-- .picu-postbox-inner -->
	</div><!-- .postbox.picu-postbox -->

	<?php
		// Create options box
		$picu_collection_options = apply_filters( 'picu_collection_options', false );

		if ( is_array( $picu_collection_options ) AND 0 < count( $picu_collection_options ) ) {

			$temp = '';

			// Wrap options in divs
			foreach ( $picu_collection_options as $key => $option ) {
				$temp .= '<div class="picu-option-set" id="' . $key . '">' . $option . '</div><!-- .picu-option-set#' . $key . ' -->';
			}

			echo '<div class="picu-collection-options"><h2>' . __( 'Collection Options', 'picu' ) . '</h2>' . $temp . '</div><!-- .picu-collection-options -->';
		}
	?>

	<div class="picu-share-options">
		<h2><?php _e( 'Share Options', 'picu' ); ?></h2>
		<ul class="picu-share-select">
			<li><a<?php if ( empty( $picu_collection_share_method ) OR 'picu-send-email' == $picu_collection_share_method ) { echo ' class="active"'; } ?> href="#picu-send-email"><?php _e( 'Send via email', 'picu' ); ?></a></li>
			<li><a<?php if ( 'picu-copy-link' == $picu_collection_share_method ) { echo ' class="active"'; } ?> href="#picu-copy-link"><?php _e( 'Copy link &amp; send manually', 'picu' ); ?></a></li>
		</ul><!-- .picu-sahre-select -->
		<div class="picu-share-option<?php if ( empty( $picu_collection_share_method ) OR 'picu-send-email' == $picu_collection_share_method ) { echo ' is-active'; } ?>" id="picu-send-email">
			<?php $user_name = get_the_author_meta( 'display_name', $post->post_author ); ?>
			<p><label for="picu-collection-email-address" title="<?php _e( 'Enter your clients email address', 'picu' ); ?>"><?php _e( 'Client Email', 'picu' ); ?>:</label>
			<input type="email" id="picu-collection-email-address" name="picu_collection_email_address" value="<?php echo sanitize_email( $picu_collection_email_address ); ?>" disabled="disabled" /></p>

			<p><label for="picu-collection-description" title="<?php _e( 'The description will be sent to your client via email.', 'picu' ); ?>"><?php _e( 'Message', 'picu' ); ?>:</label>
			<textarea id="picu-collection-description" name="picu_collection_description" cols="60" rows="13" disabled="disabled"><?php if ( esc_attr( $picu_collection_description ) ) {
					echo esc_attr( $picu_collection_description );
				}
			?></textarea></p>
		</div><!-- .picu-share-option #picu-send-email -->
		<div class="picu-share-option<?php if ( 'picu-copy-link' == $picu_collection_share_method ) { echo ' is-active'; } ?>" id="picu-copy-link">
			<p><label><?php _e( 'Copy URL', 'picu' ); ?></label><input type="text" value="<?php echo get_the_permalink(); ?>" disabled="disabled" /></p>
			<p class="picu-hint"><?php _e( '<strong>Please note:</strong> picu will <strong>NOT</strong> send an email. Make sure to copy and send the link to your client manually.', 'picu' ); ?></p>
		</div><!-- picu-share-option #picu-copy-link -->
	</div><!-- .picu-share-options -->

		<?php
	} // Display sent view

	/*
	 * Display create/edit view
	 */
	else {
	?>
	<div class="postbox picu-postbox <?php echo $gallery_class; ?>">
		<div class="picu-postbox-inner">
			<?php
			// Output the upload / dropzone if the collection wasn't sent already
			//if ( 'approved' != get_post_status() && 'sent' != get_post_status() AND $gallery_class != 'picu-gallery-has-images'  ) {
				$picu_section_header_1 = __( 'Upload Images', 'picu' );
				$picu_section_header_1 = apply_filters( 'picu_section_header_1', $picu_section_header_1 );
			?>
			<h2><span class="stepcounter">1</span> <?php echo $picu_section_header_1; ?></h2>
			<?php
			// Define the drag & drop zone for our uploader

			if ( ! empty( $gallery_image_ids ) ) {
				echo '<div class="picu-gallery-thumbnails">';

				// Loop through all uploaded images
				foreach ( $gallery_image_ids as $gallery_image_id ) {

					// Load filename
					$img_name = wp_get_attachment_image_src( $gallery_image_id, 'full' );

					// Define the attributes to output with our thumbnails
					$attr = array(
						'title' => basename( $img_name[0] ),
						'draggable' => 'false'
					);

					// Define a variable for CSS classes for approved images
					$approved_class = '';

					// Add the CSS class for approved imgs, when image is approved
					if ( ! empty( $selection_data ) ) {
						if ( in_array( $gallery_image_id, $selection_image_ids ) ) {
							$approved_class = 'class="picu-selection-approved"';
						}
					}

					// Construct the image markup
					echo '<figure ' . $approved_class . '>';
					echo '<div class="picu-gallery-thumbnail-box">';
					echo '<div class="picu-gallery-thumbnail-box-inner">';
					echo wp_get_attachment_image( $gallery_image_id, 'picu-small', 0, $attr );
					echo '</div></div></figure>';
				}
				echo '</div><!-- .picu-gallery-thumbnails -->';
			}

			?>

			<div class="toggle-picu-gallery-height">
				<a href="#" class="js-toggle-picu-gallery-height"><span class="show"><?php _e( 'Show all images', 'picu' ); ?></span><span class="hide"><?php _e( 'Hide images', 'picu' ); ?></span></a>
			</div>

			<?php
			$picu_before_upload = '';
			$picu_before_upload = apply_filters( 'picu_before_upload', $picu_before_upload );
			echo $picu_before_upload;

			?>
			<div class="picu-gallery-uploader">
				<input type="text" id="picu-gallery-ids" name="picu_gallery_ids" class="hidden" value="<?php echo $gallery_data; ?>">
				<?php wp_nonce_field( 'picu_gallery_ids', 'picu_gallery_ids_nonce' ); ?>
				<p class="picu-drag-info"><?php _e( 'Drag and drop your images here or click the button to upload', 'picu' ); ?></p>
				<p><a class="button picu-upload-image-button" href="#"><?php _e( 'Upload / Edit Images', 'picu' ); ?></a></p>
				<p class="picu-max-file-size"><?php echo __( 'Maximum upload size', 'picu' ) . ': ' . size_format( wp_max_upload_size() ); ?> <a class="picu-help" href="https://picu.io/faq#maximum-upload-size" target="_blank"><?php _e( 'Help', 'picu' ); ?></a></p>
			</div><!-- .picu-gallery-uploader -->
		</div><!-- .picu-postbox-inner -->
	</div><!-- .postbox.picu-postbox -->
	<?php
		$step = 2;

		// Create options box
		$picu_collection_options = apply_filters( 'picu_collection_options', false );

		if ( is_array( $picu_collection_options ) AND 0 < count( $picu_collection_options ) ) {

			$temp = '';

			// Wrap options in divs
			foreach ( $picu_collection_options as $key => $option ) {
				$temp .= '<div class="picu-option-set" id="' . $key . '">' . $option . '</div><!-- .picu-option-set#' . $key . ' -->';
			}

			echo '<div class="picu-collection-options"><h2><span class="stepcounter">' . $step . '</span>' . __( 'Collection Options', 'picu' ) . '</h2>' . $temp . '</div><!-- .picu-collection-options -->';

			// Add one to the step number
			$step++;
		}
	?>

	<div class="picu-share-options">
		<h2><span class="stepcounter"><?php echo ( $step ) ?: '2'; ?></span> <?php _e( 'Share Options', 'picu' ); ?></h2>
		<ul class="picu-share-select js-picu-share-select">
			<li><a<?php if ( empty( $picu_collection_share_method ) OR 'picu-send-email' == $picu_collection_share_method ) { echo ' class="active"'; } ?> href="#picu-send-email"><?php _e( 'Send via email', 'picu' ); ?></a></li>
			<li><a<?php if ( 'picu-copy-link' == $picu_collection_share_method ) { echo ' class="active"'; } ?> href="#picu-copy-link"><?php _e( 'Copy link &amp; send manually', 'picu' ); ?></a></li>
		</ul>
		<input type="hidden" class="js-picu_collection_share_method" name="picu_collection_share_method" value="<?php if ( 'picu-copy-link' == $picu_collection_share_method ) { echo 'picu-copy-link'; } else { echo 'picu-send-email'; } ?>" />
		<div class="picu-share-option<?php if ( empty( $picu_collection_share_method ) OR 'picu-send-email' == $picu_collection_share_method ) { echo ' is-active'; } ?>" id="picu-send-email">
			<?php $user_name = get_the_author_meta( 'display_name', $post->post_author ); ?>
			<p><label for="picu-collection-email-address" title="<?php _e( 'Enter your clients email address', 'picu' ); ?>"><?php _e( 'Client Email', 'picu' ); ?>:</label>
				<input type="email" id="picu-collection-email-address" name="picu_collection_email_address" value="<?php echo sanitize_email( $picu_collection_email_address ); ?>" /></p>

			<p><label for="picu-collection-description" title="<?php _e( 'The description will be sent to your client via email.', 'picu' ); ?>"><?php _e( 'Message', 'picu' ); ?>:</label>
				<textarea id="picu-collection-description" name="picu_collection_description" cols="60" rows="13"><?php if ( esc_attr( $picu_collection_description ) ) {
						echo esc_attr( $picu_collection_description );
					} elseif ( 'auto-draft' == $post->post_status ) {
						$mail_message = sprintf( __( 'Dear Client,&#10;&#10;Please select the photos you like and send your selection back to us. We will start post-production as soon as we have your approval.&#10;&#10;Sincerely,&#10;%s', 'picu' ), $user_name );
						$mail_message = apply_filters( 'picu_client_mail_message', $mail_message, $user_name );
						echo $mail_message;
					}
				?></textarea></p>
		</div><!-- .picu-share-option #picu-send-email -->
		<div class="picu-share-option<?php if ( 'picu-copy-link' == $picu_collection_share_method ) { echo ' is-active'; } ?>" id="picu-copy-link">
				<p><label for="picu-collection-link"><?php _e( 'Copy URL', 'picu' ); ?></label><input type="text" id="picu-collection-link" name="picu-collection-link" value="<?php echo get_draft_permalink( $post->ID ); ?>" /></p>
				<p class="picu-hint"><?php _e( '<strong>Please note:</strong> picu will <strong>NOT</strong> send an email. Make sure to copy and send the link to your client manually.', 'picu' ); ?></p>
		</div><!-- .picu-share-option #picu-copy-link -->
	</div><!-- .picu-share-options -->
	<?php
	}
}
add_action( 'edit_form_after_title', 'picu_main_edit_screen' );


/**
 * picu media upload save functionality
 *
 * @since  0.4.0
 */
function picu_save_gallery_ids( $post_id ) {

	// Check if nonce is set
	if ( ! isset( $_POST['picu_gallery_ids_nonce'] ) )
		return $post_id;

	// Verify that the nonce is valid
	if ( ! wp_verify_nonce( $_POST['picu_gallery_ids_nonce'], 'picu_gallery_ids' ) )
		return $post_id;

	// If this is an autosave, our form has not been submitted, so we don't want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Check user permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Run import filter.
	// Should return true, if an import from another source occured.
	$import_done = apply_filters( 'picu_save_gallery_ids_filter', $post_id, $_REQUEST );

	// Only update gallery id meta, if import didn't happen
	if ( true !== $import_done ) {
		// Sanitize data and put the image ids into a variable
		$picu_gallery_ids = sanitize_text_field( $_POST['picu_gallery_ids'] );

		// Save the image ID's as custom post meta
		update_post_meta( $post_id, '_picu_collection_gallery_ids', $picu_gallery_ids );
	}

	// Check if a valid share method is chosen
	if ( ! isset( $_POST['picu_collection_share_method'] ) OR ( 'picu-send-email' != $_POST['picu_collection_share_method'] AND 'picu-copy-link' != $_POST['picu_collection_share_method'] ) ) {
		return $post_id;
	}
	elseif ( 'picu-send-email' == $_POST['picu_collection_share_method'] ) {

		// Save share method
		update_post_meta( $post_id, '_picu_collection_share_method', 'picu-send-email' );

		// Make sure we have a valid email address
		if ( is_email ( $_POST['picu_collection_email_address'] ) ) {

			$picu_collection_email_address = sanitize_email( $_POST['picu_collection_email_address'] );

			// Update the email address in the database
			update_post_meta( $post_id, '_picu_collection_email_address', $picu_collection_email_address );

		}

		// Clean up the collection description
		$picu_collection_description = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['picu_collection_description'] ) ) );

		if ( ! empty( $picu_collection_description ) ) {
			// Update the collection description in the database
			update_post_meta( $post_id, '_picu_collection_description', $picu_collection_description );
		}

	}
	else {
		update_post_meta( $post_id, '_picu_collection_share_method', 'picu-copy-link' );
	}
}

add_action( 'save_post_picu_collection', 'picu_save_gallery_ids' );


/**
 * Custom Function to re-open an already sent collection
 *
 * @since 0.3.0
 */
function picu_collection_reopen() {

	// Check if a "reopen" parameter (the nonce) was set with this request
	if ( isset( $_REQUEST['reopen'] ) AND isset( $_REQUEST['post'] ) ) {

		$post_id = sanitize_key( $_REQUEST['post'] );

		// If it is, save it in a variable
		$reopen_nonce = $_REQUEST['reopen'];

		// Verify the nonce to see if it is a legitimate request
		if ( ! wp_verify_nonce( $reopen_nonce, 'picu_collection_reopen_' . $post_id ) ) {

			die ( 'Security check failed!' );

		} else {

			// If it is, update the post status back to publish
			picu_update_post_status( $post_id, 'publish' );
			picu_update_collection_history( $post_id, 'reopened' );

		}
	}
}

add_action( 'wp_loaded', 'picu_collection_reopen' );


/**
 * Remove the default "post-submit" metabox
 *
 * @since 0.3.0
 */
function picu_remove_submit_metabox() {

	remove_meta_box( 'submitdiv', 'picu_collection', 'side' );

}
add_action( 'admin_menu', 'picu_remove_submit_metabox' );


/**
 * Replace regular "Edit Collection" title with actual title
 *
 */
function picu_replace_edit_screen_title() {
	global $post, $title, $action;

	if ( 'edit' == $action AND 'picu_collection' == $post->post_type AND ( 'approved' == $post->post_status OR 'sent' == $post->post_status ) ) {
		$title = $post->post_title;
	}
}

add_action( 'admin_head', 'picu_replace_edit_screen_title' );


/**
 * Duplicate collection
 *
 * @since 0.9.4
 *
 * @param Collection ID
 */
function picu_duplicate_collection( $id, $selected = false ) {

	// Get image ids and description from post meta
	$custom_meta = get_post_custom( $id );

	// Check if it is actually a collection, and if the required meta is available
	if ( 'picu_collection' == get_post_type( $id ) AND isset( $custom_meta['_picu_collection_gallery_ids'] ) ) {

		$gallery_ids = $custom_meta['_picu_collection_gallery_ids'];
		if ( isset( $custom_meta['_picu_collection_description'] ) ) {
			$collection_description = $custom_meta['_picu_collection_description'];
		}

		// Create new collection
		$arg = array(
			'post_type' => 'picu_collection'
		);
		$new_id = wp_insert_post( $arg );
	}

	// If we succesfully created the new collection...
	if ( isset( $new_id ) AND ! empty( $new_id ) ) {

		// Add the description to the new collection
		if ( isset( $collection_description ) AND is_array( $collection_description ) ) {
			add_post_meta( $new_id, '_picu_collection_description', $collection_description[0] );
		}

		// Prepare Copying / duplicating images
		$upload_dir = wp_upload_dir();
		$collection_image_dir = trailingslashit( $upload_dir['basedir'] ) . 'picu/collections/' . $id;
		$new_collection_image_dir = trailingslashit( $upload_dir['basedir'] ) . 'picu/collections/' . $new_id;

		if ( ! is_dir( $new_collection_image_dir ) ) {
			mkdir( $new_collection_image_dir, 0755 );
		}

		if ( true == $selected ) {
			$selection = get_post_meta( $id, '_picu_collection_selection' );
			$image_ids = $selection[0]['selection'];
		}
		else {
			$image_ids = explode( ',', $gallery_ids[0] );
		}

		// Load images
		$images = get_posts( array(
			'include' => $image_ids,
			'post_status' => 'any',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'order' => 'ASC'
		) );

		$new_gallery_ids = array();

		foreach( $images as $image ) {

			// Get file name
			$image_name = pathinfo( $image->guid );
			$image_name = $image_name['basename'];

			// Get meta data from original file
			$original_meta = wp_get_attachment_metadata( $image->ID );

			// Copy original file
			if ( copy( $image->guid, $new_collection_image_dir . '/' . $image_name ) ) {

				// Copy other available sizes as well
				foreach( $original_meta['sizes'] as $thumbnail ) {
					copy( $collection_image_dir . '/' . $thumbnail['file'], $new_collection_image_dir . '/' . $thumbnail['file'] );
				}

				// Prepare file attachment
				$filetype = wp_check_filetype( basename( $image_name ), null );

				$attachment = array(
					'guid'           => $new_collection_image_dir . '/' . $image_name,
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $image_name ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				// Prevent new thumbnails from being created during this process
				add_filter( 'intermediate_image_sizes_advanced', function() { return ''; } );

				// Insert file as new attachment
				$attach_id = wp_insert_attachment( $attachment, $new_collection_image_dir . '/' . $image_name, $new_id );

				// wp_generate_attachment_metadata() depends on this file
				require_once( ABSPATH . 'wp-admin/includes/image.php' );

				// Generate the metadata for the new attachment
				$attach_data = wp_generate_attachment_metadata( $attach_id, $new_collection_image_dir . '/' . $image_name );

				// Use the sizes from the original file
				$attach_data['sizes'] = $original_meta['sizes'];

				// Update attachment meta data
				wp_update_attachment_metadata( $attach_id, $attach_data );

				// Add new attachment id to our array
				$new_gallery_ids[] = $attach_id;

			}
		}

		// Create string with image IDs
		$new_gallery_ids = implode( ',', $new_gallery_ids );

		// Save image IDs for our new collection
		add_post_meta( $new_id, '_picu_collection_gallery_ids', $new_gallery_ids );

		// Redirect to newly created collection edit screen
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
		exit;
	}
	else {
		// Redirect to picu collection overview, set parameter to show error
		wp_redirect( admin_url( 'edit.php?post_type=picu_collection&picu=duplication-error' ) );
		exit;
	}
}

// Run the collection function, if the correct parameter is set
if ( isset( $_REQUEST['picu_duplicate_collection'] ) AND ! empty( $_REQUEST['picu_duplicate_collection'] ) ) {
	if ( isset( $_REQUEST['picu_duplicate_collection_selected'] ) AND '1' == $_REQUEST['picu_duplicate_collection_selected'] ) {
		picu_duplicate_collection( absint( $_REQUEST['picu_duplicate_collection'] ), true );
	}
	else {
		picu_duplicate_collection( absint( $_REQUEST['picu_duplicate_collection'] ) );
	}
}

// Display error message, if duplication failed
if ( isset( $_REQUEST['picu'] ) AND 'duplication-error' == $_REQUEST['picu'] ) {
	function picu_duplication_error_notice() { ?>
		<div class="error notice is-dismissible">
			<p><?php _e( 'Duplication failed. Please try again.', 'picu' ); ?></p>
		</div>
	<?php }

	add_action( 'admin_notices', 'picu_duplication_error_notice' );
}


/**
 * Add duplicate as row action item
 *
 * @since 0.9.4
 */
function picu_add_duplicate_link( $actions, $post ) {

	if ( 'picu_collection' == $post->post_type ) {
		$actions['picu_duplication'] = '<a href="' . admin_url( 'post.php?picu_duplicate_collection=' . $post->ID ) . '">' . __( 'Duplicate', 'picu' ) . '</a>';
	}

	return $actions;
}

add_filter( 'post_row_actions', 'picu_add_duplicate_link', 10, 2 );