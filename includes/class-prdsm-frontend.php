<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PRDSM_Frontend {

	private $option_name = 'prdsm_settings';

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( $this, 'render_slide_menu_container' ) );

	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_assets() {

		if ( is_admin() ) {
			return;
		}

		$settings = $this->get_settings();

		if ( empty( $settings['enable_menu'] ) || empty( $settings['selected_menu'] ) ) {
			return;
		}

		wp_enqueue_style(
			'prdsm-style',
			PRDSM_URL . 'assets/css/prdsm-style.css',
			array(),
			PRDSM_VERSION
		);

		$breakpoint = 980;

		/**
		* Lite Version
		*
		* Uses the standard 980px breakpoint.
		* The responsive breakpoint control
		* is available in Pro.
		*/
		$dynamic_css = '

			/* ==========================================
			   CUSTOM MOBILE BREAKPOINT
			========================================== */

			@media only screen and (max-width: ' . $breakpoint . 'px) {

				/* ==================================================
				   Show plugin hamburger
				================================================== */

				#prdsm-hamburger {
					display: flex;
				}

				/* ==================================================
				   CLASSIC DIVI HEADER
				   Force desktop navigation to hide
				================================================== */

				#top-menu-nav,
				#et_top_search {
					display: none !important;
				}

				/* Show Divi mobile container */
				#et_mobile_nav_menu {
					display: block !important;
				}

				/* ==================================================
				   DIVI THEME BUILDER HEADER
				================================================== */

				/* ==========================================
				   Standard Divi Menu Layouts
				   Hide desktop menu at breakpoint
				========================================== */

				.et_pb_menu:not(.et_pb_menu--style-inline_centered_logo)
				.et_pb_menu__menu {
					display: none !important;
				}

				/* ==========================================
				   Inline Centered Logo Layout
				   Divi hides the normal logo wrapper and
				   injects a logo-slot inside the menu.

				   For the slide menu breakpoint we:
				   1. Hide the entire desktop menu
				   2. Restore the original logo wrapper
				========================================== */

				.et_pb_menu.et_pb_menu--style-inline_centered_logo
				.et_pb_menu__menu {
					display: none !important;
				}

				.et_pb_menu.et_pb_menu--style-inline_centered_logo
				.et_pb_menu__logo-wrap {
					display: flex !important;
					margin-bottom: 0 !important;
				}

				/*
				=========================================
				Centered Layout
				Move logo to the left when slide menu
				is active so the custom hamburger can
				sit naturally on the right.
				=========================================
				*/

				.et_pb_menu--style-centered .et_pb_menu__logo-wrap {
				    margin-left: 0 !important;
				}

				/* Show mobile menu */
				.et_pb_menu .et_mobile_nav_menu {
					display: flex !important;
					align-items: center;
				}

				/* ==================================================
				   Hide default Divi hamburger icon
				================================================== */

				.mobile_menu_bar::before,
				.et_pb_menu__icon.et_pb_menu__search-button::before {
					display: none !important;
				}
			}

			/* ==========================================
			   DESKTOP STATE
			========================================== */

			@media only screen and (min-width: ' . ( $breakpoint + 1 ) . 'px) {

				/* Hide plugin hamburger */
				#prdsm-hamburger {
					display: none !important;
				}

				/* ==========================================
				   CLASSIC DIVI HEADER
				========================================== */

				/* Restore classic Divi desktop navigation */
				#top-menu-nav,
				#top-menu,
				#et_top_search {
					display: block !important;
					opacity: 1 !important;
					visibility: visible !important;
				}
				
				/* Restore menu list layout */
				.nav li,
				.et-menu li {
					display: inline-block !important;
				}

				/* Hide Divi mobile nav */
				#et_mobile_nav_menu {
					display: none !important;
				}

				/* ==========================================
				   DIVI THEME BUILDER HEADER
				========================================== */

				/* Restore desktop menu */
				.et_pb_menu .et_pb_menu__menu {
					display: flex !important;
				}

				/* Hide Divi mobile menu */
				.et_pb_menu .et_mobile_nav_menu {
					display: none !important;
				}
			}
		';

		wp_add_inline_style(
			'prdsm-style',
			$dynamic_css
		);

		wp_enqueue_script(
			'prdsm-script',
			PRDSM_URL . 'assets/js/prdsm-script.js',
			array(),
			PRDSM_VERSION,
			true
		);

		wp_localize_script(
			'prdsm-script',
			'prdsmSettings',
			array(
				'hamburgerColor' => isset( $settings['hamburger_color'] ) ? $settings['hamburger_color'] : '',
				'closeIconColor' => isset( $settings['close_icon_color'] ) ? $settings['close_icon_color'] : '',
				'openLabel'      => __( 'Open Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ),
				'closeLabel'     => __( 'Close Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ),
				'slideDirection' => 'right',
				'hamburgerStyle' => 'classic',
			)
		);
	}

	/**
	 * Get saved plugin settings
	 */
	private function get_settings() {

		$defaults = array(
			'enable_menu'        => 1,
			'selected_menu'      => 0,
			'menu_width'         => 300,
			'close_on_click'     => 1,
			'hamburger_color'    => '#000000',
			'close_icon_color'   => '#000000',
			'menu_bg_color'      => '#333333',
			'menu_font_size'     => 16,
			'menu_text_color'    => '#000000',
			'menu_font_weight'   => '400',
			'menu_inner_padding' => 30,
			'menu_item_spacing'  => 12,
		);

		return get_option( $this->option_name, $defaults );
	}

	/**
	 * Render slide menu container in footer
	 */
	public function render_slide_menu_container() {

		if ( is_admin() ) {
			return;
		}

		$settings = $this->get_settings();

		if ( empty( $settings['enable_menu'] ) || empty( $settings['selected_menu'] ) ) {
			return;
		}

		?>

		<div
			id="prdsm-overlay"
			style="
				background-color: rgba(51,51,51,0.6);
				--prdsm-animation-speed: 300ms;
			"
		></div>

		<div
			id="prdsm-slide-menu"
			class="prdsm-direction-right"
			style="
				width: <?php echo esc_attr( $settings['menu_width'] ); ?>px;
				background-color: <?php echo esc_attr( $settings['menu_bg_color'] ); ?>;
				--prdsm-font-size: <?php echo esc_attr( $settings['menu_font_size'] ); ?>px;
				--prdsm-text-color: <?php echo esc_attr( $settings['menu_text_color'] ); ?>;
				--prdsm-font-weight: <?php echo esc_attr( $settings['menu_font_weight'] ); ?>;
				--prdsm-inner-padding: <?php echo esc_attr( $settings['menu_inner_padding'] ); ?>px;
				--prdsm-item-spacing: <?php echo esc_attr( $settings['menu_item_spacing'] ); ?>px;
				--prdsm-animation-speed: 300ms;
			">

			<?php
			wp_nav_menu( array(
				'menu'        => absint( $settings['selected_menu'] ),
				'container'   => false,
				'menu_class'  => 'prdsm-menu-list',
				'fallback_cb' => false,
			) );
			?>

		</div>

		<?php
	}
}