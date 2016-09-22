<?php

namespace WBWooVariationsDefaultPrice;

/**
 * The plugin bootstrap file
 *
 * @link              http://www.waboot.com
 * @package           WBWooVariationsDefaultPrice
 *
 * @wordpress-plugin
 * Plugin Name:       WAGA Variations Default Price for WooCommerce
 * Plugin URI:        http://www.waga.it/
 * Description:       Allows users to set a default price for all product variations
 * Version:           0.1.0
 * Author:            WAGA
 * Author URI:        http://www.waga.it/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wb-woo-variations-default-price
 * Domain Path:       /languages
 *
 */

use WBWooVariationsDefaultPrice\includes\Activator;
use WBWooVariationsDefaultPrice\includes\Deactivator;
use WBWooVariationsDefaultPrice\Plugin;

if ( ! defined( 'WPINC' ) ) {
	die; //If this file is called directly, abort.
}

require_once plugin_dir_path( __FILE__ ) . 'src/includes/utils.php';
try{
	$wbf_autoloader = includes\get_autoloader();
	require_once $wbf_autoloader;
}catch(\Exception $e){
	includes\maybe_disable_plugin("wb-woo-variations-default-price/wb-woo-variations-default-price.php"); // /!\ /!\ /!\ HEY, LOOK! EDIT THIS ALSO!! /!\ /!\ /!\
}

/********************************************************/
/****************** PLUGIN BEGIN ************************
/********************************************************/

// Custom plugin autoloader function
spl_autoload_register( function($class){
	$prefix = "WBWooVariationsDefaultPrice\\";
	$plugin_path = plugin_dir_path( __FILE__ );
	$base_dir = $plugin_path."src/";
	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}
	// get the relative class name
	$relative_class = substr($class, $len);
	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	// if the file exists, require it
	if (file_exists($file)) {
		require_once $file;
	}else{
		return;
	}
});

register_activation_hook( __FILE__, function(){ Activator::activate(); } );
register_deactivation_hook( __FILE__, function(){ Deactivator::deactivate(); } );

if(!\WBWooVariationsDefaultPrice\includes\pluginsframework_is_present()) return; // Starts the plugin only if WBF Plugin Framework is present

require_once 'src/Plugin.php';
/*
 * Begins execution of the plugin.
 */
$plugin = new Plugin();
$plugin->run();