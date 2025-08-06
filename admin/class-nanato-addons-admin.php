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

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
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
}
