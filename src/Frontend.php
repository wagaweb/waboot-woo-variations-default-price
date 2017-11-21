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

	/**
	 * Here We have copy-pasted the get_price_html() from the default WC_Product
	 *
	 * @hooked 'woocommerce_variable_empty_price_html'
	 *
	 * @param $price
	 * @param \WC_Product_Variable $product
	 *
	 * @return string
	 */
	public function alter_variable_empty_price_html_output($price,$product){
		if ( '' === $product->get_price() ) {
			$price = apply_filters( 'woocommerce_empty_price_html', '', $product );
		} elseif ( $product->is_on_sale() ) {
			$price = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), wc_get_price_to_display( $product ) ) . $product->get_price_suffix();
		} else {
			$price = wc_price( wc_get_price_to_display( $product ) ) . $product->get_price_suffix();
		}

		return apply_filters( 'woocommerce_get_price_html', $price, $this );
	}

	/**
	 * @param $price
	 * @param $product
	 *
	 * @return mixed
	 */
	public function get_variable_price($price,$product){
		if(!$product instanceof \WC_Product_Variable){
			return $price;
		}

		$price = get_post_meta($product->get_id(),'_price',true);

		if($price === ''){
			$regular_price = get_post_meta($product->get_id(),'_regular_price',true);
			$sale_price = get_post_meta($product->get_id(),'_sale_price',true);
			if($regular_price !== ""){
				if($sale_price !== "" && (int) $sale_price < (int) $regular_price){
					return $sale_price;
				}else{
					return $regular_price;
				}
			}
		}

		return get_post_meta($product->get_id(),'_price',true);
	}

	/**
	 * @param $price
	 * @param $product
	 *
	 * @return mixed
	 */
	public function get_variable_regular_price($price,$product){
		if(!$product instanceof \WC_Product_Variable){
			return $price;
		}
		return get_post_meta($product->get_id(),'_regular_price',true);
	}

	/**
	 * @param $price
	 * @param $product
	 *
	 * @return mixed
	 */
	public function get_variable_sale_price($price,$product){
		if(!$product instanceof \WC_Product_Variable){
			return $price;
		}
		return get_post_meta($product->get_id(),'_sale_price',true);
	}
}