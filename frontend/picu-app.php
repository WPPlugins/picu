<?php
/**
 * @package picu
 * @since 0.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// No caching or minification for picu collections
if ( ! defined( 'DONOTCACHEPAGE' ) ) {
	define( 'DONOTCACHEPAGE', true );
}
if ( ! defined( 'DONOTCACHEDB' ) ) {
	define( 'DONOTCACHEDB', true );
}
if ( ! defined( 'DONOTMINIFY' ) ) {
	define( 'DONOTMINIFY', true );
}
if ( ! defined( 'DONOTCDN' ) ) {
	define( 'DONOTCDN', true );
}
if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
	define( 'DONOTCACHEOBJECT', true );
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="robots" content="noindex, nofollow" />
		<title><?php if ( post_password_required( $post) ) {
			_e( 'This collection is password protected.', 'picu' );
			} else { the_title(); } ?></title>
		<?php

			picu_load_styles();

			$settings = get_option( 'picu_settings' );

			$custom_styles = '';
			$custom_styles = apply_filters( 'picu_custom_styles', $custom_styles );

			if ( !empty( $custom_styles) ) {
				echo '<style>' . $custom_styles . '</style>';
			}

		?>
	</head>
	<body<?php picu_body_classes(); ?>>
		<?php if ( post_password_required( $post) ) { ?>
			<div class="picu-protected">
				<div class="picu-protected-inner">
					<h1><?php _e( 'This collection is password protected.', 'picu' ); ?></h1>
					<?php
						if ( isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ) and $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password ) { ?>
			   		<p class="error-msg"><?php _e( 'Wrong password.', 'picu' ); ?></p>
					<?php } ?>
					<?php
						$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
					?>
					<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post">
						<p><label for="<?php echo $label; ?>"><?php _e( 'To view this collection, enter the password below.', 'picu' ); ?></label> <input name="post_password" id="<?php echo $label; ?>" type="password" size="20" maxlength="20" /></p>
						<p><input class="picu-button primary" type="submit" name="Submit" value="<?php echo esc_attr__( 'Enter', 'picu' ); ?>" /></p>
					</form>
				</div>
			</div><!-- .picu-protected -->
			<?php
		}
		else {
		?>

		<?php
			if ( file_exists( PICU_PATH . '/frontend/images/icons.svg' ) ) {
				include_once( PICU_PATH . '/frontend/images/icons.svg' );
			}
		?>
		<header class="picu-header">
			<?php
				$picu_header = '<div class="picu-header-inner">';
				$picu_header .= '<div class="blog-name">' . get_bloginfo( 'name' ) . '</div>';
				$picu_header .= '<div class="picu-collection-title">'.  get_the_title( $post->ID ) . '</div>';
				$picu_header .= '</div>';

				$picu_header = apply_filters( 'picu_header', $picu_header, $post->ID );
				echo $picu_header;
			?>

			<?php if ( is_user_logged_in() ) {
				edit_post_link( __( 'Edit', 'picu' ), '<span class="edit-button">', '</span>', $post->ID );
			} ?>
		</header>

		<div class="picu-collection"></div>

		<?php
			// Load backbone templates
			$templates = picu_load_backbone_templates();

			foreach ( $templates as $template => $template_path ) {
				include_once( $template_path );
				echo "\n\n\t\t";
			}
		?>

		<script>
			var picu = picu || {};
			picu.poststatus = '<?php echo get_post_status( get_the_ID() ); ?>';
		</script>

		<script src='<?php echo PICU_URL; ?>frontend/js/_vendor/jquery.min.js'></script>
		<script src='<?php echo PICU_URL; ?>frontend/js/_vendor/underscore.min.js'></script>
		<script src='<?php echo PICU_URL; ?>frontend/js/_vendor/backbone.min.js'></script>

		<script>
			_.templateSettings = {
				evaluate: /<[%@]([\s\S]+?)[%@]>/g,
				interpolate: /<[%@]=([\s\S]+?)[%@]>/g,
				escape: /<[%@]-([\s\S]+?)[%@]>/g
			};
		</script>

		<?php
			// Load collections, models & views
			$cmv = picu_load_cmv();

			foreach ( $cmv as $file_name => $file_path ) {
				echo '<script src=' . $file_path . '></script>' . "\n\t\t";
			}
		?>

		<script src='<?php echo PICU_URL; ?>frontend/js/router.js'></script>

		<script src='<?php echo PICU_URL; ?>frontend/js/picu-app.js'></script>
		<script src='<?php echo PICU_URL; ?>frontend/js/picu-ui-helpers.js'></script>

		<script>
			// Load collection data and app state
			var data = '<?php echo picu_get_images(); ?>';
			var appstate = '<?php echo picu_get_app_state(); ?>';

			// Booting up...
			$(function() { picu.boot( $( '.picu-collection' ), data, appstate ); });
		</script>

		<?php
			$custom_scripts = '';
			$custom_scripts = apply_filters( 'picu_custom_scripts', $custom_scripts );
			echo $custom_scripts;
		?>

		<?php } // post_password_required() ?>

		<?php
			if ( isset( $settings['picu_love'] ) AND 'on' == $settings['picu_love'] ) { ?>
				<a class="picu-brand" href="https://picu.io/">powered by picu</a>
		<?php } ?>
	</body>
</html>