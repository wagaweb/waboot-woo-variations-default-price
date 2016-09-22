<?php
namespace WBWooVariationsDefaultPrice;
use WBF\components\assets\AssetsManager;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    WBWooVariationsDefaultPrice
 * @subpackage WBWooVariationsDefaultPrice/admin
 */
class Admin {

	/**
	 * The main plugin class
	 *
	 * @var \WBWooVariationsDefaultPrice\Plugin
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name = null, $version = null, $core = null ) {
		if(isset($core)) $this->plugin = $core;
	}

	public function assets(){
		$assets = [
			'vdp-admin-script' => [
				'path' => $this->plugin->get_dir()."/assets/dist/js/bundle.js",
				'uri' => $this->plugin->get_uri()."/assets/dist/js/bundle.js",
				'type' => 'js',
				'deps' => ['jquery','backbone','underscore'],
				'i10n' => [
					'name' => "VDPData",
					'params' => [
						'isAdmin' => is_admin()
					]
				]
			]
		];

		$am = new AssetsManager($assets);
		$am->enqueue();
	}

	/**
	 * For further usage
	 *
	 * @param $loop
	 * @param $variation_data
	 * @param $variation
	 */
	public function inject_js_after_variation_prices($loop, $variation_data, $variation){}
}