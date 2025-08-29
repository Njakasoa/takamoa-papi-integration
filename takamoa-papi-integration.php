<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
	/**
	* The plugin bootstrap file
	*
	* This file is read by WordPress to generate the plugin information in the plugin
	* admin area. This file also includes all of the dependencies used by the plugin,
	* registers the activation and deactivation functions, and defines a function
	* that starts the plugin.
	*
	* @link              https://nexa.takamoa.com/
	* @since             0.0.1
	* @package           Takamoa
	*
	* @wordpress-plugin
	* Plugin Name:       Takamoa Papi Integration
	* Plugin URI:        https://nexa.takamoa.com/nos-realisations/
	* Description:       Easily generate and track payment links via Papi.mg directly from your WordPress site. Supports MVOLA, Orange Money, Airtel Money, and BRED. Includes real-time status tracking and customizable forms.
	* Version:           0.0.8
	* Author:            Nexa by Takamoa
	* Author URI:        https://nexa.takamoa.com/
	* License:           GPL-2.0+
	* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	* Text Domain:       Takamoa_Papi_Integration
	* Domain Path:       /languages
	*/
	// If this file is called directly, abort.
if (! defined('WPINC')) {
	die;
}
	/**
	* Currently plugin version.
	* Start at version 0.0.1 and use SemVer - https://semver.org
	* Rename this for your plugin and update it as you release new versions.
	*/
define('TAKAMOA_PAPI_INTEGRATION_VERSION', '0.0.8');
	/**
	* The code that runs during plugin activation.
	* This action is documented in includes/class-takamoa-papi-integration-activator.php
	*/
function activate_takamoa_papi_integration()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-takamoa-papi-integration-activator.php';
	Takamoa_Papi_Integration_Activator::activate();
}
	/**
	* The code that runs during plugin deactivation.
	* This action is documented in includes/class-takamoa-papi-integration-deactivator.php
	*/
function deactivate_takamoa_papi_integration()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-takamoa-papi-integration-deactivator.php';
	Takamoa_Papi_Integration_Deactivator::deactivate();
}
register_activation_hook(__FILE__, 'activate_takamoa_papi_integration');
register_deactivation_hook(__FILE__, 'deactivate_takamoa_papi_integration');
	/**
	* The core plugin class that is used to define internationalization,
	* admin-specific hooks, and public-facing site hooks.
	*/
require plugin_dir_path(__FILE__) . 'includes/class-takamoa-papi-integration.php';
	/**
	* Begins execution of the plugin.
	*
	* Since everything within the plugin is registered via hooks,
	* then kicking off the plugin from this point in the file does
	* not affect the page life cycle.
	*
	* @since    0.0.1
	*/

$plugin = new Takamoa_Papi_Integration();
$plugin->run();
