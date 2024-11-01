<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_Cart_Handler' ) ) {
	
	/**
	 * WCAPP Cart Class.
	 */
	class WCAPP_Cart_Handler {
		
		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'display_paypal_button_product' ), 20 );
			add_action( 'woocommerce_proceed_to_checkout', array( $this, 'display_paypal_button_cart' ), 30 );
			add_action( 'wc_ajax_wc_wcapp_generate_cart', array( $this, 'wc_ajax_generate_cart' ) );
			add_action( 'wc_ajax_wc_wcapp_redirect_checkout', array( $this, 'wc_ajax_redirect_checkout' ) );
		}
		
		/**
		* return checkout url
		*/
		public function get_checkout_url() {
			//wc_get_checkout_url().'?payment=PayPal'
			return home_url().'?wc-ajax=wc_wcapp_redirect_checkout';
		}
		
		/**
		* redirect to checkout url
		*/
		public function wc_ajax_redirect_checkout() {
			//WC()->session->set( 'payment', 'PayPal' );
			wp_safe_redirect(wc_get_checkout_url().'?payment=PayPal');
            exit;
		}
		
		/**
		 * Display paypal button on the product page.
		 */
		public function display_paypal_button_product() {
            $settings_array = setting_helper()->settings_array;			
			if( isset( $settings_array['on_single_product_page'] ) && 'yes' === $settings_array['on_single_product_page'] ){
				global $product;
				if ( ! is_product() || $product->is_type( 'external' ) || $product->is_type( 'grouped' ) ) {
					return;
				}
                wcapp_enqueue_paypal_sdk_function();				
				echo '<div class="wcapp_paypal_button_wrapper" style="margin-top:10px"><div id="wcapp_mask" style="position:absolute;background:transparent;z-index:999;cursor:pointer" onclick="goCheck()"></div><div id="woo_wcapp_paypal_button"></div><div id="product_paypal_button"></div></div>';
				
				if($settings_array['google_pay_checked']=="yes"){
				    echo '<div id="wcapp_googlepay_container"></div>';
					echo '<style>.gpay-card-info-container {max-width:750px;width:100%;margin-top:6px;}</style>';
				}
				
				if($settings_array['apple_pay_checked']=="yes"){
					echo '<div id="wcapp_applepay_container"></div>';
		            echo '<style>
						apple-pay-button {
						  --apple-pay-button-max-width: 750px;
						  --apple-pay-button-width: 100%;
						  --apple-pay-button-height: 40px;
						  --apple-pay-button-border-radius: 3px;
						  --apple-pay-button-padding: 0px 0px;
						  --apple-pay-button-box-sizing: border-box;
						  max-width: 750px;
						  margin-top:6px;
						}
						</style>';
				}
					
				wp_enqueue_script( 'wc-gateway-wcapp-generate-cart', WCAPP_PLUGIN_URL.'assets/js/wcapp-generate-cart.js', array( 'jquery' ), '1.0', true );
				wp_localize_script(
					'wc-gateway-wcapp-generate-cart',
					'wc_wcapp_generate_cart_context',
					array(
						'generate_cart_nonce' => wp_create_nonce( '_wc_wcapp_generate_cart_nonce' ),
						'ajaxurl'             => WC_AJAX::get_endpoint( 'wc_wcapp_generate_cart' ),
						//'checkout_url'             => wc_get_checkout_url().'?payment=PayPal',
						'checkout_url'             => $this->get_checkout_url(),
					)
				);
			}
		}

		/**
		 * Display paypal button on the cart page.
		 */
		public function display_paypal_button_cart() {
			$settings_array = setting_helper()->settings_array;	
			if( isset( $settings_array['on_cart_page'] ) && 'yes' === $settings_array['on_cart_page'] ){
				wcapp_enqueue_paypal_sdk_function();
				echo '<div class="wcapp_paypal_button_wrapper"><div id="wcapp_mask" style="position:absolute;background:transparent;z-index:999;cursor:pointer" onclick="goCheck()"></div><div id="woo_wcapp_paypal_button"></div></div>';
				
				if($settings_array['google_pay_checked']=="yes"){
				    echo '<div id="wcapp_googlepay_container"></div>';
					echo '<style>.gpay-card-info-container {max-width:750px;width:100%;margin-top:6px;}</style>';
				}
				
				if($settings_array['apple_pay_checked']=="yes"){
					echo '<div id="wcapp_applepay_container"></div>';
		            echo '<style>
						apple-pay-button {
						  --apple-pay-button-max-width: 750px;
						  --apple-pay-button-width: 100%;
						  --apple-pay-button-height: 40px;
						  --apple-pay-button-border-radius: 3px;
						  --apple-pay-button-padding: 0px 0px;
						  --apple-pay-button-box-sizing: border-box;
						  max-width: 750px;
						  margin-top:6px;
						}
						</style>';
				}
			}
		}
		
		/**
		 * Generates the cart for PayPal Checkout on a product level.
		 * TODO: Why not let the default "add-to-cart" PHP form handler insert the product into the cart? Investigate.
		 *
		 * @since 1.0.0
		 */
		public function wc_ajax_generate_cart() {
			global $post;

			if ( empty( sanitize_text_field($_POST['nonce'] )) || ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), '_wc_wcapp_generate_cart_nonce' ) ) { 
				wp_die( __( 'error', 'advanced-paypal-payments-for-woocommerce' ) );
			}

			WC()->shipping->reset_shipping();
			$product = wc_get_product( $post->ID );

			if ( ! empty( sanitize_text_field($_POST['wcapp-add-to-cart'] )) ) {
				$product = wc_get_product( absint( sanitize_text_field($_POST['wcapp-add-to-cart']) ) );
			}

			/**
			 * If this page is single product page, we need to simulate
			 * adding the product to the cart taken account if it is a
			 * simple or variable product.
			 */
			if ( $product ) {
				$qty = ! isset( $_POST['quantity'] ) ? 1 : absint( sanitize_text_field($_POST['quantity']) );
				wc_empty_cart();

				if ( $product->is_type( 'variable' ) ) {
					$attributes = array();

					foreach ( $product->get_attributes() as $attribute ) {
						if ( ! $attribute['is_variation'] ) {
							continue;
						}

						$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );

						if ( isset( $_POST['attributes'][ $attribute_key ] ) ) {
							if ( $attribute['is_taxonomy'] ) {
								// Don't use wc_clean as it destroys sanitized characters.
								$value = sanitize_title( wp_unslash( $_POST['attributes'][ $attribute_key ] ) );
							} else {
								$value = html_entity_decode( wc_clean( wp_unslash( $_POST['attributes'][ $attribute_key ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );
							}

							$attributes[ $attribute_key ] = $value;
						}
					}

					if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
						$variation_id = $product->get_matching_variation( $attributes );
					} else {
						$data_store   = WC_Data_Store::load( 'product' );
						$variation_id = $data_store->find_matching_product_variation( $product, $attributes );
					}

					WC()->cart->add_to_cart( $product->get_id(), $qty, $variation_id, $attributes );
				} else {
					WC()->cart->add_to_cart( $product->get_id(), $qty );
				}

				WC()->cart->calculate_totals();
			}

			wp_send_json( new stdClass() );
		}		
	}
	new WCAPP_Cart_Handler();
}