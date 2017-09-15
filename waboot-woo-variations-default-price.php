<?php

namespace WBWooVariationsDefaultPrice;

/**
 * The plugin bootstrap file
 *
 * @wordpress-plugin
 * Plugin Name:       WAGA Variations Default Prices for WooCommerce
 * Plugin URI:        http://www.waga.it/
 * Description:       Allows shop owner to set default prices for product variations.
 * Version:           1.1.0
 * Author:            WAGA
 * Author URI:        http://www.waga.it/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wb-woo-variations-default-price
 * Domain Path:       /languages
 *
 */

use WBWooVariationsDefaultPrice\Plugin;

if ( ! defined( 'WPINC' ) ) {
	die; //If this file is called directly, abort.
}

if(is_file(__DIR__.'/vendor/autoload.php')){
	require_once "vendor/autoload.php";
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

require_once 'src/includes/wbf-plugin-check-functions.php';
includes\include_wbf_autoloader();

if(class_exists("\\WBF\\components\\pluginsframework\\BasePlugin")) {
	require_once 'src/Plugin.php';
	$plugin = new Plugin();
	$plugin->run();
}else{
	if(is_admin()){
		add_action( 'admin_notices', function(){
			?>
			<div class="error">
				<p><?php _e( basename(__FILE__). ' requires Waboot Framework' ); ?></p>
			</div>
			<?php
		});
	}
}