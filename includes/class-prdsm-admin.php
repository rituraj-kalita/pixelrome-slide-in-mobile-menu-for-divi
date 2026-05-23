<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PRDSM_Admin {

	private $option_name = 'prdsm_settings';

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_filter(
			'plugin_action_links_' . plugin_basename( PRDSM_PLUGIN_FILE ),
			array( $this, 'add_settings_link' )
		);
	}

	/**
	 * Add top-level admin menu
	 */
	public function add_admin_menu() {

		add_menu_page(
			esc_html__( 'Slide-In Mobile Menu for Divi', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			esc_html__( 'Slide-In Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			'manage_options',
			'prdsm-settings',
			array( $this, 'render_settings_page' ),
			'dashicons-menu',
			58
		);
	}

	/**
	 * Register plugin settings
	 */
	public function register_settings() {

		register_setting(
			'prdsm_settings_group',
			$this->option_name,
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'prdsm_main_section',
			esc_html__( 'Lite Settings', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			null,
			'prdsm-settings'
		);

		add_settings_field(
			'enable_menu',
			esc_html__( 'Enable Slide Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_enable_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'selected_menu',
			esc_html__( 'Select Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_menu_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'hamburger_color',
			esc_html__( 'Hamburger Icon Color', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_hamburger_color_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'close_icon_color',
			esc_html__( 'Close Icon Color', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_close_icon_color_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'menu_bg_color',
			esc_html__( 'Menu Background Color', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_menu_bg_color_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'menu_font_size',
			esc_html__( 'Menu Font Size (px)', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_font_size_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'menu_text_color',
			esc_html__( 'Menu Text Color', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_text_color_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'menu_font_weight',
			esc_html__( 'Menu Font Weight', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_font_weight_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'menu_inner_padding',
			esc_html__( 'Menu Inner Padding (px)', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_inner_padding_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'menu_item_spacing',
			esc_html__( 'Menu Item Vertical Spacing (px)', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_item_spacing_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'menu_width',
			esc_html__( 'Menu Width (px)', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_width_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'close_on_click',
			esc_html__( 'Close on Link Click', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_close_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);
	}

	/**
	 * Sanitize inputs
	 */
	public function sanitize_settings( $input ) {

		$sanitized = array();

		$sanitized['enable_menu']        = isset( $input['enable_menu'] ) ? 1 : 0;
		$sanitized['selected_menu']      = isset( $input['selected_menu'] ) ? absint( $input['selected_menu'] ) : 0;
		$sanitized['hamburger_color']    = isset( $input['hamburger_color'] ) ? sanitize_hex_color( $input['hamburger_color'] ) : '#000000';
		$sanitized['close_icon_color']   = isset( $input['close_icon_color'] ) ? sanitize_hex_color( $input['close_icon_color'] ) : '#000000';
		$sanitized['menu_bg_color']      = isset( $input['menu_bg_color'] ) ? sanitize_hex_color( $input['menu_bg_color'] ) : '#ffffff';
		$sanitized['menu_font_size']     = isset( $input['menu_font_size'] ) ? absint( $input['menu_font_size'] ) : 16;
		$sanitized['menu_text_color']    = isset( $input['menu_text_color'] ) ? sanitize_hex_color( $input['menu_text_color'] ) : '#000000';
		$sanitized['menu_font_weight']   = isset( $input['menu_font_weight'] ) ? sanitize_text_field( $input['menu_font_weight'] ) : '400';
		$sanitized['menu_inner_padding'] = isset( $input['menu_inner_padding'] ) ? absint( $input['menu_inner_padding'] ) : 30;
		$sanitized['menu_item_spacing']  = isset( $input['menu_item_spacing'] ) ? absint( $input['menu_item_spacing'] ) : 12;
		$sanitized['menu_width']         = isset( $input['menu_width'] ) ? absint( $input['menu_width'] ) : 300;
		$sanitized['close_on_click']     = isset( $input['close_on_click'] ) ? 1 : 0;

		return $sanitized;
	}

	/**
	 * Get saved settings
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

	/* --------------------------
	   Field Renderers
	-------------------------- */

	public function render_enable_field() {
		$options = $this->get_settings();
		?>
		<input type="checkbox"
			   name="<?php echo esc_attr( $this->option_name ); ?>[enable_menu]"
			   value="1"
			   <?php checked( 1, $options['enable_menu'] ); ?> />
		<?php
	}

	public function render_menu_field() {
		$options = $this->get_settings();
		$menus   = wp_get_nav_menus();
		?>
		<select name="<?php echo esc_attr( $this->option_name ); ?>[selected_menu]">
			<option value="0">
				<?php esc_html_e( '— Select a Menu —', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>
			<?php foreach ( $menus as $menu ) : ?>
				<option value="<?php echo esc_attr( $menu->term_id ); ?>"
					<?php selected( $options['selected_menu'], $menu->term_id ); ?>>
					<?php echo esc_html( $menu->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function render_hamburger_color_field() {
		$options = $this->get_settings();
		?>
		<input type="color"
			   name="<?php echo esc_attr( $this->option_name ); ?>[hamburger_color]"
			   value="<?php echo esc_attr( $options['hamburger_color'] ); ?>" />
		<?php
	}

	public function render_close_icon_color_field() {
		$options = $this->get_settings();
		?>
		<input type="color"
			   name="<?php echo esc_attr( $this->option_name ); ?>[close_icon_color]"
			   value="<?php echo esc_attr( $options['close_icon_color'] ); ?>" />
		<?php
	}

	public function render_menu_bg_color_field() {
		$options = $this->get_settings();
		?>
		<input type="color"
			   name="<?php echo esc_attr( $this->option_name ); ?>[menu_bg_color]"
			   value="<?php echo esc_attr( $options['menu_bg_color'] ); ?>" />
		<?php
	}

	public function render_font_size_field() {
		$options = $this->get_settings();
		?>
		<input type="number"
			   min="12"
			   max="40"
			   name="<?php echo esc_attr( $this->option_name ); ?>[menu_font_size]"
			   value="<?php echo esc_attr( $options['menu_font_size'] ); ?>" />
		<?php
	}

	public function render_text_color_field() {
		$options = $this->get_settings();
		?>
		<input type="color"
			   name="<?php echo esc_attr( $this->option_name ); ?>[menu_text_color]"
			   value="<?php echo esc_attr( $options['menu_text_color'] ); ?>" />
		<?php
	}

	public function render_font_weight_field() {
		$options = $this->get_settings();
		?>
		<select name="<?php echo esc_attr( $this->option_name ); ?>[menu_font_weight]">
			<option value="400" <?php selected( $options['menu_font_weight'], '400' ); ?>>
				<?php esc_html_e( 'Normal (400)', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>
			<option value="500" <?php selected( $options['menu_font_weight'], '500' ); ?>>
				<?php esc_html_e( 'Medium (500)', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>
			<option value="600" <?php selected( $options['menu_font_weight'], '600' ); ?>>
				<?php esc_html_e( 'Semi Bold (600)', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>
			<option value="700" <?php selected( $options['menu_font_weight'], '700' ); ?>>
				<?php esc_html_e( 'Bold (700)', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>
		</select>
		<?php
	}

	public function render_inner_padding_field() {
		$options = $this->get_settings();
		?>
		<input type="number"
			   min="0"
			   max="100"
			   name="<?php echo esc_attr( $this->option_name ); ?>[menu_inner_padding]"
			   value="<?php echo esc_attr( $options['menu_inner_padding'] ); ?>" />
		<?php
	}

	public function render_item_spacing_field() {
		$options = $this->get_settings();
		?>
		<input type="number"
			   min="0"
			   max="50"
			   name="<?php echo esc_attr( $this->option_name ); ?>[menu_item_spacing]"
			   value="<?php echo esc_attr( $options['menu_item_spacing'] ); ?>" />
		<?php
	}

	public function render_width_field() {
		$options = $this->get_settings();
		?>
		<input type="number"
			   min="200"
			   max="600"
			   name="<?php echo esc_attr( $this->option_name ); ?>[menu_width]"
			   value="<?php echo esc_attr( $options['menu_width'] ); ?>" />
		<?php
	}

	public function render_close_field() {
		$options = $this->get_settings();
		?>
		<input type="checkbox"
			   name="<?php echo esc_attr( $this->option_name ); ?>[close_on_click]"
			   value="1"
			   <?php checked( 1, $options['close_on_click'] ); ?> />
		<?php
	}

	public function render_settings_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>

		<div class="wrap">

			<?php
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$settings_updated = isset( $_GET['settings-updated'] )
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					? sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) )
					: '';

				if ( $settings_updated ) :
			?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e( 'Settings saved successfully.', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?></strong></p>
				</div>
			<?php endif; ?>

			<h1><?php esc_html_e( 'PIXELROME – Slide-In Mobile Menu for Divi', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?></h1>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'prdsm_settings_group' );
				do_settings_sections( 'prdsm-settings' );
				submit_button();
				?>
			</form>
		</div>

		<?php
	}

	public function add_settings_link( $links ) {

		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=prdsm-settings' ) ) . '">'
			. esc_html__( 'Settings', 'pixelrome-slide-in-mobile-menu-for-divi' )
			. '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

}