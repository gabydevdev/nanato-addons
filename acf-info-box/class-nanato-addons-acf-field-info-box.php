<?php
/**
 * ACF Info Box Field Type
 *
 * A custom ACF field for creating styled info boxes with optional icons.
 *
 * @package Nanato_Addons
 * @subpackage ACF_Fields
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF Info Box Field Class
 *
 * Extends the ACF field base class to provide a comprehensive info box configuration interface
 * with support for headlines, content, icons, styling, and HTML attributes.
 *
 * @since 1.0.0
 */
class nanato_addons_acf_field_info_box extends \acf_field {
	/**
	 * Controls field type visibility in REST requests.
	 *
	 * @var bool
	 * @since 1.0.0
	 */
	public $show_in_rest = true;

	/**
	 * Environment values relating to the theme or plugin.
	 *
	 * @var array Plugin or theme context such as 'url' and 'version'.
	 * @since 1.0.0
	 */
	private $env;

	/**
	 * Initialize the field type.
	 *
	 * Sets up field properties, defaults, and environment configuration.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		/**
		 * Field type reference used in PHP and JS code.
		 *
		 * No spaces. Underscores allowed.
		 */
		$this->name = 'info-box';

		/**
		 * Field type label.
		 *
		 * For public-facing UI. May contain spaces.
		 */
		$this->label = __( 'Info Box', 'nanato-addons' );

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'content'; // basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME

		// Field type description
		$this->description = __( 'Create informational content boxes with headlines, text, icons and styling options.', 'nanato-addons' );

		// Documentation URLs (optional)
		$this->doc_url      = '';
		$this->tutorial_url = '';

		// Set default field values
		$this->set_defaults();

		// Set JavaScript localization strings
		$this->set_localization();

		// Set environment configuration
		$this->set_environment();

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Set default field values.
	 *
	 * @since 1.0.0
	 */
	private function set_defaults() {
		$this->defaults = array(
			'headline'    => '',
			'text'        => '',
			'icon'        => '',
			'type'        => 'info',
			'style'       => 'default',
			'html_id'     => '',
			'css_classes' => '',
		);
	}

	/**
	 * Set JavaScript localization strings.
	 *
	 * @since 1.0.0
	 */
	private function set_localization() {
		$this->l10n = array(
			'error' => __( 'Error! Please enter a valid value', 'nanato-addons' ),
		);
	}

	/**
	 * Set environment configuration.
	 *
	 * @since 1.0.0
	 */
	private function set_environment() {
		$this->env = array(
			'url'     => NANATO_ADDONS_URL . 'acf-info-box',
			'version' => NANATO_ADDONS_VERSION,
		);
	}

	/**
	 * Render field settings.
	 *
	 * Settings to display when users configure a field of this type.
	 * These settings appear on the ACF "Edit Field Group" admin page.
	 *
	 * @param array $field The field settings array.
	 * @return void
	 * @since 1.0.0
	 */
	public function render_field_settings( $field ) {
		// Default settings
		$field = wp_parse_args(
			$field,
			array(
				'show_headline'        => 1,
				'show_icon'            => 1,
				'show_layout_style'    => 1,
				'show_html_attributes' => 1,
			)
		);

		// Show Headline Field
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'Show Headline Field', 'nanato-addons' ),
				'instructions'  => __( 'Allow users to add a headline to the info box.', 'nanato-addons' ),
				'name'          => 'show_headline',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			)
		);

		// Show Icon Field
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'Show Icon Field', 'nanato-addons' ),
				'instructions'  => __( 'Allow users to upload an icon for the info box.', 'nanato-addons' ),
				'name'          => 'show_icon',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			)
		);

		// Show Layout & Style Section
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'Show Layout & Style Options', 'nanato-addons' ),
				'instructions'  => __( 'Allow users to customize type, style, and colors of the info box.', 'nanato-addons' ),
				'name'          => 'show_layout_style',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			)
		);

		// Show HTML Attributes Section
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'Show HTML Attributes', 'nanato-addons' ),
				'instructions'  => __( 'Allow users to set custom ID and CSS classes for the info box.', 'nanato-addons' ),
				'name'          => 'show_html_attributes',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			)
		);
	}

	/**
	 * Render the field interface.
	 *
	 * HTML content to show when a publisher edits the field on the edit screen.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 * @since 1.0.0
	 */
	public function render_field( $field ) {
		// Initialize field values with defaults
		$field['value'] = $this->initialize_field_values( $field['value'] );

		// Set default field settings
		$field = wp_parse_args(
			$field,
			array(
				'show_headline'        => 1,
				'show_icon'            => 1,
				'show_layout_style'    => 1,
				'show_html_attributes' => 1,
			)
		);
		?>
		<div class="acf-info-box" id="acf-<?php echo esc_attr( $field['key'] ); ?>" data-key="<?php echo esc_attr( $field['key'] ); ?>">
			<fieldset>
				<?php
				$this->render_content_section( $field );

				if ( $field['show_layout_style'] ) {
					$this->render_layout_style_section( $field );
				}

				if ( $field['show_html_attributes'] ) {
					$this->render_html_attributes_section( $field );
				}
				?>
			</fieldset>
		</div>
		<?php
	}

	/**
	 * Initialize field values with defaults.
	 *
	 * @param array $values Current field values.
	 * @return array Initialized field values.
	 * @since 1.0.0
	 */
	private function initialize_field_values( $values ) {
		if ( ! is_array( $values ) ) {
			$values = array();
		}

		foreach ( $this->defaults as $key => $default_value ) {
			if ( ! isset( $values[ $key ] ) ) {
				$values[ $key ] = $default_value;
			}
		}

		return $values;
	}

	/**
	 * Render the Content section.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 * @since 1.0.0
	 */
	private function render_content_section( $field ) {
		?>
		<!-- Content Section -->
		<div class="acf-info-box-section">
			<div class="acf-info-box-section-title"><?php echo esc_html__( 'Content', 'nanato-addons' ); ?></div>
			<div class="acf-info-box-grid">
				<?php if ( $field['show_headline'] ) : ?>
					<!-- Headline -->
					<div class="acf-info-box-subfield acf-info-box-headline acf-info-box-full-width">
						<div class="acf-label">
							<label for="<?php echo esc_attr( $field['name'] ); ?>_headline">
								<?php echo esc_html__( 'Headline', 'nanato-addons' ); ?>
							</label>
						</div>
						<div class="acf-input">
							<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[headline]" id="<?php echo esc_attr( $field['name'] ); ?>_headline" value="<?php echo esc_attr( $field['value']['headline'] ); ?>" placeholder="<?php echo esc_attr__( 'Enter info box headline', 'nanato-addons' ); ?>" />
						</div>
					</div>
				<?php endif; ?>

				<!-- Text Content -->
				<div class="acf-info-box-subfield acf-info-box-text acf-info-box-full-width">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_text"><?php echo esc_html__( 'Text Content', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<textarea name="<?php echo esc_attr( $field['name'] ); ?>[text]" id="<?php echo esc_attr( $field['name'] ); ?>_text" rows="4" placeholder="<?php echo esc_attr__( 'Enter info box content', 'nanato-addons' ); ?>"><?php echo esc_textarea( $field['value']['text'] ); ?></textarea>
					</div>
				</div>

				<?php if ( $field['show_icon'] ) : ?>
					<!-- Icon -->
					<div class="acf-info-box-subfield acf-info-box-icon acf-info-box-full-width">
						<div class="acf-label">
							<label for="<?php echo esc_attr( $field['name'] ); ?>_icon"><?php echo esc_html__( 'Icon', 'nanato-addons' ); ?></label>
							<p class="description"><?php echo esc_html__( 'Select an SVG icon or image file', 'nanato-addons' ); ?></p>
						</div>
						<div class="acf-input">
							<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>[icon]" id="<?php echo esc_attr( $field['name'] ); ?>_icon" value="<?php echo esc_attr( $field['value']['icon'] ); ?>" />
							<div class="icon-controls">
								<button type="button" class="button select-icon"><?php echo esc_html__( 'Select Icon', 'nanato-addons' ); ?></button>
								<button type="button" class="button remove-icon" style="<?php echo empty( $field['value']['icon'] ) ? 'display:none;' : ''; ?>"><?php echo esc_html__( 'Remove', 'nanato-addons' ); ?></button>
							</div>
							<div class="icon-preview">
								<?php if ( ! empty( $field['value']['icon'] ) ) : ?>
									<img src="<?php echo esc_url( wp_get_attachment_image_url( $field['value']['icon'], 'thumbnail' ) ); ?>" alt="" />
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Layout & Style section.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 * @since 1.0.0
	 */
	private function render_layout_style_section( $field ) {
		?>
		<!-- Layout & Style Section -->
		<div class="acf-info-box-section">
			<div class="acf-info-box-section-title"><?php echo esc_html__( 'Layout & Style', 'nanato-addons' ); ?></div>
			<div class="acf-info-box-grid">
				<!-- Info Box Type -->
				<div class="acf-info-box-subfield acf-info-box-type">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_type"><?php echo esc_html__( 'Type', 'nanato-addons' ); ?></label>
						<p class="description"><?php echo esc_html__( 'Info box purpose/type', 'nanato-addons' ); ?></p>
					</div>
					<div class="acf-input">
						<?php
						$type_options = apply_filters(
							'nanato_addons_acf_field_info_box_type_options',
							array(
								'info'    => __( 'Info', 'nanato-addons' ),
								'success' => __( 'Success', 'nanato-addons' ),
								'warning' => __( 'Warning', 'nanato-addons' ),
								'error'   => __( 'Error', 'nanato-addons' ),
								'note'    => __( 'Note', 'nanato-addons' ),
							)
						);
						?>
						<select name="<?php echo esc_attr( $field['name'] ); ?>[type]" id="<?php echo esc_attr( $field['name'] ); ?>_type">
							<?php foreach ( $type_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $field['value']['type'], $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<!-- Style -->
				<div class="acf-info-box-subfield acf-info-box-style">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_style"><?php echo esc_html__( 'Style', 'nanato-addons' ); ?></label>
						<p class="description"><?php echo esc_html__( 'Visual style variation', 'nanato-addons' ); ?></p>
					</div>
					<div class="acf-input">
						<?php
						$style_options = apply_filters(
							'nanato_addons_acf_field_info_box_style_options',
							array(
								'default'  => __( 'Default', 'nanato-addons' ),
								'bordered' => __( 'Bordered', 'nanato-addons' ),
								'minimal'  => __( 'Minimal', 'nanato-addons' ),
								'card'     => __( 'Card', 'nanato-addons' ),
							)
						);
						?>
						<select name="<?php echo esc_attr( $field['name'] ); ?>[style]" id="<?php echo esc_attr( $field['name'] ); ?>_style">
							<?php foreach ( $style_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $field['value']['style'], $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the HTML Attributes section.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 * @since 1.0.0
	 */
	private function render_html_attributes_section( $field ) {
		?>
		<!-- HTML Attributes Section -->
		<div class="acf-info-box-section">
			<div class="acf-info-box-section-title"><?php echo esc_html__( 'HTML Attributes', 'nanato-addons' ); ?></div>
			<div class="acf-info-box-grid">
				<!-- HTML ID -->
				<div class="acf-info-box-subfield acf-info-box-id">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_html_id"><?php echo esc_html__( 'ID', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[html_id]" id="<?php echo esc_attr( $field['name'] ); ?>_html_id" value="<?php echo esc_attr( $field['value']['html_id'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g., my-info-box', 'nanato-addons' ); ?>" />
					</div>
				</div>

				<!-- CSS Classes -->
				<div class="acf-info-box-subfield acf-info-box-classes">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_css_classes"><?php echo esc_html__( 'CSS Classes', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[css_classes]" id="<?php echo esc_attr( $field['name'] ); ?>_css_classes" value="<?php echo esc_attr( $field['value']['css_classes'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g., info-box custom-class highlight', 'nanato-addons' ); ?>" />
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue CSS and JavaScript for the field.
	 *
	 * Callback for admin_enqueue_script.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function input_admin_enqueue_scripts() {
		$url     = trailingslashit( $this->env['url'] );
		$version = $this->env['version'];

		// Register and enqueue field assets
		wp_enqueue_script(
			'nanato-addons-info-box',
			"{$url}assets/js/field.js",
			array( 'acf-input' ),
			$version
		);

		wp_enqueue_style(
			'nanato-addons-info-box',
			"{$url}assets/css/field.css",
			array( 'acf-input' ),
			$version
		);
	}

	/**
	 * Validate field value.
	 *
	 * @param bool   $valid Whether the value is valid.
	 * @param mixed  $value The field value.
	 * @param array  $field The field settings.
	 * @param string $input_name The input name.
	 * @return bool|string True if valid, error message if invalid.
	 * @since 1.0.0
	 */
	public function validate_value( $valid, $value, $field, $input_name ) {
		// Set default field settings
		$field = wp_parse_args(
			$field,
			array(
				'show_headline' => 1,
			)
		);

		return $valid;
	}

	/**
	 * Format field value for frontend display.
	 *
	 * @param mixed $value The field value.
	 * @param int   $post_id The post ID.
	 * @param array $field The field settings.
	 * @return mixed The formatted value.
	 * @since 1.0.0
	 */
	public function format_value( $value, $post_id, $field ) {
		// Return early if no value
		if ( empty( $value ) ) {
			return $value;
		}

		// Ensure we have an array
		if ( ! is_array( $value ) ) {
			return $value;
		}

		// Initialize with defaults
		$value = $this->initialize_field_values( $value );

		// Process icon if it's an attachment ID
		if ( ! empty( $value['icon'] ) && is_numeric( $value['icon'] ) ) {
			$value['icon_url'] = wp_get_attachment_image_url( $value['icon'], 'thumbnail' );
			$value['icon_alt'] = get_post_meta( $value['icon'], '_wp_attachment_image_alt', true );
		}

		return $value;
	}

	/**
	 * Load field value from database.
	 *
	 * @param mixed $value The field value.
	 * @param int   $post_id The post ID.
	 * @param array $field The field settings.
	 * @return mixed The loaded value.
	 * @since 1.0.0
	 */
	public function load_value( $value, $post_id, $field ) {
		// Initialize with defaults if empty
		if ( empty( $value ) ) {
			$value = $this->defaults;
		}

		return $value;
	}

	/**
	 * Update field value in database.
	 *
	 * @param mixed $value The field value.
	 * @param int   $post_id The post ID.
	 * @param array $field The field settings.
	 * @return mixed The updated value.
	 * @since 1.0.0
	 */
	public function update_value( $value, $post_id, $field ) {
		// Remove empty string values to keep database clean
		if ( is_array( $value ) ) {
			$value = array_filter(
				$value,
				function ( $val ) {
					return $val !== '';
				}
			);
		}

		return $value;
	}

	/**
	 * Get field settings with defaults for use with get_info_box_data.
	 *
	 * @param array $field The ACF field array.
	 * @return array Field settings with defaults.
	 * @since 1.0.0
	 */
	public static function get_field_settings( $field ) {
		if ( empty( $field ) || ! is_array( $field ) ) {
			return array(
				'show_headline'        => 1,
				'show_icon'            => 1,
				'show_layout_style'    => 1,
				'show_html_attributes' => 1,
			);
		}

		return array(
			'show_headline'        => ! empty( $field['show_headline'] ) ? 1 : 0,
			'show_icon'            => ! empty( $field['show_icon'] ) ? 1 : 0,
			'show_layout_style'    => ! empty( $field['show_layout_style'] ) ? 1 : 0,
			'show_html_attributes' => ! empty( $field['show_html_attributes'] ) ? 1 : 0,
		);
	}

	/**
	 * Get processed info box data.
	 *
	 * This is a helper method for theme developers to get processed info box data.
	 *
	 * @param array $value The field value.
	 * @param array $field_settings Optional. Field settings to respect visibility options.
	 * @return array|null Processed info box data or null if empty.
	 * @since 1.0.0
	 */
	public static function get_info_box_data( $value, $field_settings = array() ) {
		if ( empty( $value ) || ! is_array( $value ) ) {
			return null;
		}

		// Set default field settings
		$field_settings = wp_parse_args(
			$field_settings,
			array(
				'show_headline'        => 1,
				'show_icon'            => 1,
				'show_layout_style'    => 1,
				'show_html_attributes' => 1,
			)
		);

		// Process the data
		$data = array(
			'text' => ! empty( $value['text'] ) ? $value['text'] : '',
		);

		// Add headline only if enabled
		if ( $field_settings['show_headline'] ) {
			$data['headline'] = ! empty( $value['headline'] ) ? $value['headline'] : '';
		}

		// Add layout & style data only if enabled
		if ( $field_settings['show_layout_style'] ) {
			$data['type']  = ! empty( $value['type'] ) ? $value['type'] : 'info';
			$data['style'] = ! empty( $value['style'] ) ? $value['style'] : 'default';
		} else {
			// Use defaults if layout & style is disabled
			$data['type']  = 'info';
			$data['style'] = 'default';
		}

		// Add HTML attributes only if enabled
		if ( $field_settings['show_html_attributes'] ) {
			$data['attributes'] = array(
				'id'      => ! empty( $value['id'] ) ? $value['id'] : '',
				'classes' => ! empty( $value['classes'] ) ? $value['classes'] : '',
			);
		} else {
			$data['attributes'] = array(
				'id'      => '',
				'classes' => '',
			);
		}

		// Add icon data only if enabled
		if ( $field_settings['show_icon'] ) {
			$data['icon'] = array(
				'id'  => ! empty( $value['icon'] ) ? $value['icon'] : '',
				'url' => '',
				'alt' => '',
			);

			// Process icon data if available
			if ( ! empty( $value['icon'] ) && is_numeric( $value['icon'] ) ) {
				$data['icon']['url'] = wp_get_attachment_image_url( $value['icon'], 'full' );
				$data['icon']['alt'] = get_post_meta( $value['icon'], '_wp_attachment_image_alt', true );
			}
		} else {
			$data['icon'] = array(
				'id'  => '',
				'url' => '',
				'alt' => '',
			);
		}

		// Build CSS classes array
		$css_classes   = array( 'nanato-info-box' );
		$css_classes[] = 'nanato-info-box--' . $data['type'];
		$css_classes[] = 'nanato-info-box--style-' . $data['style'];

		if ( ! empty( $data['attributes']['classes'] ) ) {
			$css_classes[] = $data['attributes']['classes'];
		}

		$data['css_classes'] = $css_classes;

		return $data;
	}
}
