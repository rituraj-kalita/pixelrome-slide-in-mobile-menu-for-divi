<?php
/**
 * Plugin Name: PIXELROME – Slide-In Mobile Menu for Divi
 * Plugin URI: https://pixelrome.com
 * Description: Premium slide-in mobile menu enhancements for Divi theme.
 * Version: 1.0.2
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Tested up to: 7.0
 * Author: PIXELROME
 * Author URI: https://pixelrome.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pixelrome-slide-in-mobile-menu-for-divi
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define Plugin Constants
 */
if ( ! defined( 'PRDSM_VERSION' ) ) {
	define( 'PRDSM_VERSION', '1.0.2' );
}

if ( ! defined( 'PRDSM_PLUGIN_FILE' ) ) {
	define( 'PRDSM_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'PRDSM_PATH' ) ) {
	define( 'PRDSM_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'PRDSM_URL' ) ) {
	define( 'PRDSM_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Prevent Lite version from loading if Pro is active.
 */
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$prdsmpro_plugin = 'slide-in-mobile-menu-for-divi-pro/slide-in-mobile-menu-for-divi-pro.php';

if ( is_plugin_active( $prdsmpro_plugin ) ) {

	deactivate_plugins( plugin_basename( __FILE__ ), true );
	
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading activation flag only to suppress activation notice.
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}

	add_action(
		'admin_notices',
		function() {
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php
					echo esc_html__(
						'PIXELROME – Slide-In Mobile Menu for Divi was automatically deactivated because the Pro version is active.',
						'pixelrome-slide-in-mobile-menu-for-divi'
					);
					?>
				</p>
			</div>
			<?php
		}
	);

	return;
}

/**
 * Load Core Loader Class
 */
require_once PRDSM_PATH . 'includes/class-prdsm-loader.php';

/**
 * Initialize Plugin
 */
function prdsm_init_plugin() {
	if ( class_exists( 'PRDSM_Loader' ) ) {
		new PRDSM_Loader();
	}
}
add_action( 'plugins_loaded', 'prdsm_init_plugin' );