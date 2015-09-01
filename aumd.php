<?php

/**
  * Plugin Name:       Ajaxify Ultimate Member Directory
 * Plugin URI:        http://www.champ.ninja/
 * Description:       Enables ultimate member directory to search and load profiles with ajax
 * Version:           1.0.0
 * Author:            Champ Camba
 * Author URI:        http://www.champ.ninja/
 * Text Domain:       aumd
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aumd-activator.php
 */
function activate_aumd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aumd-activator.php';
	Aumd_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aumd-deactivator.php
 */
function deactivate_aumd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aumd-deactivator.php';
	Aumd_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aumd' );
register_deactivation_hook( __FILE__, 'deactivate_aumd' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aumd.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_aumd() {

	$plugin = new Aumd();
	$plugin->run();

}
run_aumd();
