<?php
/**
 * Plugin Name: picu
 * Plugin URI: https://picu.io/
 * Description: Client proofing for photographers.
 * Version: 1.1.1
 * Author: Claudio Rimann, Florian Ziegler
 * Author URI: https://picu.io/
 * License: GNU General Public License 2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: picu
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Include functions for picu
 *
 * @package picu
 * @since 0.2.0
 */

if ( ! function_exists( 'picu_setup' ) ) {

	function picu_setup() {

		// Define plugin version
		define( 'PICU_VERSION', '1.1.1' );

		// Define path for this plugin
		define( 'PICU_PATH', plugin_dir_path(__FILE__) );

		// Define URL for this plugin
		define( 'PICU_URL', plugin_dir_url(__FILE__) );

		// Include functions to render add-ons page
		require PICU_PATH . 'backend/includes/picu-addons-page.php';

		// Include functions to render admin menu and settings page
		require PICU_PATH . 'backend/includes/picu-settings.php';

		// Include function for registering custom post type collection
		require PICU_PATH . 'backend/includes/picu-cpt-collection.php';

		// Include welcome screen
		require PICU_PATH . 'backend/includes/picu-welcome-screen.php';

		// Include functions for admin notices and error messages
		require PICU_PATH . 'backend/includes/picu-admin-notices.php';

		// Include custom metabox and our custom edit screen
		require PICU_PATH . 'backend/includes/picu-edit-collection.php';

		// Picu media handling
		require PICU_PATH . 'backend/includes/picu-media.php';

		// Everything that doesn't fit anywhere else...
		require PICU_PATH . 'backend/includes/picu-email.php';

		// Everything that doesn't fit anywhere else...
		require PICU_PATH . 'backend/includes/picu-helper.php';

		// Handle ajax requests
		require PICU_PATH . 'backend/includes/picu-ajax.php';

		// Fix third party stuff
		require PICU_PATH . 'backend/includes/picu-opp.php';

		// Include template redirection etc. for collections
		require PICU_PATH . 'frontend/includes/picu-template-functions.php';

	}
}

add_action( 'after_setup_theme', 'picu_setup' );


/**
 * Set transient to display welcome screen on activation
 *
 * @package picu
 * @since 0.7.0
 */

function picu_activate_welcome_screen() {
	// Set transient for redirect to activation screen
	set_transient( '_picu_welcome_screen_activation_redirect', true, 30 );
}

register_activation_hook( __FILE__, 'picu_activate_welcome_screen' );


/**
 * Flush rewrite rules on plugin activation/deactivation
 *
 * @since 0.7.0
 */
function picu_flush_rewrites() {

	// Include custom post type registration
	include( plugin_dir_path(__FILE__) . 'backend/includes/picu-cpt-collection.php' );

	// Make sure our custom post types are defined first
	picu_register_cpt_collection();

	// Flush the rewrite rules
	flush_rewrite_rules();

}

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'picu_flush_rewrites' );


/**
 * Load language files to enable plugin translation
 *
 * @package picu
 * @since 0.1.0
 */

function picu_load_plugin_textdomain() {
	load_plugin_textdomain( 'picu', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action( 'plugins_loaded', 'picu_load_plugin_textdomain' );


/**
 * Load some custom styling for our admin screens
 *
 * @since 0.3.2
 */

function picu_admin_styles_scripts() {

	global $post;

	$current_screen = get_current_screen();

	// Prevent conflicts in case no current_screen is set
	if ( empty($current_screen) )
		return;

	// Only load those styles on edit collection screens
	if ( is_admin() ) {

		wp_enqueue_style( 'picu-admin', PICU_URL . 'backend/css/picu-admin.css', false, PICU_VERSION );

		global $pagenow;
		if ( $current_screen->post_type == 'picu_collection' AND get_post_type() == 'picu_collection' AND $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
			// Enqueue wp.media scripts manually, because we don't load the default editor on collections
			// Post needs to be provided, or uploaded media will not get attached to the collection
			$args = array( 'post' => $post->ID );
			wp_enqueue_media( $args );
		}

		if ( 'picu_page_picu-settings' == $current_screen->base ) {
			// Add the color picker css file
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( ( 'picu_collection' == $current_screen->post_type AND 'picu_collection' == get_post_type() AND 'post-new.php' == $pagenow || 'post.php' == $pagenow ) || ( 'picu_page_picu-settings' == $current_screen->base ) || ( 'picu_page_picu-add-ons' == $current_screen->base ) || ( 'dashboard_page_picu-welcome-screen' == $current_screen->base ) ) {

			// Enqueue media
			wp_enqueue_media();

			// Enqueue our custom script
			wp_enqueue_script( 'picu-admin', PICU_URL . 'backend/js/picu-admin.min.js', array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-sortable', 'underscore', 'backbone', 'wp-color-picker' ), PICU_VERSION, true );
		}

	}

}

add_action( 'admin_enqueue_scripts', 'picu_admin_styles_scripts' );


/**
 * Add settings link in plugins overview
 *
 * @since 1.0.0
 */
function picu_plugin_action_links( $actions ) {

	$action = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=picu-settings' ), __( 'Settings', 'picu' ) );
	array_unshift( $actions, $action );

	return $actions;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , 'picu_plugin_action_links', 10 );


/**
 * Return capability that allows picu to be accessed
 *
 * Can be filtered using 'picu_capability'
 *
 * @since 1.1.0
 */
function picu_capability() {

	// Defaults to administrator privileges
	$picu_capability = 'manage_options';
	$picu_capabilities = apply_filters( 'picu_capability', $picu_capability );

	return $picu_capability;
}