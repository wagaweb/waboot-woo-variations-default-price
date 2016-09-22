<?php

namespace WBWooVariationsDefaultPrice;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    WBWooVariationsDefaultPrice
 * @subpackage WBWooVariationsDefaultPrice/public
 */
class Frontend {

	/**
	 * The main plugin class
	 * @var \WBWooVariationsDefaultPrice\Plugin
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param null|string $plugin_name @deprecated
	 * @param null|string $version @deprecated
	 * @param null $core The plugin main object
	 */
	public function __construct( $plugin_name = null, $version = null, $core = null ) {
		if(isset($core)) $this->plugin = $core;
	}
}