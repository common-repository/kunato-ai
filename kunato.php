<?php
/**
 * @link              https://kunato.ai/
 * @since             1.0.0
 * @package           Kunato
 *
 * @wordpress-plugin
 * Plugin Name:       Kunato.ai
 * Description:       Kunato is a Quantitative deep learning system that predicts, assigns and updates the value of the content on your website in real time. Once activated on your website, users will be able to see realtime prices of the articles.
 * Version:           1.0.8
 * Author:            Kunato
 * Author URI:        https://kunato.ai/
 * License:           Proprietary
 * License URI:       https://plugins.svn.wordpress.org/kunato-ai/trunk/LICENSE.txt
 * Text Domain:       kunato.ai
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'KUNATO_VERSION', '1.0.0' );

/**
 * Plugin Text Domain
 */
define( 'KUNATO_TEXT_DOMAIN', 'kunatoplg' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kunato-activator.php
 */
function activate_kunato() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kunato-activator.php';
	Kunato_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kunato-deactivator.php
 */
function deactivate_kunato() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kunato-deactivator.php';
	Kunato_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kunato' );
register_deactivation_hook( __FILE__, 'deactivate_kunato' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kunato.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kunato() {

	$plugin = new Kunato();
	$plugin->run();

}
run_kunato();
