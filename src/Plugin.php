<?php

namespace WBWooVariationsDefaultPrice;
use WBF\components\pluginsframework\BasePlugin;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WBWooVariationsDefaultPrice
 */
class Plugin extends BasePlugin {
	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		parent::__construct( "wb-woo-variations-default-price", plugin_dir_path( dirname( __FILE__ ) ) );

		$this->define_public_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 */
	private function define_public_hooks() {
		$plugin_public = $this->loader->public_plugin;
	}

	/**
	 * Register all of the hooks related to the admin-facing functionality of the plugin.
	 */
	private function define_admin_hooks(){
		$plugin_admin = $this->loader->admin_plugin;

		$this->loader->add_action("admin_enqueue_scripts",$plugin_admin,"assets");

		$this->loader->add_action("woocommerce_variation_options_pricing",$plugin_admin,"inject_js_after_variation_prices", 10, 3);
	}

	/**
	 * Load the required dependencies for this plugin (called into parent::_construct())
	 *
	 * [IT] E' possibile utilizzare questa funzione per dei require di eventuali vendors
	 */
	protected function load_dependencies() {
		parent::load_dependencies();
	}
}
