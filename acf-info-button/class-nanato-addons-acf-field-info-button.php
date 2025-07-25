<?php
/**
 * ACF Info Button Field Type
 *
 * A comprehensive button configuration field for Advanced Custom Fields (ACF)
 * that provides a modern, responsive admin interface with extensive customization options.
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
 * ACF Info Button Field Class
 *
 * Extends the ACF field base class to provide a comprehensive button configuration interface
 * with support for content, styling, links, and HTML attributes.
 *
 * @since 1.0.0
 */
class nanato_addons_acf_field_info_button extends \acf_field {
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
		$this->name = 'info_button';

		/**
		 * Field type label.
		 *
		 * For public-facing UI. May contain spaces.
		 */
		$this->label = __( 'Info Button', 'nanato-addons' );

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'content'; // basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME

		// Field type description
		$this->description = __( 'Create comprehensive buttons with content, styling, and link configuration.', 'nanato-addons' );

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
			'text'             => '',
			'subtext'          => '',
			'icon'             => '',
			'layout'           => 'filled',
			'type'             => 'standard',
			'style'            => 'primary',
			'background_image' => '',
			'link_type'        => 'internal',
			'internal_link'    => '',
			'url'              => '',
			'target'           => '',
			'html_id'          => '',
			'css_classes'      => '',
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
			'url'     => NANATO_ADDONS_URL . 'acf-info-button',
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
				'show_html_attributes' => 1,
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
				'show_html_attributes' => 1,
			)
		);
		?>
		<div class="acf-info-button" id="acf-<?php echo esc_attr( $field['key'] ); ?>" data-key="<?php echo esc_attr( $field['key'] ); ?>">
			<fieldset>
				<?php
				$this->render_content_section( $field );
				$this->render_layout_style_section( $field );
				$this->render_link_configuration_section( $field );

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
		<div class="acf-info-button-section">
			<div class="acf-info-button-grid">
				<!-- Button Text -->
				<div class="acf-info-button-subfield acf-info-button-text">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_text"><?php echo esc_html__( 'Button Text', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[text]" id="<?php echo esc_attr( $field['name'] ); ?>_text" value="<?php echo esc_attr( $field['value']['text'] ); ?>" placeholder="<?php echo esc_attr__( 'Enter button text', 'nanato-addons' ); ?>" />
					</div>
				</div>

				<!-- Button Subtext -->
				<div class="acf-info-button-subfield acf-info-button-subtext">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_subtext"><?php echo esc_html__( 'Button Subtext', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[subtext]" id="<?php echo esc_attr( $field['name'] ); ?>_subtext" value="<?php echo esc_attr( $field['value']['subtext'] ); ?>" placeholder="<?php echo esc_attr__( 'Optional subtext', 'nanato-addons' ); ?>" />
					</div>
				</div>

				<!-- Icon -->
				<div class="acf-info-button-subfield acf-info-button-icon acf-info-button-full-width">
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
		<div class="acf-info-button-section">
			<div class="acf-info-button-section-title"><?php echo esc_html__( 'Layout & Style', 'nanato-addons' ); ?></div>
			<div class="acf-info-button-grid">
				<!-- Layout -->
				<div class="acf-info-button-subfield acf-info-button-layout">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_layout"><?php echo esc_html__( 'Layout', 'nanato-addons' ); ?></label>
						<p class="description"><?php echo esc_html__( 'Button appearance style', 'nanato-addons' ); ?></p>
					</div>
					<div class="acf-input">
						<?php
						$layout_options = apply_filters(
							'nanato_addons_acf_field_info_button_layout_options',
							array(
								'filled'   => __( 'Filled', 'nanato-addons' ),
								'outlined' => __( 'Outlined', 'nanato-addons' ),
							)
						);
						?>
						<select name="<?php echo esc_attr( $field['name'] ); ?>[layout]" id="<?php echo esc_attr( $field['name'] ); ?>_layout">
							<?php foreach ( $layout_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $field['value']['layout'], $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<!-- Type -->
				<div class="acf-info-button-subfield acf-info-button-type">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_type"><?php echo esc_html__( 'Type', 'nanato-addons' ); ?></label>
						<p class="description"><?php echo esc_html__( 'Button layout type', 'nanato-addons' ); ?></p>
					</div>
					<div class="acf-input">
						<?php
						$type_options = apply_filters(
							'nanato_addons_acf_field_info_button_type_options',
							array(
								'standard'   => __( 'Standard', 'nanato-addons' ),
								'icon_boxed' => __( 'Icon Boxed', 'nanato-addons' ),
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
				<div class="acf-info-button-subfield acf-info-button-style">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_style"><?php echo esc_html__( 'Style', 'nanato-addons' ); ?></label>
						<p class="description"><?php echo esc_html__( 'Color scheme variation', 'nanato-addons' ); ?></p>
					</div>
					<div class="acf-input">
						<?php
						$style_options = apply_filters(
							'nanato_addons_acf_field_info_button_style_options',
							array(
								'primary'   => __( 'Primary', 'nanato-addons' ),
								'secondary' => __( 'Secondary', 'nanato-addons' ),
								'light'     => __( 'Light', 'nanato-addons' ),
								'dark'      => __( 'Dark', 'nanato-addons' ),
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

				<!-- Background Image -->
				<div class="acf-info-button-subfield acf-info-button-background-image acf-info-button-full-width">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_background_image"><?php echo esc_html__( 'Background Image', 'nanato-addons' ); ?></label>
						<p class="description"><?php echo esc_html__( 'Optional background image for the button', 'nanato-addons' ); ?></p>
					</div>
					<div class="acf-input">
						<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>[background_image]" id="<?php echo esc_attr( $field['name'] ); ?>_background_image" value="<?php echo esc_attr( $field['value']['background_image'] ); ?>" />
						<div class="image-controls">
							<button type="button" class="button select-image"><?php echo esc_html__( 'Select Image', 'nanato-addons' ); ?></button>
							<button type="button" class="button remove-image" style="<?php echo empty( $field['value']['background_image'] ) ? 'display:none;' : ''; ?>"><?php echo esc_html__( 'Remove', 'nanato-addons' ); ?></button>
						</div>
						<div class="image-preview">
							<?php if ( ! empty( $field['value']['background_image'] ) ) : ?>
								<img src="<?php echo esc_url( wp_get_attachment_image_url( $field['value']['background_image'], 'thumbnail' ) ); ?>" alt="" />
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Link Configuration section.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 * @since 1.0.0
	 */
	private function render_link_configuration_section( $field ) {
		?>
		<!-- Link Configuration Section -->
		<div class="acf-info-button-section">
			<div class="acf-info-button-section-title"><?php echo esc_html__( 'Link Configuration', 'nanato-addons' ); ?></div>
			<div class="acf-info-button-grid">
				<!-- Link Type -->
				<div class="acf-info-button-subfield acf-info-button-link-type">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_link_type"><?php echo esc_html__( 'Link Type', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<select name="<?php echo esc_attr( $field['name'] ); ?>[link_type]" id="<?php echo esc_attr( $field['name'] ); ?>_link_type" class="link-type-select">
							<option value="internal" <?php selected( $field['value']['link_type'], 'internal' ); ?>><?php echo esc_html__( 'Internal Link', 'nanato-addons' ); ?></option>
							<option value="external" <?php selected( $field['value']['link_type'], 'external' ); ?>><?php echo esc_html__( 'External Link', 'nanato-addons' ); ?></option>
						</select>
					</div>
				</div>

				<!-- Link Content (Internal/External) -->
				<div class="acf-info-button-subfield acf-info-button-link-content">
					<!-- Internal Link -->
					<div class="acf-info-button-internal-link" style="<?php echo $field['value']['link_type'] !== 'internal' ? 'display:none;' : ''; ?>">
						<div class="acf-label">
							<label for="<?php echo esc_attr( $field['name'] ); ?>_internal_link"><?php echo esc_html__( 'Select Content', 'nanato-addons' ); ?></label>
						</div>
						<div class="acf-input">
							<?php echo $this->get_internal_link_dropdown( $field ); ?>
						</div>
					</div>

					<!-- External Link -->
					<div class="acf-info-button-external-link" style="<?php echo $field['value']['link_type'] !== 'external' ? 'display:none;' : ''; ?>">
						<div class="acf-label">
							<label for="<?php echo esc_attr( $field['name'] ); ?>_url"><?php echo esc_html__( 'External URL', 'nanato-addons' ); ?></label>
						</div>
						<div class="acf-input">
							<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[url]" id="<?php echo esc_attr( $field['name'] ); ?>_url" value="<?php echo esc_attr( $field['value']['url'] ); ?>" placeholder="https://example.com" />
						</div>
					</div>
				</div>

				<!-- Target -->
				<div class="acf-info-button-subfield acf-info-button-target">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_target"><?php echo esc_html__( 'Link Target', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<select name="<?php echo esc_attr( $field['name'] ); ?>[target]" id="<?php echo esc_attr( $field['name'] ); ?>_target">
							<option value="" <?php selected( $field['value']['target'], '' ); ?>><?php echo esc_html__( 'Same Window', 'nanato-addons' ); ?></option>
							<option value="_blank" <?php selected( $field['value']['target'], '_blank' ); ?>><?php echo esc_html__( 'New Window', 'nanato-addons' ); ?></option>
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
		<div class="acf-info-button-section">
			<div class="acf-info-button-section-title"><?php echo esc_html__( 'HTML Attributes', 'nanato-addons' ); ?></div>
			<div class="acf-info-button-grid">
				<!-- HTML ID -->
				<div class="acf-info-button-subfield acf-info-button-id">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_html_id"><?php echo esc_html__( 'ID', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[html_id]" id="<?php echo esc_attr( $field['name'] ); ?>_html_id" value="<?php echo esc_attr( $field['value']['html_id'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g., my-button', 'nanato-addons' ); ?>" />
					</div>
				</div>

				<!-- CSS Classes -->
				<div class="acf-info-button-subfield acf-info-button-classes">
					<div class="acf-label">
						<label for="<?php echo esc_attr( $field['name'] ); ?>_css_classes"><?php echo esc_html__( 'CSS Classes', 'nanato-addons' ); ?></label>
					</div>
					<div class="acf-input">
						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[css_classes]" id="<?php echo esc_attr( $field['name'] ); ?>_css_classes" value="<?php echo esc_attr( $field['value']['css_classes'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g., btn btn-primary custom-class', 'nanato-addons' ); ?>" />
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Generate internal link dropdown HTML.
	 *
	 * @param array $field The field settings and values.
	 * @return string HTML for the internal link dropdown.
	 * @since 1.0.0
	 */
	private function get_internal_link_dropdown( $field ) {
		$selected = $field['value']['internal_link'];
		$args     = array(
			'post_type'      => array( 'page', 'post' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'type title',
			'order'          => 'ASC',
		);
		$posts    = get_posts( $args );

		ob_start();
		?>
		<select name="<?php echo esc_attr( $field['name'] ); ?>[internal_link]" id="<?php echo esc_attr( $field['name'] ); ?>_internal_link">
			<option value=""><?php echo esc_html__( 'Select a page/post', 'nanato-addons' ); ?></option>
			<?php
			$post_type = '';
			foreach ( $posts as $post ) {
				$this_post_type = get_post_type( $post );
				if ( $post_type !== $this_post_type ) {
					if ( '' !== $post_type ) {
						echo '</optgroup>';
					}
					$post_type = $this_post_type;
					echo '<optgroup label="' . esc_attr( get_post_type_object( $post_type )->labels->name ) . '">';
				}
				?>
				<option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $selected, $post->ID ); ?>>
					<?php echo esc_html( $post->post_title ); ?>
				</option>
				<?php
			}
			if ( '' !== $post_type ) {
				echo '</optgroup>';
			}
			?>
		</select>
		<?php
		return ob_get_clean();
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
			'nanato-addons-info-button',
			"{$url}assets/js/field.js",
			array( 'acf-input' ),
			$version
		);

		wp_enqueue_style(
			'nanato-addons-info-button',
			"{$url}assets/css/field.css",
			array( 'acf-input' ),
			$version
		);
	}
}
