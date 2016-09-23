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
		$this->define_general_hooks();
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

		$this->loader->add_action('woocommerce_process_product_meta_'.'variable', $plugin_admin, "save_prices_for_variable_products", 10, 1);

		$this->loader->add_action('woocommerce_product_quick_edit_save', $plugin_admin, "save_prices_for_variable_products_during_quick_edit", 10, 1);
	}

	private function define_general_hooks(){
		$this->loader->add_filter("get_"."post"."_metadata", $this, "get_parent_price_on_variation_price_get", 10, 4);
	}

	/**
	 * WooCommerce use __get() method to retrieve $variation->price. The method search price meta on variation.
	 * This method will override the value if
	 *
	 * @param $value
	 * @param $object_id
	 * @param $meta_key
	 * @param $single
	 *
	 * @return string|array
	 */
	public function get_parent_price_on_variation_price_get($value, $object_id, $meta_key, $single){
		if($meta_key == "_price" || $meta_key == "_sale_price" || $meta_key == "_regular_price"){
			if(get_post_type($object_id) == "product_variation"){
				remove_filter( current_filter(), [$this,'get_parent_price_on_variation_price_get'] ); //Avoid infinite loop
				//Try to get the price:
				$variation_price = get_post_meta($object_id,$meta_key,true);
				if($variation_price == ""){
					$parent_price = call_user_func(function() use($object_id,$meta_key){
						$v = wc_get_product($object_id);
						$value = get_post_meta($v->parent->id,$meta_key,true);
						return $value;
					});
					if($parent_price != ""){
						$value = [$parent_price];
					}
				}
				add_filter("get_"."post"."_metadata", [$this, "get_parent_price_on_variation_price_get"], 10, 4); //Re-add the filter
			}
		}
		return $value;
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
