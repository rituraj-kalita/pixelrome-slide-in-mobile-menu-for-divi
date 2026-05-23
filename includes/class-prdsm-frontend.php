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
				'closeOnClick'   => ! empty( $settings['close_on_click'] ) ? 1 : 0,
				'hamburgerColor' => isset( $settings['hamburger_color'] ) ? $settings['hamburger_color'] : '',
				'closeIconColor' => isset( $settings['close_icon_color'] ) ? $settings['close_icon_color'] : '',
				'openLabel'      => __( 'Open Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ),
				'closeLabel'     => __( 'Close Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ),
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

		<div id="prdsm-overlay"></div>

		<div id="prdsm-slide-menu"
			style="
				width: <?php echo esc_attr( $settings['menu_width'] ); ?>px;
				background-color: <?php echo esc_attr( $settings['menu_bg_color'] ); ?>;
				--prdsm-font-size: <?php echo esc_attr( $settings['menu_font_size'] ); ?>px;
				--prdsm-text-color: <?php echo esc_attr( $settings['menu_text_color'] ); ?>;
				--prdsm-font-weight: <?php echo esc_attr( $settings['menu_font_weight'] ); ?>;
				--prdsm-inner-padding: <?php echo esc_attr( $settings['menu_inner_padding'] ); ?>px;
				--prdsm-item-spacing: <?php echo esc_attr( $settings['menu_item_spacing'] ); ?>px;
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