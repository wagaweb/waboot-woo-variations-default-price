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
	 * WooCommerce bypass the saving of prices for variable and grouped products. We want to save them.
	 * For this behavior looks at: class-wc-meta-box-product-data.php @ save()
	 *
	 * @hooked 'woocommerce_process_product_meta_[variable]'
	 */
	public function save_prices_for_variable_products($post_id){
		//Following lines are cut-past from WooCommerce source:
		$date_from     = (string) isset( $_POST['_sale_price_dates_from'] ) ? wc_clean( $_POST['_sale_price_dates_from'] ) : '';
		$date_to       = (string) isset( $_POST['_sale_price_dates_to'] ) ? wc_clean( $_POST['_sale_price_dates_to'] )     : '';
		$regular_price = (string) isset( $_POST['_regular_price'] ) ? wc_clean( $_POST['_regular_price'] )                 : '';
		$sale_price    = (string) isset( $_POST['_sale_price'] ) ? wc_clean( $_POST['_sale_price'] )                       : '';

		update_post_meta( $post_id, '_regular_price', '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
		update_post_meta( $post_id, '_sale_price', '' === $sale_price ? '' : wc_format_decimal( $sale_price ) );

		// Dates
		update_post_meta( $post_id, '_sale_price_dates_from', $date_from ? strtotime( $date_from ) : '' );
		update_post_meta( $post_id, '_sale_price_dates_to', $date_to ? strtotime( $date_to ) : '' );

		if ( $date_to && ! $date_from ) {
			$date_from = date( 'Y-m-d' );
			update_post_meta( $post_id, '_sale_price_dates_from', strtotime( $date_from ) );
		}

		// Update price if on sale
		if ( '' !== $sale_price && '' === $date_to && '' === $date_from ) {
			update_post_meta( $post_id, '_price', wc_format_decimal( $sale_price ) );
		} elseif ( '' !== $sale_price && $date_from && strtotime( $date_from ) <= strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
			update_post_meta( $post_id, '_price', wc_format_decimal( $sale_price ) );
		} else {
			update_post_meta( $post_id, '_price', '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
		}

		if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
			update_post_meta( $post_id, '_price', '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
			update_post_meta( $post_id, '_sale_price', '' );
			update_post_meta( $post_id, '_sale_price_dates_from', '' );
			update_post_meta( $post_id, '_sale_price_dates_to', '' );
		}
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