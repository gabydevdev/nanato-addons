<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://nanatomedia.com
 * @since      1.0.0
 *
 * @package    Nanato_Addons
 * @subpackage Nanato_Addons/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Nanato_Addons
 * @subpackage Nanato_Addons/admin
 * @author     Nanato Media <gabrielac@nanatomedia.com>
 */
class Nanato_Addons_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Holds the plugin options.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $noindex_options    The noindex options.
	 */
	private $noindex_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name     = $plugin_name;
		$this->version         = $version;
		$this->noindex_options = get_option( 'nanato_addons_noindex_options', array() );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nanato_Addons_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nanato_Addons_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nanato-addons-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nanato_Addons_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nanato_Addons_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nanato-addons-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Adds the options page to the admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Nanato Addons Settings', 'nanato-addons' ),
			__( 'Nanato Addons', 'nanato-addons' ),
			'manage_options',
			'nanato-addons',
			array( $this, 'options_page' )
		);
	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting( 'nanato_addons_noindex', 'nanato_addons_noindex_options' );
	}

	/**
	 * Renders the options page.
	 *
	 * @since    1.0.0
	 */
	public function options_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/nanato-addons-admin-display.php';
	}

	/**
	 * Get the noindex options.
	 *
	 * @since    1.0.0
	 * @return   array    The noindex options.
	 */
	public function get_noindex_options() {
		return $this->noindex_options;
	}

	/**
	 * Enable SVG uploads by adding SVG to allowed mime types.
	 *
	 * @since    1.0.0
	 * @param    array $mime_types    Current array of mime types.
	 * @return   array                Modified array of mime types.
	 */
	public function enable_svg_uploads( $mime_types ) {
		$mime_types['svg']  = 'image/svg+xml';
		$mime_types['svgz'] = 'image/svg+xml';
		return $mime_types;
	}

	/**
	 * Fix SVG display in media library.
	 *
	 * @since    1.0.0
	 * @param    array  $response    Array of prepared attachment data.
	 * @param    object $attachment  Attachment object.
	 * @param    array  $meta        Array of attachment meta data.
	 * @return   array               Modified response.
	 */
	public function fix_svg_display( $response, $attachment, $meta ) {
		if ( $response['mime'] === 'image/svg+xml' ) {
			$response['image'] = array(
				'src'    => $response['url'],
				'width'  => 150,
				'height' => 150,
			);
		}
		return $response;
	}

	/**
	 * Add SVG support to WordPress media uploader.
	 * Sanitizes SVG files for security.
	 *
	 * @since    1.0.0
	 * @param    array $data     An array of slashed post data.
	 * @param    array $postarr  An array of sanitized, but otherwise unmodified post data.
	 * @return   array           Modified post data.
	 */
	public function sanitize_svg_upload( $data, $postarr ) {
		// Only process SVG files
		if ( ! isset( $_FILES['async-upload']['tmp_name'] ) ) {
			return $data;
		}

		$file_tmp_name = $_FILES['async-upload']['tmp_name'];
		$file_name     = $_FILES['async-upload']['name'];
		$file_type     = wp_check_filetype( $file_name );

		if ( $file_type['type'] === 'image/svg+xml' ) {
			// Basic SVG sanitization - remove script tags and on* attributes
			$svg_content = file_get_contents( $file_tmp_name );

			if ( $svg_content !== false ) {
				// Remove script tags
				$svg_content = preg_replace( '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $svg_content );

				// Remove on* event attributes
				$svg_content = preg_replace( '/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $svg_content );

				// Write cleaned content back
				file_put_contents( $file_tmp_name, $svg_content );
			}
		}

		return $data;
	}

	/**
	 * Add inline SVG settings section to admin page.
	 *
	 * @since    1.0.0
	 */
	public function register_svg_settings() {
		// Register SVG inline settings
		register_setting( 'nanato_addons_svg', 'nanato_addons_svg_options' );

		add_settings_section(
			'nanato_addons_svg_section',
			__( 'SVG Support Settings', 'nanato-addons' ),
			array( $this, 'svg_settings_section_callback' ),
			'nanato-addons-svg'
		);

		add_settings_field(
			'enable_inline_svg',
			__( 'Enable Inline SVG', 'nanato-addons' ),
			array( $this, 'enable_inline_svg_callback' ),
			'nanato-addons-svg',
			'nanato_addons_svg_section'
		);

		add_settings_field(
			'svg_target_class',
			__( 'CSS Target Class', 'nanato-addons' ),
			array( $this, 'svg_target_class_callback' ),
			'nanato-addons-svg',
			'nanato_addons_svg_section'
		);

		add_settings_field(
			'force_inline_svg',
			__( 'Force Inline SVG', 'nanato-addons' ),
			array( $this, 'force_inline_svg_callback' ),
			'nanato-addons-svg',
			'nanato_addons_svg_section'
		);

		add_settings_field(
			'auto_insert_class',
			__( 'Auto Insert Class', 'nanato-addons' ),
			array( $this, 'auto_insert_class_callback' ),
			'nanato-addons-svg',
			'nanato_addons_svg_section'
		);
	}

	/**
	 * SVG settings section callback.
	 *
	 * @since    1.0.0
	 */
	public function svg_settings_section_callback() {
		echo '<p>' . esc_html__( 'Configure SVG support and inline rendering options.', 'nanato-addons' ) . '</p>';
	}

	/**
	 * Enable inline SVG callback.
	 *
	 * @since    1.0.0
	 */
	public function enable_inline_svg_callback() {
		$options = get_option( 'nanato_addons_svg_options', array() );
		$enabled = isset( $options['enable_inline_svg'] ) ? $options['enable_inline_svg'] : '';
		?>
		<input type="checkbox" id="enable_inline_svg" name="nanato_addons_svg_options[enable_inline_svg]" value="1" <?php checked( $enabled, 1 ); ?> />
		<label for="enable_inline_svg">
			<?php esc_html_e( 'Enable inline SVG rendering for images with the target class', 'nanato-addons' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, img tags with the target class will be replaced with inline SVG code, allowing direct CSS styling of SVG elements.', 'nanato-addons' ); ?>
		</p>
		<?php
	}

	/**
	 * SVG target class callback.
	 *
	 * @since    1.0.0
	 */
	public function svg_target_class_callback() {
		$options = get_option( 'nanato_addons_svg_options', array() );
		$class   = isset( $options['svg_target_class'] ) ? $options['svg_target_class'] : 'style-svg';
		?>
		<input type="text" id="svg_target_class" name="nanato_addons_svg_options[svg_target_class]" value="<?php echo esc_attr( $class ); ?>" class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'CSS class to target for inline SVG replacement. Default: style-svg', 'nanato-addons' ); ?>
			<br>
			<?php esc_html_e( 'Example usage: <img class="style-svg" src="image.svg" alt="My SVG" />', 'nanato-addons' ); ?>
		</p>
		<?php
	}

	/**
	 * Force inline SVG callback.
	 *
	 * @since    1.0.0
	 */
	public function force_inline_svg_callback() {
		$options = get_option( 'nanato_addons_svg_options', array() );
		$enabled = isset( $options['force_inline_svg'] ) ? $options['force_inline_svg'] : '';
		?>
		<input type="checkbox" id="force_inline_svg" name="nanato_addons_svg_options[force_inline_svg]" value="1" <?php checked( $enabled, 1 ); ?> />
		<label for="force_inline_svg">
			<?php esc_html_e( 'Force all SVG images to render inline', 'nanato-addons' ); ?>
		</label>
		<p class="description">
			<strong><?php esc_html_e( 'Use with caution!', 'nanato-addons' ); ?></strong>
			<?php esc_html_e( 'This will automatically convert ALL SVG images to inline, regardless of CSS classes. Useful for page builders that don\'t allow custom CSS classes.', 'nanato-addons' ); ?>
		</p>
		<?php
	}

	/**
	 * Auto insert class callback.
	 *
	 * @since    1.0.0
	 */
	public function auto_insert_class_callback() {
		$options = get_option( 'nanato_addons_svg_options', array() );
		$enabled = isset( $options['auto_insert_class'] ) ? $options['auto_insert_class'] : '';
		?>
		<input type="checkbox" id="auto_insert_class" name="nanato_addons_svg_options[auto_insert_class]" value="1" <?php checked( $enabled, 1 ); ?> />
		<label for="auto_insert_class">
			<?php esc_html_e( 'Automatically add target class to SVG images', 'nanato-addons' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( '(Classic Editor Only) Automatically adds the target class when inserting SVG images. Removes default WordPress classes and only affects SVG files.', 'nanato-addons' ); ?>
		</p>
		<?php
	}

	/**
	 * Automatically insert SVG class when adding images via Classic Editor.
	 *
	 * @since    1.0.0
	 * @param    array $html    The attachment HTML.
	 * @param    int   $id      The attachment ID.
	 * @param    array $attachment The attachment array.
	 * @return   array           Modified attachment HTML.
	 */
	public function auto_insert_svg_class( $html, $id, $attachment ) {
		$options = get_option( 'nanato_addons_svg_options', array() );

		// Only proceed if auto insert is enabled
		if ( empty( $options['auto_insert_class'] ) ) {
			return $html;
		}

		// Check if this is an SVG file
		$mime_type = get_post_mime_type( $id );
		if ( $mime_type !== 'image/svg+xml' ) {
			return $html;
		}

		// Get the target class
		$target_class = isset( $options['svg_target_class'] ) ? $options['svg_target_class'] : 'style-svg';

		// Remove WordPress default classes and add our target class
		$html = preg_replace( '/class="[^"]*"/', 'class="' . esc_attr( $target_class ) . '"', $html );

		// If no class attribute exists, add it
		if ( strpos( $html, 'class=' ) === false ) {
			$html = str_replace( '<img ', '<img class="' . esc_attr( $target_class ) . '" ', $html );
		}

		return $html;
	}
}
