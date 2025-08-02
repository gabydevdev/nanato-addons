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
}
