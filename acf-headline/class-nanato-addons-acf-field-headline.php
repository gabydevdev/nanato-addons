<?php
/**
 * ACF Headline Field Type
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
 * ACF Headline Field Class
 *
 * Extends the ACF field base class to provide a headline configuration interface
 * with support for title, subtitle, heading tag selection, and HTML attributes.
 *
 * @since 1.0.0
 */
class nanato_addons_acf_field_headline extends \acf_field {
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
		$this->name = 'headline';

		/**
		 * Field type label.
		 *
		 * For public-facing UI. May contain spaces.
		 */
		$this->label = __( 'Headline', 'nanato-addons' );

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'content'; // basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME

		// Field type description
		$this->description = __( '', 'nanato-addons' );

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
			'title'       => '',
			'title_tag'   => 'h2',
			'subtitle'    => '',
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
			'url'     => NANATO_ADDONS_URL . 'acf-headline',
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
				'show_subtitle'        => 1,
				'show_html_attributes' => 1,
			)
		);

		// Show Subtitle Setting
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'Show Subtitle', 'nanato-addons' ),
				'instructions'  => __( 'Allow users to add a subtitle below the main headline.', 'nanato-addons' ),
				'name'          => 'show_subtitle',
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
				'instructions'  => __( 'Allow users to set custom ID and CSS classes for the headline.', 'nanato-addons' ),
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
				'show_subtitle'        => 1,
				'show_html_attributes' => 1,
			)
		);
		?>
		<div class="acf-headline" id="acf-<?php echo esc_attr( $field['key'] ); ?>" data-key="<?php echo esc_attr( $field['key'] ); ?>">
			<fieldset>
				<?php
				$this->render_content_section( $field );
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
		<div class="acf-headline-section">
			<div class="acf-headline-section-title"><?php echo esc_html__( 'Content', 'nanato-addons' ); ?></div>
			<div class="acf-headline-grid">
				<!-- Title -->
				<div class="acf-headline-subfield acf-headline-title acf-headline-full-width">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_title">
							<?php echo esc_html__( 'Title', 'nanato-addons' ); ?>
						</label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[title]" id="<?php echo esc_attr( $field['name'] ); ?>_title" value="<?php echo esc_attr( $field['value']['title'] ); ?>" placeholder="<?php echo esc_attr__( 'Enter headline title', 'nanato-addons' ); ?>" />
					</div>
				</div>

				<!-- Title Tag -->
				<div class="acf-headline-subfield acf-headline-title-tag">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_title_tag">
							<?php echo esc_html__( 'Title Tag', 'nanato-addons' ); ?>
						</label>
						<p class="description"><?php echo esc_html__( 'HTML tag for the title', 'nanato-addons' ); ?></p>
					</div>
					<div class="acf-input">
						<?php
						$title_tag_options = array(
							'h1'  => 'H1',
							'h2'  => 'H2',
							'h3'  => 'H3',
							'h4'  => 'H4',
							'h5'  => 'H5',
							'h6'  => 'H6',
							'p'   => 'P',
							'div' => 'DIV',
						);
						?>
						<select name="<?php echo esc_attr( $field['name'] ); ?>[title_tag]" id="<?php echo esc_attr( $field['name'] ); ?>_title_tag">
							<?php foreach ( $title_tag_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $field['value']['title_tag'], $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<?php if ( $field['show_subtitle'] ) : ?>
					<!-- Subtitle -->
					<div class="acf-headline-subfield acf-headline-subtitle acf-headline-full-width">
						<div class="acf-label">
							<label for="<?php echo esc_attr( $field['name'] ); ?>_subtitle">
								<?php echo esc_html__( 'Subtitle', 'nanato-addons' ); ?>
							</label>
						</div>
						<div class="acf-input">
							<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[subtitle]" id="<?php echo esc_attr( $field['name'] ); ?>_subtitle" value="<?php echo esc_attr( $field['value']['subtitle'] ); ?>" placeholder="<?php echo esc_attr__( 'Enter subtitle (optional)', 'nanato-addons' ); ?>" />
						</div>
					</div>
				<?php endif; ?>
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
		<div class="acf-headline-section">
			<div class="acf-headline-section-title"><?php echo esc_html__( 'HTML Attributes', 'nanato-addons' ); ?></div>
			<div class="acf-headline-grid">
				<!-- HTML ID -->
				<div class="acf-headline-subfield acf-headline-id">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_html_id"><?php echo esc_html__( 'ID', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[html_id]" id="<?php echo esc_attr( $field['name'] ); ?>_html_id" value="<?php echo esc_attr( $field['value']['html_id'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g., my-headline', 'nanato-addons' ); ?>" />
					</div>
				</div>

				<!-- CSS Classes -->
				<div class="acf-headline-subfield acf-headline-classes">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_css_classes"><?php echo esc_html__( 'CSS Classes', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[css_classes]" id="<?php echo esc_attr( $field['name'] ); ?>_css_classes" value="<?php echo esc_attr( $field['value']['css_classes'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g., headline custom-class highlight', 'nanato-addons' ); ?>" />
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
			'nanato-addons-headline',
			"{$url}assets/js/field.js",
			array( 'acf-input' ),
			$version
		);

		wp_enqueue_style(
			'nanato-addons-headline',
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
				'show_subtitle' => 1,
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
	 * Get field settings with defaults for use with get_headline_data.
	 *
	 * @param array $field The ACF field array.
	 * @return array Field settings with defaults.
	 * @since 1.0.0
	 */
	public static function get_field_settings( $field ) {
		if ( empty( $field ) || ! is_array( $field ) ) {
			return array(
				'show_subtitle'        => 1,
				'show_html_attributes' => 1,
			);
		}

		return array(
			'show_subtitle'        => ! empty( $field['show_subtitle'] ) ? 1 : 0,
			'show_html_attributes' => ! empty( $field['show_html_attributes'] ) ? 1 : 0,
		);
	}

	/**
	 * Get processed headline data.
	 *
	 * This is a helper method for theme developers to get processed headline data.
	 *
	 * @param array $value The field value.
	 * @param array $field_settings Optional. Field settings to respect visibility options.
	 * @return array|null Processed headline data or null if empty.
	 * @since 1.0.0
	 */
	public static function get_headline_data( $value, $field_settings = array() ) {
		if ( empty( $value ) || ! is_array( $value ) ) {
			return null;
		}

		// Set default field settings
		$field_settings = wp_parse_args(
			$field_settings,
			array(
				'show_subtitle'        => 1,
				'show_html_attributes' => 1,
			)
		);

		// Process the data
		$data = array(
			'title'     => ! empty( $value['title'] ) ? $value['title'] : '',
			'title_tag' => ! empty( $value['title_tag'] ) ? $value['title_tag'] : 'h2',
		);

		// Add subtitle only if enabled
		if ( $field_settings['show_subtitle'] ) {
			$data['subtitle'] = ! empty( $value['subtitle'] ) ? $value['subtitle'] : '';
		}

		// Add HTML attributes only if enabled
		if ( $field_settings['show_html_attributes'] ) {
			$data['attributes'] = array(
				'id'      => ! empty( $value['html_id'] ) ? $value['html_id'] : '',
				'classes' => ! empty( $value['css_classes'] ) ? $value['css_classes'] : '',
			);
		} else {
			$data['attributes'] = array(
				'id'      => '',
				'classes' => '',
			);
		}

		// Build CSS classes array
		$css_classes = array( 'nanato-headline' );

		if ( ! empty( $data['attributes']['classes'] ) ) {
			$css_classes[] = $data['attributes']['classes'];
		}

		$data['css_classes'] = $css_classes;

		return $data;
	}
}
