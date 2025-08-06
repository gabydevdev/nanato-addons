<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://nanatomedia.com
 * @since      1.0.0
 *
 * @package    Nanato_Addons
 * @subpackage Nanato_Addons/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Nanato_Addons
 * @subpackage Nanato_Addons/public
 * @author     Nanato Media <gabrielac@nanatomedia.com>
 */
class Nanato_Addons_Public {

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->noindex_options = get_option( 'nanato_addons_noindex_options', array() );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nanato-addons-public.css', array(), $this->version, 'all' );
		
		// Enqueue SVG inline styles if inline SVG is enabled
		$svg_options = get_option( 'nanato_addons_svg_options', array() );
		if ( ! empty( $svg_options['enable_inline_svg'] ) ) {
			wp_enqueue_style( 
				$this->plugin_name . '-svg-inline', 
				plugin_dir_url( __DIR__ ) . 'assets/css/svg-inline.css', 
				array(), 
				$this->version, 
				'all' 
			);
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nanato-addons-public.js', array( 'jquery' ), $this->version, false );
		
		// Enqueue SVG inline script if inline SVG is enabled
		$svg_options = get_option( 'nanato_addons_svg_options', array() );
		if ( ! empty( $svg_options['enable_inline_svg'] ) ) {
			wp_enqueue_script( 
				$this->plugin_name . '-svg-inline', 
				plugin_dir_url( __DIR__ ) . 'assets/js/svg-inline.js', 
				array(), 
				$this->version, 
				true 
			);
			
			// Localize script with settings
			$target_class = isset( $svg_options['svg_target_class'] ) ? $svg_options['svg_target_class'] : 'style-svg';
			$force_inline = ! empty( $svg_options['force_inline_svg'] );
			
			wp_localize_script(
				$this->plugin_name . '-svg-inline',
				'nanatoaddonsSvg',
				array(
					'targetClass' => esc_attr( $target_class ),
					'forceInline' => $force_inline,
					'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'nanato_svg_nonce' )
				)
			);
		}
	}

	/**
	 * Adds a noindex meta tag to archive pages based on the plugin options.
	 *
	 * @since    1.0.0
	 */
	public function add_noindex_meta() {
		// Skip if no options are set
		if ( empty( $this->noindex_options ) ) {
			return;
		}

		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

		// Check if we should add noindex based on current page type and settings
		$should_noindex = false;

		if ( is_category() && ! empty( $this->noindex_options['category'] ) ) {
			$should_noindex = true;
		} elseif ( is_tag() && ! empty( $this->noindex_options['tag'] ) ) {
			$should_noindex = true;
		} elseif ( is_author() && ! empty( $this->noindex_options['author'] ) ) {
			$should_noindex = true;
		} elseif ( is_date() && ! empty( $this->noindex_options['date'] ) ) {
			$should_noindex = true;
		}

		// If we should noindex and either it's not paginated-only mode or we're on page 2+
		if ( $should_noindex ) {
			if ( empty( $this->noindex_options['paginated_only'] ) || $paged > 1 ) {
				echo '<meta name="robots" content="noindex, follow">' . "\n";
			}
		}
	}
}
