<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PRDSM_Loader {

	public function __construct() {

		if ( $this->is_divi_active() ) {
			$this->load_dependencies();
		} else {
			add_action( 'admin_notices', array( $this, 'divi_not_active_notice' ) );
		}
	}

	/**
	 * Check if Divi theme (or child theme) is active
	 */
	private function is_divi_active() {

	$theme = wp_get_theme();

	if ( 'Divi' === $theme->get_stylesheet() ) {
		return true;
	}

	if ( $theme->parent() && 'Divi' === $theme->parent()->get_stylesheet() ) {
		return true;
	}

	return false;
}

	/**
	 * Load required classes
	 */
	private function load_dependencies() {

		require_once PRDSM_PATH . 'includes/class-prdsm-admin.php';
		require_once PRDSM_PATH . 'includes/class-prdsm-frontend.php';

		if ( is_admin() ) {
			new PRDSM_Admin();
		}

		new PRDSM_Frontend();
	}

	/**
	 * Admin notice if Divi not active
	 */
	public function divi_not_active_notice() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		echo '<div class="notice notice-error"><p>';
		echo '<strong>' . esc_html__( 'PIXELROME – Slide-In Mobile Menu for Divi', 'pixelrome-slide-in-mobile-menu-for-divi' ) . '</strong> ';
		echo esc_html__( 'requires the Divi theme to be active.', 'pixelrome-slide-in-mobile-menu-for-divi' );
		echo '</p></div>';
	}
}