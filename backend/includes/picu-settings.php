<?php
/**
 * Picu Settings
 *
 * Adds our admin menu and the settings page
 *
 * @since 0.5.0
 * @package picu
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Register settings menu for picu
 *
 * @since 0.5.0
 */
function picu_plugin_menu() {

	add_menu_page(
		'picu',
		'picu',
		picu_capability(),
		'picu',
		'',
		'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNDAgMzIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiM5OTkiPjxwYXRoIGQ9Ik0yNy45NzYgMy42MzJoOC4zMjR2Ny45OTJoMy43di0xMS42MjRoLTEyLjAyNHYzLjYzMnpNMzYuMjkyIDI4LjM2aC04LjM0MXYzLjY0aDEyLjA0OXYtMTEuNjQ4aC0zLjcwOHY4LjAwOHpNMy43NCAyMC4yNDloLTMuNzR2MTEuNzUxaDExLjk2OXYtMy42NzJoLTguMjI5di04LjA3OXpNMy43NDIgMy42NzRoOC4yMzJ2LTMuNjc0aC0xMS45NzR2MTEuNzU2aDMuNzQydi04LjA4MnpNMjcuMTE4IDYuNDE4bC0xMC42NDcgMTIuOTQ3LTUuMjA0LTUuMDMzLTMuMzYyIDMuMzQ0IDkuMDI3IDguNzY5IDEzLjkwNS0xNy4xMjQtMy43MTktMi45MDN6Ii8+PC9nPjwvc3ZnPg==',
		25
	);

	add_submenu_page(
		'picu',
		__( 'New Collection', 'picu' ),
		__( 'New Collection', 'picu' ),
		picu_capability(),
		'post-new.php?post_type=picu_collection'
	);

	add_submenu_page(
		'picu',
		__( 'picu Settings', 'picu' ),
		__( 'Settings', 'picu' ),
		picu_capability(),
		'picu-settings',
		'picu_load_settings_page'
	);

	add_submenu_page(
		'picu',
		__( 'picu Add-Ons', 'picu' ),
		__( 'Add-Ons', 'picu' ),
		picu_capability(),
		'picu-add-ons',
		'picu_load_add_ons_page'
	);

}
add_action( 'admin_menu', 'picu_plugin_menu' );


/**
 * Load Picu settings page
 *
 * @since 0.7.0
 */
function picu_load_settings_page() {
?>
	<div class="picu-settings wrap">

		<h1><?php _e( 'picu Settings', 'picu' ); ?></h1>
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
		// Show generic notification
		elseif ( isset( $_REQUEST['settings-updated'] ) AND false != $_REQUEST['settings-updated'] ) { ?>
			<div class="updated">
				<p><?php _e( 'Settings saved.', 'picu' ); ?></p>
			</div>
		<?php }

		// Load additional settings (eg. from picu add-ons)
		$additional_settings = array();
		$additional_settings = apply_filters( 'picu_get_additional_settings_tabs', $additional_settings );

	?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab is-active" href="#picu-settings-general"><?php _e( 'General Settings', 'picu' ); ?></a><?php
				/**
				 * Add nav element for additional settings
				 */
				foreach( $additional_settings as $tab ) {
					echo "\n" . $tab['nav'] . "\n";
				}
			?>
		</h2>

		<div class="picu-tab-content js-picu-tab-content is-active" id="picu-settings-general">
			<form class="picu-form  picu-settings-form" method="post" action="options.php#picu-settings-general" enctype="multipart/form-data">

			<?php
				settings_fields( 'picu_settings' );
				$options = get_option( 'picu_settings' );

				if ( ! isset( $options['theme'] ) ) { $options['theme'] = 'dark'; }
				if ( ! isset( $options['picu_love'] ) ) { $options['picu_love'] = 'off'; }
				if ( ! isset( $options['send_html_mails'] ) ) { $options['send_html_mails'] = 'on'; }
			?>

				<fieldset>
					<legend><?php _e( 'Theme', 'picu' ); ?></legend>
					<p><?php _e( 'Choose how your collections will be displayed:', 'picu' ); ?></p>
					<p>
						<span class="nowrap">
							<input type="radio" class="picu-radio-image" name="picu_settings[theme]" id="picu_dark_theme" value="dark" <?php checked( $options['theme'], 'dark' ); ?> />
							<label for="picu_dark_theme" class="after"><img class="theme-thumbnail" src="<?php echo PICU_URL; ?>/backend/images/dark-theme.png" alt="<?php _e( 'Dark', 'picu' ); ?>" /></label>
						</span>
						<span class="nowrap">
							<input type="radio" class="picu-radio-image" name="picu_settings[theme]" id="picu_light_theme" value="light" <?php checked( $options['theme'], 'light' ); ?> />
							<label for="picu_light_theme" class="after"><img class="theme-thumbnail" src="<?php echo PICU_URL; ?>/backend/images/light-theme.png" alt="<?php _e( 'Light', 'picu' ); ?>" /></label>
						</span>
					</p>
				</fieldset>

				<fieldset>
					<legend><?php _e( 'Email Notifications', 'picu' ); ?></legend>
					<p></p>
					<p><input type="checkbox" id="send_html_mails" name="picu_settings[send_html_mails]" <?php checked( $options['send_html_mails'], 'on' ); ?>/> <label for="send_html_mails" class="after"><?php _e( 'Send HTML email notifications', 'picu' ); ?><br /><span class="description"><?php _e( 'Don\'t like HTML in your emails? Switch to plain text by unchecking this box.', 'picu' ); ?></span></label></p>
				</fieldset>

				<fieldset>
					<legend>picu love</legend>
					<p><input type="checkbox" id="picu_love" name="picu_settings[picu_love]" <?php checked( $options['picu_love'], 'on' ); ?>/> <label for="picu_love" class="after"><?php _e( 'Show picu logo', 'picu' ); ?><br /><span class="description"><?php _e( 'Spread some picu love, by displaying our logo in collections and picu related emails.', 'picu' ); ?> <span class="normal">❤️</span></span></label></p>
				</fieldset>

				<fieldset>
					<p><input type="submit" name="save-picu-settings" value="<?php _e( 'Save Settings', 'picu'); ?>" class="button button-primary" /></p>
				</fieldset>

			</form>
		</div>

		<?php
			/**
			 * Add content for additional settings
			 */
			foreach( $additional_settings as $tab ) {
				require_once( $tab['page'] );
			}
		?>

	</div>
<?php
}


/**
 * Register settings
 *
 * @since 0.7.0
 */
function picu_register_settings() {
	register_setting( 'picu_settings', 'picu_settings', 'picu_settings_validate' );
}
add_action( 'admin_init', 'picu_register_settings' );


/**
 * Change access depending on a predefined capability
 *
 * @since 1.1.0
 */
add_filter( 'option_page_capability_picu_settings', 'picu_capability' );


/**
 * Validate picu settings
 *
 * @since 0.7.0
 */
function picu_settings_validate( $args ) {

	// Validate theme variable
	if ( 'dark' != $args['theme'] AND 'light' != $args['theme'] ) {
		unset( $args['theme'] );
	}

	// Validate picu_love Setting
	if ( ! isset( $args['picu_love'] ) OR 'on' != $args['picu_love'] ) {
		unset( $args['picu_love'] );
	}

	// Validate Mail Setting
	if ( ! isset( $args['send_html_mails'] ) OR 'on' != $args['send_html_mails'] ) {
		$args['send_html_mails'] = 'off';
	}

	return $args;

}


/**
 * Add settings tab for debug info
 *
 * @since 0.9.3
 */
function picu_debug_add_settings_tab( $tabs ) {

	$bc_tab['nav'] = '<a class="nav-tab" href="#picu-settings-debug">'. __( 'Debug Info', 'picu' ) . '</a>';
	$bc_tab['page'] = PICU_PATH . 'backend/includes/picu-debug.php';
	array_push( $tabs, $bc_tab );

	return $tabs;

}

add_filter( 'picu_get_additional_settings_tabs', 'picu_debug_add_settings_tab' );