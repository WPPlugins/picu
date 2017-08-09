<?php
/**
 * Debug info page
 *
 * @since 0.9.3
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Content of debug settings page
 *
 * @since 0.9.3
 */
?>
<div class="picu-tab-content js-picu-tab-content" id="picu-settings-debug">
	<table id="picu-debug-info">
		<thead>
			<tr>
				<th><?php _e( 'Setting', 'picu' ); ?></th>
				<th><?php _e( 'Value', 'picu' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php _e( 'Home URL', 'picu' ); ?></td>
				<td><?php echo home_url(); ?></td>
			<tr>
				<td><?php _e( 'WordPress version', 'picu' ); ?></td>
				<td><?php echo bloginfo('version'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Active plugins', 'picu' ); ?></td>
				<td>
					<?php
					$plugins = get_option( 'active_plugins' );
					$temp = array();
					foreach( $plugins as $key => $value ) {
            		$plugin = explode( '/', $value );
						$temp[] = $plugin[0];
					}
					echo implode( $temp, ', ' );
					?>
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Active theme', 'picu' ); ?></td>
				<td>
					<?php
						$theme = wp_get_theme();
						echo $theme->get( 'Name') . ' (' . $theme->get( 'Version') . ')<br />' . $theme->get( 'ThemeURI' );
					?>
				</td>
			</tr>
			<tr>
				<td><?php _e( 'picu version', 'picu' ); ?></td>
				<td><?php echo esc_html( PICU_VERSION ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'PHP version', 'picu' ); ?></td>
				<td><?php echo esc_html( PHP_VERSION ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'MySQL version', 'picu' ); ?></td>
				<?php
					global $wpdb;
				?>
				<td><?php echo esc_html( $wpdb->get_var( "SELECT VERSION() AS version" ) ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Server', 'picu' ); ?></td>
				<td><?php echo esc_html( $_SERVER[ 'SERVER_SOFTWARE' ] ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Operating System', 'picu' ); ?></td>
				<td><?php echo esc_html( PHP_OS ); ?></td>
			</tr>
			<?php
				if ( function_exists( 'get_current_user' ) ) {
			?>
			<tr>
				<td><?php _e( 'Current PHP user', 'picu' ); ?></td>
				<td><?php echo esc_html( get_current_user() ); ?></td>
			</tr>
			<?php
				}
			?>
			<tr>
				<td><?php _e( 'Safe Mode', 'picu' ); ?></td>
				<td><?php echo (bool) ini_get( 'safe_mode' ) ? __( 'On', 'picu' ) : __( 'Off', 'picu' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Maximum execution time', 'picu' ); ?></td>
				<td><?php echo esc_html( ini_get( 'max_execution_time' ) ) . ' ' . __( 'seconds', 'picu' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Server Time', 'picu' ); ?></td>
				<td><?php echo esc_html( date( 'H:i' ) ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Blog Time', 'picu' ); ?></td>
				<td><?php echo date( 'H:i', current_time( 'timestamp' ) ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Blog language', 'picu' ); ?></td>
				<td><?php echo get_bloginfo( 'language' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'MySQL client encoding', 'picu' ); ?></td>
				<td><?php echo defined( 'DB_CHARSET' ) ? DB_CHARSET : ''; ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Blog charset', 'picu' ); ?></td>
				<td><?php echo get_bloginfo( 'charset' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'PHP memory limit', 'picu' ); ?></td>
				<td><?php echo esc_html ( ini_get( 'memory_limit' ) ); ?></td>
			</tr>
			<tr>
				<td><?php echo _e( 'Maximum upload file size', 'picu' ); ?></td>
				<td><?php echo size_format( wp_max_upload_size() ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'WordPress memory limit', 'picu' ); ?></td>
				<td><?php echo esc_html( WP_MEMORY_LIMIT ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'WordPress maximum memory limit', 'picu' ); ?></td>
				<td><?php echo esc_html( WP_MAX_MEMORY_LIMIT ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Memory in use', 'picu' ); ?></td>
				<td><?php echo size_format( @memory_get_usage( TRUE ), 2 ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'ASP style PHP tags', 'picu' ); ?></td>
				<td><?php echo (bool) ini_get( 'asp_tags' ) ? __( 'On', 'picu' ) : __( 'Off', 'picu' ); ?></td>
			<tr>
				<td><?php _e( 'Loaded PHP extensions', 'picu' ); ?></td>
				<?php
					$extensions = get_loaded_extensions();
					sort( $extensions );
				?>
				<td><?php echo esc_html( implode( ', ', $extensions ) ); ?></td>
			</tr>
		</tbody>
	</table>

</div><!-- .picu-tab #picu-settings-debug"-->