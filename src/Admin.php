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

	/**
	 * Enqueue plugins assets
	 *
	 * @hooked 'admin_enqueue_scripts'
	 */
	public function assets(){
		$assets = [
			'vdp-admin-script' => [
				'path' => $this->plugin->is_debug() ? $this->plugin->get_dir()."/assets/dist/js/bundle.js" : $this->plugin->get_dir()."assets/dist/js/".$this->plugin->get_plugin_name().".min.js",
				'uri' =>  $this->plugin->is_debug() ? $this->plugin->get_uri()."/assets/dist/js/bundle.js" : $this->plugin->get_uri()."assets/dist/js/".$this->plugin->get_plugin_name().".min.js",
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
	 *
	 * @param int $post_id
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
	 * WooCommerce bypass the saving of prices for variable and grouped products. We want to save them.
	 * For this behavior looks at: class-wc-admin-post-types.php @ quick_edit_save()
	 *
	 * @hooked 'woocommerce_product_quick_edit_save'
	 *
	 * @param $product
	 */
	public function save_prices_for_variable_products_during_quick_edit($product){
		if ( $product->is_type('variable') ) {
			$post_id = $product->id;

			if ( isset( $_REQUEST['_regular_price'] ) ) {
				$new_regular_price = $_REQUEST['_regular_price'] === '' ? '' : wc_format_decimal( $_REQUEST['_regular_price'] );
				update_post_meta( $post_id, '_regular_price', $new_regular_price );
			} else {
				$new_regular_price = null;
			}
			if ( isset( $_REQUEST['_sale_price'] ) ) {
				$new_sale_price = $_REQUEST['_sale_price'] === '' ? '' : wc_format_decimal( $_REQUEST['_sale_price'] );
				update_post_meta( $post_id, '_sale_price', $new_sale_price );
			} else {
				$new_sale_price = null;
			}

			// Handle price - remove dates and set to lowest
			$price_changed = false;

			if ( ! is_null( $new_regular_price ) && $new_regular_price != $old_regular_price ) {
				$price_changed = true;
			} elseif ( ! is_null( $new_sale_price ) && $new_sale_price != $old_sale_price ) {
				$price_changed = true;
			}

			if ( $price_changed ) {
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
				update_post_meta( $post_id, '_sale_price_dates_to', '' );

				if ( ! is_null( $new_sale_price ) && $new_sale_price !== '' ) {
					update_post_meta( $post_id, '_price', $new_sale_price );
				} else {
					update_post_meta( $post_id, '_price', $new_regular_price );
				}
			}
		}
	}

	/**
	 * Adds product variable to 'save-price-enabled' types for bulk edit. For some strange reason WooCommerce applies this more logical approach in bulk edit only ¯\_(ツ)_/¯
	 *
	 * @param array $types
	 *
	 * @hooked 'woocommerce_bulk_edit_save_price_product_types'
	 *
	 * @return array
	 */
	public function enable_variable_product_to_save_price_in_bulk_edit($types){
		$types[] = "variable";
		return $types;
	}

	/**
	 * For further usage
	 *
	 * @hooked 'woocommerce_variation_options_pricing'
	 *
	 * @param $loop
	 * @param $variation_data
	 * @param $variation
	 */
	public function inject_js_after_variation_prices($loop, $variation_data, $variation){}
}