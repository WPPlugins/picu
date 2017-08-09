<?php
/**
 * Picu add-ons page
 *
 * @since 0.7.0
 * @package picu
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function picu_load_add_ons_page() {
?>
<div class="wrap picu-add-ons-wrap">

	<h1>Add-Ons &amp; Licenses</h1>
	<hr class="wp-header-end" />

<?php

	// Load and display notifications
	$notifications = get_option( '_' . get_current_user_id() . '_picu_notifications' );

	if ( isset( $notifications ) AND is_array( $notifications ) ) {
		foreach( $notifications as $notification ) {
			echo '<div class="' . $notification['type'] . '"><p>' . $notification['message'] . '</p></div>';
		}

		// Delete notifications
		delete_option( '_' . get_current_user_id() . '_picu_notifications' );
	}

	// Load installed picu add-ons that need activation
	$picu_addons = array();
	$picu_addons = apply_filters( 'picu_addons', $picu_addons );

	// Load licensing information
	$licenses = get_option( 'picu_addon_licenses' );

?>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active is-active" href="#picu-add-ons-tab"><?php _e( 'Add-Ons', 'picu' ); ?></a>
		<?php
			// If an add-on exists, we add the license tab
			if ( count( $picu_addons ) > 0 ) { ?>
			<a class="nav-tab" href="#picu-licenses-tab"><?php _e( 'Licenses', 'picu' ); ?></a>
		<?php } ?>
	</h2>

	<div class="picu-tab-content js-picu-tab-content is-active" id="picu-add-ons-tab">
		<p><strong><?php _e( 'Extend picu\'s functionality by using our awesome add-ons', 'picu' ); ?>:</strong></p>
		<?php
			// Get add-ons, that have registered with our hook earlier
			$picu_addons = array();
			$picu_addons = apply_filters( 'picu_addons', $picu_addons );
		?>
		<div class="picu-add-ons">

			<div class="picu-add-on">
				<div class="picu-add-on-inner js-picu-add-on-inner">
					<h2><?php _e( 'Brand &amp; Customize', 'picu' ); ?></h2>
					<div class="picu-add-on-description">
						<p><?php _e( 'The <strong>Brand &amp; Customize</strong> add-on enables you to customize the overall appearance of your collections. Add your logo, adjust the color scheme, select a font that fits your brand and much more.', 'picu' ); ?></p>
						<p style="text-align: center;">
							<?php
							if ( ! array_key_exists( 'picu_brand_customize', $picu_addons ) ) {
								echo '<a class="button" href="https://picu.io/add-ons/brand-and-customize/">' . sprintf( __( 'Get %s', 'picu' ), __( 'Brand &amp; Customize', 'picu' ) ) . '</a>';
							}
							elseif( array_key_exists( 'picu_brand_customize', $picu_addons ) AND isset(  $licenses['picu_brand_customize_license_status'] ) AND 'valid' == $licenses['picu_brand_customize_license_status'] ) {
								echo '<span class="picu-active">' . __( 'Active', 'picu' ) . '</span>';
							}
							else {
								echo '<a class="button js-tab-switch" href="#picu-licenses-tab">' . __( 'Activate License', 'picu' ) . '</a>';
							} ?>
						</p>
					</div>
				</div>
			</div><!-- .picu-add-on -->

			<div class="picu-add-on">
				<div class="picu-add-on-inner js-picu-add-on-inner">
					<h2><?php _e( 'Import', 'picu' ); ?></h2>
					<div class="picu-add-on-description">
						<p><?php _e( 'Dealing with large collections that contain lots of images? The <strong>Import</strong> add-on enables you to import images directly from a folder on you webserver. Just upload images via ftp and save precious time.', 'picu' ); ?></p>
						<p style="text-align: center;">
							<?php
							if ( ! array_key_exists( 'picu_import', $picu_addons ) ) {
								echo '<a class="button" href="https://picu.io/add-ons/import/">' . sprintf( __( 'Get %s', 'picu' ), __( 'Import', 'picu' ) ) . '</a>';
							}
							elseif( array_key_exists( 'picu_import', $picu_addons ) AND isset(  $licenses['picu_import_license_status'] ) AND 'valid' == $licenses['picu_import_license_status'] ) {
								echo '<span class="picu-active">' . __( 'Active', 'picu' ) . '</span>';
							}
							else {
								echo '<a class="button js-tab-switch" href="#picu-licenses-tab">' . __( 'Activate License', 'picu' ) . '</a>';
							} ?>
						</p>
					</div>
				</div>
			</div><!-- .picu-add-on -->

			<div class="picu-add-on">
				<div class="picu-add-on-inner js-picu-add-on-inner">
					<h2><?php _e( 'Selection Options', 'picu' ); ?></h2>
					<div class="picu-add-on-description">
						<p><?php _e( 'The <strong>Selection Options</strong> add-on enables you to set specific selection goals: Define an exact, minimum, maximum or a range of numbers of images your client needs to approve.', 'picu' ); ?></p>
						<p style="text-align: center;">
							<?php
							if ( ! array_key_exists( 'picu_selection_options', $picu_addons ) ) {
								echo '<a class="button" href="https://picu.io/add-ons/selection-options/">' . sprintf( __( 'Get %s', 'picu' ), __( 'Selection Options', 'picu' ) ) . '</a>';
							}
							elseif( array_key_exists( 'picu_selection_options', $picu_addons ) AND isset(  $licenses['picu_selection_options_license_status'] ) AND 'valid' == $licenses['picu_selection_options_license_status'] ) {
								echo '<span class="picu-active">' . __( 'Active', 'picu' ) . '</span>';
							}
							else {
								echo '<a class="button js-tab-switch" href="#picu-licenses-tab">' . __( 'Activate License', 'picu' ) . '</a>';
							} ?>
						</p>
					</div>
				</div>
			</div><!-- .picu-add-on -->

			<div class="picu-add-on">
				<div class="picu-add-on-inner coming-soon js-picu-add-on-inner">
					<h2><?php _e( 'Download', 'picu' ); ?></h2>
					<div class="picu-add-on-description">
						<p><?php _e( 'The <strong>Download</strong> add-on enables you to allow clients to download the collection images as a .zip file. Alternatively you can specify an external link.', 'picu' ); ?></p>
						<p style="text-align: center;">
							<?php
							if ( ! array_key_exists( 'picu_download', $picu_addons ) ) {
								echo '<a class="button" href="https://picu.io/add-ons/picu_download/">' . sprintf( __( 'Get %s', 'picu' ), __( 'Download', 'picu' ) ) . '</a>';
							}
							elseif( array_key_exists( 'picu_download', $picu_addons ) AND isset(  $licenses['picu_download_license_status'] ) AND 'valid' == $licenses['picu_download_license_status'] ) {
								echo '<span class="picu-active">' . __( 'Active', 'picu' ) . '</span>';
							}
							else {
								echo '<a class="button js-tab-switch" href="#picu-licenses-tab">' . __( 'Activate License', 'picu' ) . '</a>';
							} ?>
						</p>
					</div>
					<span class="soon"><?php _e( 'coming soon', 'picu' ); ?></span>
				</div>
			</div><!-- .picu-add-on -->

			<div class="picu-add-on">
				<div class="picu-add-on-inner js-picu-add-on-inner">
					<h2><?php _e( 'Disable Right Click', 'picu' ); ?></h2>
					<div class="picu-add-on-description">
						<p><?php _e( 'Many photographers want to disable right clicks on their websites to prevent image theft. While this is <a href="https://picu.io/2016/03/disable-right-click/">not a recommended practice</a> and therefore not part of our core plugin, we do provide this feature as an add-on, if you really need it.', 'picu' ); ?></p>
						<p style="text-align: center;"><a style="opacity: 1.5" class="button inactive" href="https://picu.io/wp-content/uploads/2016/03/picu-disable-right-click.zip">
							<?php echo sprintf( __( 'Get %s', 'picu' ), __( 'Disable Right Click', 'picu' ) ); ?></a></p>
					</div>
				</div>
			</div><!-- .picu-add-on -->

			<div class="picu-add-on">
				<div class="picu-add-on-inner idea js-picu-add-on-inner">
					<h3><?php _e( 'Ideas?', 'picu' ); ?></h3>
					<p><?php _e( 'Tell us how we can improve your workflow.', 'picu' ); ?></p>
					<p><a class="button button-primary" href="https://picu.io/contact/"><?php _e( 'Contact us', 'picu' ); ?></a></p>
				</div>
			</div><!-- .picu-add-on -->

		</div>
		<p><?php _e( 'We are continuously adding more functionality.', 'picu' ); ?></p>
		<p><?php _e( 'To get notified when a new add-on ships, <a href="https://picu.io/#newsletter">subscribe to our newsletter</a>.', 'picu' ); ?></p>
	</div>

<?php
	// Again, if add-ons exist, we create the content under the licenses tab
	if ( count( $picu_addons ) > 0 ) { ?>

	<div class="picu-tab-content js-picu-tab-content" id="picu-licenses-tab">
		<form class="picu-form" method="post" action="options.php#picu-licenses-tab">
		<?php
			wp_nonce_field( 'picu_addons_nonce',  'picu_addons_nonce' );

			settings_fields( 'picu_addon_licenses' );

			// Loop through all add-ons that have registered with our hook earlier
			foreach( $picu_addons as $addon_slug => $addon_data ) {

				// Create output
				echo '<fieldset class="license" id="' . $addon_slug . '">';
				echo '<legend>' . $addon_data['name'] . '</legend>';

				// Get license from the data base
				$license = ( isset( $licenses[$addon_slug] ) AND ! empty( $licenses[$addon_slug] ) ) ? esc_attr( $licenses[$addon_slug] ) : '';

				// Show valid license
				if ( isset( $licenses[$addon_slug] ) AND 6 <= strlen( $license ) ) {
					echo '<p><span class="license">' . str_repeat( '•', strlen( $license ) - 6 ) . substr( $license, -6, 6 ) . '</span> <span class="picu-active-license">' . __( 'active', 'picu' ). '</span>';
					echo '<br /><input type="submit" class="button-secondary" name="' . $addon_slug . '_license_deactivate" value="' . __( 'Deactivate License', 'picu' ) . '" />';
				}
				// Show license input field
				else {
					echo '<p><label for="' . $addon_slug .
					'_license_key">' . __( 'Enter your license key', 'picu' );
					echo '</label> ';
					echo '<input type="text" id="' . $addon_slug . '_license_key" name="picu_addon_licenses[' . $addon_slug . ']"  placeholder="' . __( 'Enter your license key', 'picu' ) . '" />';
					echo '<br /><input type="submit" class="button-secondary" name="' . $addon_slug . '_license_activate" value="' . __( 'Activate License', 'picu' ) . '" /></p>';
				}

				echo '</fieldset>';

			} // foreach

		?>
		</form>
	</div><!-- .picu-tab-content .js-picu-tab-content #picu-licenses-tab -->

<?php } ?>

</div><!-- .wrap -->

<?php }



/**
 * Register licenses settings
 *
 * @since 0.7.5
 */
function picu_register_addons_settings() {

	register_setting( 'picu_addon_licenses', 'picu_addon_licenses', 'picu_addons_licenses_validate' );

}

add_action( 'admin_init', 'picu_register_addons_settings' );


/**
 * Change access depending on a predefined capability
 *
 * @since 1.1.0
 */
add_filter( 'option_page_capability_picu_addon_licenses', 'picu_capability' );


/**
 * Validate add-on licenses
 *
 * @since 0.7.5
 */
function picu_addons_licenses_validate( $args ) {

	// Save submitted data into a placeholder variable
	$temp = $args;

	// Switcharoo –> Make sure we reatain existing licenses
	$licenses = get_option( 'picu_addon_licenses' );
	$args = $licenses;

	// Check if nonce is set
	if ( isset( $_POST['picu_addons_nonce'] ) ) {

		// Security check
		if ( ! check_admin_referer( 'picu_addons_nonce', 'picu_addons_nonce' ) ) {
			return;
		}

		// Get add-ons, that have registered with our hook earlier
		$picu_addons = array();
		$picu_addons = apply_filters( 'picu_addons', $picu_addons );

		// Loop through all add-ons
		foreach( $picu_addons as $addon_slug => $addon_data ) {

			// Listen for activate button for each add-on
			// Then try to validate with picu.io
			if ( isset( $_POST[ $addon_slug .'_license_activate'] ) AND isset( $temp[ $addon_slug ] ) ) {

				// Get submitted license key
				$license_key = trim( $temp[ $addon_slug ] );

				// Prepare data to send in our API request
				$api_params = array(
					'edd_action'=> 'activate_license',
					'license' 	=> $license_key,
					'item_name' => urlencode( $addon_data['name'] ), // the name of our product in EDD
					'url'       => home_url()
				);

				// Call the custom API
				$response = wp_remote_post( 'https://www.picu.io', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

				// Make sure the response came back okay
				if ( is_wp_error( $response ) ) {
					return false;
				}

				// Decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// Check if the licenes is valid
				if ( 'valid' == $license_data->license ) {

					// Add licence status
					$args[ $addon_slug ] = $temp[ $addon_slug ];
					$args[ $addon_slug . '_license_status'] = 'valid';

					// Add success notification
					picu_add_notification( 'picu_license_validated', 'notice notice-success is-dismissible', __( '🎉 Your license was activated. Thanks for supporting picu!', 'picu' ) );
				}
				else {
					// License is invalid, tell the user
					picu_add_notification( 'picu_license_check_failed', 'notice notice-error is-dismissible', __( 'The license you entered could not be verified.<br />You can buy a license on the <a href="https://picu.io/add-ons/">picu Add-On Store</a>. If you think this is an error, please <a href="https://picu.io/support/">contact support</a>.', 'picu' ), $current_screen->base );
				}
			}
			// Listen for activate button for each add-on
			elseif ( isset( $_POST[ $addon_slug .'_license_deactivate'] ) AND isset( $args[ $addon_slug ] ) ) {

				$license_key = trim( $args[ $addon_slug ] );

				// Data to send in our API request
				$api_params = array(
					'edd_action'=> 'deactivate_license',
					'license' 	=> $license_key,
					'item_name' => urlencode( $addon_data['name'] ), // the name of our product in EDD
					'url'       => home_url()
				);

				// Call the custom API.
				$response = wp_remote_post( 'https://www.picu.io', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

				// Make sure the response came back okay
				if ( is_wp_error( $response ) )
					return false;

				// Decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// $license_data->license will be either "deactivated" or "failed"
				if ( $license_data->license == 'deactivated' ) {
					unset( $args[ $addon_slug ], $args[ $addon_slug . '_license_status'] );
					picu_add_notification( 'picu_license_deactivated', 'notice notice-success is-dismissible', __( 'License deactivated.', 'picu' ) );
				}
				else {
					// Deactivation didn't work
					picu_add_notification( 'picu_license_deactivation_error', 'notice notice-error is-dismissible', __( 'Your License could not be deactivated. Please contact <a href="https://picu.io/support/">support</a>.', 'picu' ) );
				}
			}
		}
	}

	return $args;

}