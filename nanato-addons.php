<?php
/**
 * Plugin Name: Nanato Addons
 * Plugin URI: https://yourdomain.com
 * Description: Custom made addons for Nanato Media Themes.
 * Author: Nanato Media
 * Author URI: https://github.com/nanatomedia
 * Version: 1.0.4
 * Text Domain: nanato-addons
 *
 * @package Nanato_Addons
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'NANATO_ADDONS_VERSION', '1.0.0' );
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
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require NANATO_ADDONS_DIR . 'includes/class-nanato-addons.php';

/**
 * Registers the ACF field type.
 */
function nanato_addons_include_acf_fields() {
	if ( ! function_exists( 'acf_register_field_type' ) ) {
		return;
	}

	require_once NANATO_ADDONS_DIR . '/acf-info-button/class-nanato-addons-acf-field-info-button.php';
	acf_register_field_type( 'nanato_addons_acf_field_info_button' );

	require_once NANATO_ADDONS_DIR . '/acf-button/class-nanato-addons-acf-field-button.php';
	acf_register_field_type( 'nanato_addons_acf_field_button' );

	require_once NANATO_ADDONS_DIR . '/acf-info-box/class-nanato-addons-acf-field-info-box.php';
	acf_register_field_type( 'nanato_addons_acf_field_info_box' );

	require_once NANATO_ADDONS_DIR . '/acf-headline/class-nanato-addons-acf-field-headline.php';
	acf_register_field_type( 'nanato_addons_acf_field_headline' );
}
add_action( 'init', 'nanato_addons_include_acf_fields' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_nanato_addons() {
	$plugin = new Nanato_Addons();
	$plugin->run();
}
run_nanato_addons();
