<?php
/**
 * Plugin Name: Nanato Addons
 * Plugin URI:
 * Description: Custom made addons for Nanato Media Themes including SEO optimization features, SVG support, and page ordering functionality.
 * Author: Nanato Media
 * Author URI:
 * Version: 1.0.9
 * Text Domain: nanato-addons
 *
 * @package Nanato_Addons
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'NANATO_ADDONS_VERSION', '1.0.9' );
define( 'NANATO_ADDONS_DIR', plugin_dir_path( __FILE__ ) );
define( 'NANATO_ADDONS_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nanato-addons-activator.php
 */
function activate_nanato_addons() {
	require_once NANATO_ADDONS_DIR . 'includes/class-nanato-addons-activator.php';
	Nanato_Addons_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nanato-addons-deactivator.php
 */
function deactivate_nanato_addons() {
	require_once NANATO_ADDONS_DIR . 'includes/class-nanato-addons-deactivator.php';
	Nanato_Addons_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_nanato_addons' );
register_deactivation_hook( __FILE__, 'deactivate_nanato_addons' );

/**
 * The core plugin class.
 */
require NANATO_ADDONS_DIR . 'includes/class-nanato-addons.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_nanato_addons() {
	$plugin = new Nanato_Addons();
	$plugin->run();
}
run_nanato_addons();
