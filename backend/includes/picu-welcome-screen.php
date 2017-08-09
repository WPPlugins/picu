<?php
/**
 * picu welcome screen
 *
 * @since 0.7.0
 * @package picu
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Redirect to the picu welcome screen
 *
 * @since 0.5.0
 */
function picu_welcome_screen_activation_redirect() {

	// Only redirect if transient is set
	if ( ! get_transient( '_picu_welcome_screen_activation_redirect' ) ) {
		return;
	}

	// Delete the redirect transient
	delete_transient( '_picu_welcome_screen_activation_redirect' );

	// Don't redirect if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Redirect to bbPress about page
	wp_safe_redirect( add_query_arg( array( 'page' => 'picu-welcome-screen' ), admin_url( 'index.php' ) ) );

}

add_action( 'admin_init', 'picu_welcome_screen_activation_redirect' );


/**
 * Add welcome screen as dashboard page
 *
 * @since 0.7.0
 */
function picu_welcome_screen_page() {

	add_dashboard_page(
		'picu Welcome Screen',
		'picu Welcome Screen',
		'read',
		'picu-welcome-screen',
		'welcome_screen_content'
	);
}

add_action('admin_menu', 'picu_welcome_screen_page');


/**
 * Display the welcome screen
 *
 * @since 0.7.0
 */
function welcome_screen_content() {
?>
<div class="wrap">
	<div class="picu-welcome">
		<h2 class="picu-welcome-subtitle"></h2>
		<img class="picu-logo" src="<?php echo PICU_URL; ?>/frontend/images/picu-logo-grey.svg" alt="picu" />
		<div class="row row-big">
			<div class="column col-50">
				<h1><?php _e( 'Greetings, Photographer!', 'picu' ); ?></h1>
				<p class="thanks"><?php _e( 'Thank you for installing picu.', 'picu' ); ?></p>
				<p><?php _e( 'We work very hard to make your experience and that of your clients as smooth as possible and we hope using picu will transform the way you work and interact with your photo clients.', 'picu' ); ?></p>
				<p><?php _e( 'If you have feedback of any kind, please get in touch. We\'d love to hear from you.', 'picu' ); ?></p>
				<p><?php _e( 'To get started follow the steps below.', 'picu' ); ?></p>
			</div>
			<div class="column col-50"><img src="<?php echo PICU_URL; ?>/backend/images/picu-browser.jpg" alt="picu collection" /></div>
		</div>

		<div class="row row-white">
			<div class="column col-33">
				<div class="column-inner get-started js-column-inner">
					<h2><?php _e( 'Get started', 'picu' ); ?></h2>
					<p><?php _e( 'Start right away and create your first collection:', 'picu' ); ?></p>
					<p><a class="button button-primary" href="<?php echo get_admin_url(); ?>post-new.php?post_type=picu_collection"><?php _e( 'Create a collection', 'picu' ); ?></a></p>
					<p><?php _e( 'Configure how picu looks:', 'picu' ); ?></p>
					<p><a class="button" href="<?php echo get_admin_url(); ?>admin.php?page=picu-settings"><?php _e( 'Select picu theme', 'picu' ); ?></a></p>
				</div>
			</div>

			<div class="column col-33">
				<div class="column-inner js-column-inner">
					<h2><?php _e( 'Add-Ons', 'picu' ); ?></h2>
					<div class="soon"><?php _e( 'coming soon', 'picu' ); ?></div>
					<p><?php _e( 'We are in the process of building great add-ons that will make picu even better.', 'picu' ); ?></p>
					<p><?php echo sprintf( __( 'Check out the <a href="%s">add-ons page</a> or visit <a href="https://picu.io/">our website</a> for further information.', 'picu' ), get_admin_url() . 'admin.php?page=picu-add-ons' ); ?></p>
				</div>
			</div>

			<div class="column col-33">
				<div class="column-inner js-column-inner">
					<h2><?php _e( 'Need help?', 'picu' ); ?></h2>
					<ul>
						<li><?php _e( 'Please take a look at <a href="https://picu.io/faq/">our FAQ</a>.', 'picu' ); ?></li>
						<li><?php _e( 'If you can\'t find the answer to your question, please use the official WordPress.org <a href="https://wordpress.org/support/plugin/picu">support forum</a>.', 'picu' ); ?></li>
					</ul>
				</div>
			</div>
		</div>

		<p class="align-center"><?php _e( 'To get the latest updates and be informed when our add-ons become available <a href="https://picu.io/#newsletter">sign up for our newsletter</a>', 'picu' ); ?></p>
	</div>
</div>
<?php
}


/**
 * Remove the welcome screen from the menu
 *
 * @since 0.7.0
 */
function welcome_screen_remove_menus() {
	remove_submenu_page( 'index.php', 'picu-welcome-screen' );
}

add_action( 'admin_head', 'welcome_screen_remove_menus' );