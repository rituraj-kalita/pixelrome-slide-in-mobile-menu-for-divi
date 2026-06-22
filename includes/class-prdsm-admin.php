<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PRDSM_Admin {

	private $option_name = 'prdsm_settings';

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		add_filter(
			'plugin_action_links_' . plugin_basename( PRDSM_PLUGIN_FILE ),
			array( $this, 'add_settings_link' )
		);
	}

	/**
	 * Load admin assets only on plugin settings page.
	 *
	 * Prevents unnecessary asset loading across wp-admin.
	 *
	 * @param string $hook Current admin page hook suffix.
	 *
	 * @return void
	 */
	public function enqueue_admin_assets( $hook ) {
	
		// Only load assets on plugin settings page.
		if ( 'toplevel_page_prdsm-settings' !== $hook ) {
			return;
		}
	
		wp_enqueue_style(
			'prdsm-admin-style',
			PRDSM_URL . 'assets/css/prdsm-admin.css',
			array(),
			PRDSM_VERSION
		);
	
		wp_enqueue_script(
			'prdsm-admin-script',
			PRDSM_URL . 'assets/js/prdsm-admin.js',
			array(),
			PRDSM_VERSION,
			true
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
			'slide_direction',
			esc_html__( 'Slide Direction', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_slide_direction_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		/**
		 * Responsive Breakpoint Control
		 *
		 * Controls the viewport width where
		 * the custom mobile menu activates.
		 */
		add_settings_field(
			'breakpoint',
			esc_html__( 'Responsive Breakpoint', 'pixelrome-slide-in-mobile-menu-for-divi' ),
			array( $this, 'render_breakpoint_field' ),
			'prdsm-settings',
			'prdsm_main_section'
		);

		add_settings_field(
			'hamburger_style',
			__(
				'Hamburger Icon Style',
				'pixelrome-slide-in-mobile-menu-for-divi'
			),
			array( $this, 'render_hamburger_style_field' ),
			'prdsm-settings',
			'prdsm_design_section'
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

	/**
	 * Render overlay color field.
	 *
	 * @return void
	 */
	public function render_overlay_color_field() {

		$options = $this->get_settings();

		?>

		<input
			type="color"
			name="<?php echo esc_attr( $this->option_name ); ?>[overlay_color]"
			value="<?php echo esc_attr( $options['overlay_color'] ); ?>"
		/>

		<?php
	}

	/**
	 * Render overlay opacity field.
	 *
	 * @return void
	 */
	public function render_overlay_opacity_field() {
	    ?>
	    <input
	        type="range"
	        min="0"
	        max="100"
	        step="1"
	        class="prdsm-range-slider"
	        id="prdsm-overlay-opacity-slider"
	        name="<?php echo esc_attr( $this->option_name ); ?>[overlay_opacity]"
	        value="60"
	    />
		
	    <?php
	}

	/**
	 * Render animation speed field.
	 *
	 * @return void
	 */
	public function render_animation_speed_field() {

		$options = $this->get_settings();

		$animation_speed = isset(
			$options['animation_speed']
		)
			? absint(
				$options['animation_speed']
			)
			: 300;

		?>

		<div class="prdsm-range-control">

			<input
				type="range"
				min="100"
				max="1000"
				step="10"
				class="prdsm-range-slider"
				id="prdsm-animation-speed-slider"
				name="<?php echo esc_attr(
					$this->option_name
				); ?>[animation_speed]"
				value="<?php echo esc_attr(
					$animation_speed
				); ?>"
			/>

			<div class="prdsm-range-values">

				<span>100ms</span>

				<div class="prdsm-breakpoint-input-wrap">

					<input
						type="number"
						min="100"
						max="1000"
						step="10"
						class="prdsm-breakpoint-input"
						id="prdsm-animation-speed-input"
						value="<?php echo esc_attr(
							$animation_speed
						); ?>"
					/>

					<span class="prdsm-breakpoint-unit">
						ms
					</span>

				</div>

				<span>1000ms</span>

			</div>

		</div>

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

	/**
	 * Render slide direction select field.
	 *
	 * Controls the direction from which
	 * the mobile menu panel enters.
	 *
	 * @return void
	 */
	public function render_slide_direction_field() {

		$options = $this->get_settings();

		?>

		<select
			name="<?php echo esc_attr( $this->option_name ); ?>[slide_direction]"
		>

			<option
				value="right"
				<?php selected( $options['slide_direction'], 'right' ); ?>
			>
				<?php esc_html_e( 'Right to Left', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>

			<option
				value="left"
				<?php selected( $options['slide_direction'], 'left' ); ?>
			>
				<?php esc_html_e( 'Left to Right', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>

		</select>

		<?php
	}

	/**
	 * Render responsive breakpoint slider field.
	 */
	public function render_breakpoint_field() {

		$options = $this->get_settings();

		$breakpoint = isset( $options['breakpoint'] )
			? absint( $options['breakpoint'] )
			: 980;

		?>

		<div class="prdsm-range-control">

			<input
				type="range"
				min="480"
				max="1600"
				step="1"
				class="prdsm-range-slider"
				id="prdsm-breakpoint-slider"
				name="<?php echo esc_attr( $this->option_name ); ?>[breakpoint]"
				value="<?php echo esc_attr( $breakpoint ); ?>"
			/>

			<div class="prdsm-range-values">

				<span class="prdsm-range-min">
					480px
				</span>

				<div class="prdsm-breakpoint-input-wrap">

					<input
						type="number"
						min="480"
						max="1600"
						step="1"
						class="prdsm-breakpoint-input"
						id="prdsm-breakpoint-input"
						value="<?php echo esc_attr( $breakpoint ); ?>"
					/>
							
					<span class="prdsm-breakpoint-unit">
						px
					</span>
							
				</div>

				<span class="prdsm-range-max">
					1600px
				</span>

			</div>

		</div>

		<?php
	}

	/**
	 * Render hamburger icon style field.
	 *
	 * @return void
	 */
	public function render_hamburger_style_field() {

		$options = $this->get_settings();

		$current_style = isset( $options['hamburger_style'] )
			? sanitize_key( $options['hamburger_style'] )
			: 'classic';

		?>

		<select
			id="prdsm-hamburger-style"
			name="<?php echo esc_attr( $this->option_name ); ?>[hamburger_style]"
		>

			<option value="classic" <?php selected( $current_style, 'classic' ); ?>>
				<?php esc_html_e( 'Classic', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>

			<option value="modern" <?php selected( $current_style, 'modern' ); ?>>
				<?php esc_html_e( 'Modern', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>

			<option value="split" <?php selected( $current_style, 'split' ); ?>>
				<?php esc_html_e( 'Split', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>

			<option value="two-bar" <?php selected( $current_style, 'two-bar' ); ?>>
				<?php esc_html_e( 'Two Bar', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>
						
			<option value="right-align" <?php selected( $current_style, 'right-align' ); ?>>
				<?php esc_html_e( 'Right Align', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
			</option>

		</select>

		<?php
	}

	public function render_settings_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>

		<div class="wrap">

			<div class="prdsm-admin-page-header">

				<div class="prdsm-admin-page-title">
					<?php esc_html_e(
						'PIXELROME – Slide-In Mobile Menu for Divi',
						'pixelrome-slide-in-mobile-menu-for-divi'
					); ?>
				</div>
							
				<p class="prdsm-admin-page-description">
					<?php esc_html_e(
						'Create beautiful responsive slide-in navigation menus for Divi with pixel-perfect breakpoint control, sticky header support, and modern customization options.',
						'pixelrome-slide-in-mobile-menu-for-divi'
					); ?>
				</p>
							
			</div>

			<h1 style="display:none"></h1>  <!-- anchor for WP notice mover -->

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

			<div class="prdsm-admin-layout">
				<form method="post" action="options.php">
					<?php
					settings_fields( 'prdsm_settings_group' );
					$this->render_settings_fields();
					submit_button();
					?>
				</form>

				

			</div>
		</div>

		<?php
	}

	/**
	 * Render PRO badge.
	 *
	 * @return void
	 */
	private function render_pro_badge() {
		?>
		<span class="prdsm-pro-badge">
			PRO
		</span>
		<?php
	}

	/**
	 * Open PRO field wrapper.
	 *
	 * @return void
	 */
	private function pro_field_start( $field_class = '' ) {
		echo '<div class="prdsm-pro-field ' . esc_attr( $field_class ) . '">';
	}

	/**
	 * Close PRO field wrapper.
	 *
	 * @return void
	 */
	private function pro_field_end() {
		?>
		</div>
		<?php
	}
	
	/**
	 * Render custom settings UI.
	 *
	 * We intentionally render settings manually instead of relying on
	 * do_settings_sections() so we can build a scalable accordion-based
	 * admin interface for future Pro features.
	 */
	public function render_settings_fields() {

		?>

		<div class="prdsm-admin-sections">

			<!-- General Settings Section -->
			<div class="prdsm-admin-section active">

				<button
					type="button"
					class="prdsm-admin-section-toggle"
					aria-expanded="true"
				>
					<span class="prdsm-admin-section-title">
						<?php esc_html_e( 'General Settings', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
					</span>

					<span class="prdsm-admin-section-icon"></span>
				</button>

				<div class="prdsm-admin-section-content">

					<table class="form-table" role="presentation">

						<!-- Enable Menu -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Enable Slide Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>

							<td>
								<?php $this->render_enable_field(); ?>
							</td>
						</tr>

						<!-- WordPress Menu Selection -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Select Menu', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>

							<td>
								<?php $this->render_menu_field(); ?>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<?php esc_html_e( 'Responsive Breakpoint', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>

							<td>

								<?php $this->pro_field_start( 'prdsm-pro-responsive-breakpoint' ); ?>

									<?php $this->render_breakpoint_field(); ?>
									<?php $this->render_pro_badge(); ?>

								<?php $this->pro_field_end(); ?>

							</td>
						</tr>

					</table>

				</div>

			</div>

			<!-- Layout Settings Section -->
			<div class="prdsm-admin-section">

				<button
					type="button"
					class="prdsm-admin-section-toggle"
					aria-expanded="false"
				>
					<span class="prdsm-admin-section-title">
						<?php esc_html_e( 'Layout Settings', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
					</span>

					<span class="prdsm-admin-section-icon"></span>
				</button>

				<div class="prdsm-admin-section-content">

					<table class="form-table" role="presentation">

						<!-- Menu Width -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Menu Width (px)', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>

							<td>
								<?php $this->render_width_field(); ?>
							</td>
						</tr>

						<!-- Menu Inner Padding -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Menu Inner Padding (px)', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>

							<td>
								<?php $this->render_inner_padding_field(); ?>
							</td>
						</tr>

						<!-- Menu Item Spacing -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Menu Item Vertical Spacing (px)', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>

							<td>
								<?php $this->render_item_spacing_field(); ?>
							</td>
						</tr>

						<!-- Slide Direction -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Slide Direction', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>
										
							<td>

								<?php $this->pro_field_start( 'prdsm-pro-slide-direction' ); ?>
											
									<?php $this->render_slide_direction_field(); ?>
									<?php $this->render_pro_badge(); ?>
											
								<?php $this->pro_field_end(); ?>
											
							</td>
						</tr>

					</table>

				</div>

			</div>

			<!-- Design Settings Section -->
			<div class="prdsm-admin-section">
							
				<button
					type="button"
					class="prdsm-admin-section-toggle"
					aria-expanded="false"
				>
					<span class="prdsm-admin-section-title">
						<?php esc_html_e( 'Design Settings', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
					</span>
							
					<span class="prdsm-admin-section-icon"></span>
				</button>
							
				<div class="prdsm-admin-section-content">
							
					<table class="form-table" role="presentation">
							
						<!-- Hamburger Icon Color -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Hamburger Icon Color', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>
							
							<td>
								<?php $this->render_hamburger_color_field(); ?>
							</td>
						</tr>
							
						<!-- Close Icon Color -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Close Icon Color', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>
							
							<td>
								<?php $this->render_close_icon_color_field(); ?>
							</td>
						</tr>
							
						<!-- Menu Background Color -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Menu Background Color', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>
							
							<td>
								<?php $this->render_menu_bg_color_field(); ?>
							</td>
						</tr>
							
						<!-- Menu Text Color -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Menu Text Color', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>
							
							<td>
								<?php $this->render_text_color_field(); ?>
							</td>
						</tr>
							
						<!-- Menu Font Size -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Menu Font Size (px)', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>
							
							<td>
								<?php $this->render_font_size_field(); ?>
							</td>
						</tr>
							
						<!-- Menu Font Weight -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Menu Font Weight', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>
							
							<td>
								<?php $this->render_font_weight_field(); ?>
							</td>
						</tr>

						<!-- Overlay Color -->
						<tr>
							<th scope="row">
								<?php esc_html_e(
									'Overlay Color',
									'pixelrome-slide-in-mobile-menu-for-divi'
								); ?>
							</th>

							<td>
								
								<?php $this->pro_field_start( 'prdsm-pro-overlay-color' ); ?>

									<?php $this->render_overlay_color_field(); ?>
									<?php $this->render_pro_badge(); ?>

								<?php $this->pro_field_end(); ?>

							</td>
						</tr>

						<!-- Overlay Opacity -->
						<tr>
							<th scope="row">
								<?php esc_html_e(
									'Overlay Opacity',
									'pixelrome-slide-in-mobile-menu-for-divi'
								); ?>
							</th>

							<td>
								
								<?php $this->pro_field_start( 'prdsm-pro-overlay-opacity' ); ?>

									<?php $this->render_overlay_opacity_field(); ?>
									<?php $this->render_pro_badge(); ?>
												
								<?php $this->pro_field_end(); ?>

							</td>
						</tr>

						<!-- Hamburger Icon Style -->
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Hamburger Icon Style', 'pixelrome-slide-in-mobile-menu-for-divi' ); ?>
							</th>
										
							<td>
								
								<?php $this->pro_field_start( 'prdsm-pro-hamburger-icon-style' ); ?>

									<?php $this->render_hamburger_style_field(); ?>
									<?php $this->render_pro_badge(); ?>

								<?php $this->pro_field_end(); ?>

							</td>
						</tr>
							
					</table>
							
				</div>
							
			</div>

			<!-- Animation Settings Section -->
			<div class="prdsm-admin-section">

				<button
					type="button"
					class="prdsm-admin-section-toggle"
					aria-expanded="false"
				>
					<span class="prdsm-admin-section-title">
						<?php esc_html_e(
							'Animation Settings',
							'pixelrome-slide-in-mobile-menu-for-divi'
						); ?>
					</span>

					<span class="prdsm-admin-section-icon"></span>
				</button>

				<div class="prdsm-admin-section-content">

					<table class="form-table" role="presentation">

						<tr>
							<th scope="row">
								<?php esc_html_e(
									'Animation Speed',
									'pixelrome-slide-in-mobile-menu-for-divi'
								); ?>
							</th>

							<td>
								
								<?php $this->pro_field_start( 'prdsm-pro-animation-speed' ); ?>

									<?php $this->render_animation_speed_field(); ?>
									<?php $this->render_pro_badge(); ?>

								<?php $this->pro_field_end(); ?>
							</td>
						</tr>

					</table>

				</div>

			</div>

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