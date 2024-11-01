<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_AJAX_Handler' ) ) {
	
	/**
	 * WCAPP Ajax Class.
	 */
	class WCAPP_AJAX_Handler {
		
		/**
		 * constructor.
		 *
		 */
		public function __construct() {
			add_action( 'wc_ajax_wcapp_validate', array( $this, 'wcapp_validate_function' ) );
			add_action( 'wc_ajax_wcapp_create_order', array( $this, 'wcapp_create_order_function' ) );
			add_action( 'wc_ajax_wcapp_create_order_for_order_pay', array( $this, 'wcapp_create_order_for_order_pay_function' ) );
			add_action( 'wc_ajax_wcapp_credential', array( $this, 'wcapp_credential_function' ) );
			add_action( 'wc_ajax_wcapp_disconnect', array( $this, 'wcapp_disconnect_function' ) );
			add_action( 'wc_ajax_wcapp_return_url', array( $this, 'wcapp_return_url_function' ) );
			add_action( 'wc_ajax_wcapp_capture', array( $this, 'wcapp_capture_function' ) );
			add_action( 'wc_ajax_wcapp_order_total', array( $this, 'wcapp_order_total_function' ) );
		}
		
		/**
		* validate checkout data
		*/
		public function wcapp_validate_function() {
			if ( empty( sanitize_text_field($_POST['nonce'] )) || ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), '_wc_wcapp_validate_nonce' ) ) {
				wp_die( __( 'error', 'advanced-paypal-payments-for-woocommerce' ) );
			}
			
			try {
				if ( WC()->cart->is_empty() ) {
					throw new Exception( $expiry_message );
				}
				
				$errors      = new WP_Error();
				$posted_data = WC()->checkout()->get_posted_data();

                wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
                $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
				$posted_shipping_methods = isset( $_POST['shipping_method'] ) ? wc_clean( wp_unslash( $_POST['shipping_method'] ) ) : array();

				if ( is_array( $posted_shipping_methods ) ) {
					foreach ( $posted_shipping_methods as $i => $value ) {
						$chosen_shipping_methods[ $i ] = $value;
					}
				}

				WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
				
				// Validate posted data and cart items before proceeding.
				$WCAPP_Checkout = new WCAPP_Checkout();
				$WCAPP_Checkout->wcapp_validate_checkout( $posted_data, $errors );
				if( isset( $_POST['shipping_method'] ) && ! $chosen_shipping_methods[0] ){
					$errors->add( 'shipping', __( 'No shipping method has been selected. Please double check your address, or contact us if you need any help.', 'advanced-paypal-payments-for-woocommerce' ) );
				}
				
				if($errors->has_errors()){
					$result=array(
						'result'=>'error',
						'messages'=> $errors->get_error_messages(),
					);
				}
				else{
					$result=array(
						'result'=>'success',
						'messages'=>'',
					);
				}
				echo json_encode($result);
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );
				$result=array(
					'result'=>'error',
					'messages'=>$e->getMessage(),
				);
				echo json_encode($result);
			}
		}
		
		/**
		* call paypal api create order
		*/
		public function wcapp_create_order_function() {
			if ( empty( sanitize_text_field($_POST['nonce'] )) || ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), '_wc_wcapp_create_order_nonce' ) ) {
				wp_die( __( 'error', 'advanced-paypal-payments-for-woocommerce' ) );
			}

			$total=WC()->cart->get_total( 'raw' );
			$item_total=WC()->cart->get_subtotal();
			$shipping=WC()->cart->get_shipping_total();
			$handling=WC()->cart->get_fee_total();
			$tax_total=WC()->cart->get_total_tax();
			$discount=WC()->cart->get_discount_total();
			
			if($shipping<0){
				$discount+=abs($shipping);
				$shipping=0;
			}
			if($handling<0){
				$discount+=abs($handling);
				$handling=0;
			}
			if($tax_total<0){
				$discount+=abs($tax_total);
				$tax_total=0;
			}
			
			$customer_id=get_current_user_id();
			$currency=get_woocommerce_currency();
			
			$item_list='';
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			   $product = $cart_item['data'];
			   $product_id = $cart_item['product_id'];
			   $quantity = $cart_item['quantity'];
			   $price = $product->get_price();
			   //$subtotal = WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] );
			   $link = $product->get_permalink( $cart_item );
			   $item_list.='{
								"name": "'.substr($product->get_name(),0,127).'",
								"sku": "'.substr($product->get_sku(),0,127).'",
								"quantity": "'.substr($quantity,0,10).'",
								"unit_amount":
								{
									"currency_code": "'.$currency.'",
									"value": "'.$price.'"
								}
							},';
				//$item_total+=$price*$quantity;
			}
			$item_list=substr($item_list,0,strlen($item_list)-1);
			
			$settings_array = setting_helper()->settings_array;
			$intent = $settings_array['payment_action'];
			$locale = get_bloginfo("language");
			$payee_preferred = $settings_array['instant_payments']=='yes'?'IMMEDIATE_PAYMENT_REQUIRED':'UNRESTRICTED';
			$brand_name = $settings_array['brand_name'];
			//$logo_url = $settings_array['logo'];
			$landing_page = strtoupper($settings_array['landing_page']);
			//$description="pay success";
			//$soft_descriptor='soft_descriptor';
			
			$merchantid='';
			$merchant_email='';
			$env = $settings_array['env'];
			if($env=='sandbox'){
			   $clientid=$settings_array['client_id_sandbox'];
			   $merchantid=$settings_array['merchant_id_sandbox'];
			   $merchant_email=$settings_array['merchant_email_sandbox'];
			}else{
			   $clientid=$settings_array['client_id_production'];
			   $merchantid=$settings_array['merchant_id_production'];
			   $merchant_email=$settings_array['merchant_email_production'];
			}
			$merchant='';
			if(!empty($merchantid)){
				$merchant='"merchant_id":"'.$merchantid.'",';
			}
				
			$invoice='';
			$invoice=date("Ymdhis").rand(100, 999);
			if(!empty($settings_array['invoice_prefix']))
				$invoice=$settings_array['invoice_prefix'].date("Ymdhis").rand(100, 999);
			WC()->session->set( 'wcapp_invoice_id', $invoice );
			
		    $address_shipper='';
			$shipping_preference='NO_SHIPPING';
			
			$ship_to_different_address=sanitize_text_field($_POST['ship_to_different_address']);
			
			$shipping_country=sanitize_text_field($_POST['billing_country']);
			if($ship_to_different_address=="1")
				$shipping_country=sanitize_text_field($_POST['shipping_country']);

			$state=sanitize_text_field($_POST['billing_state']);
			if($ship_to_different_address=="1")
				$state=sanitize_text_field($_POST['shipping_state']);
				
			$shipping_city=sanitize_text_field($_POST['billing_city']);
			if($ship_to_different_address=="1")
				$shipping_city=sanitize_text_field($_POST['shipping_city']);
				
			$shipping_postcode=sanitize_text_field($_POST['billing_postcode']);
			if($ship_to_different_address=="1")
				$shipping_postcode=sanitize_text_field($_POST['shipping_postcode']);
			
			$shipping_address_1=sanitize_text_field($_POST['billing_address_1']);
			if($ship_to_different_address=="1")
				$shipping_address_1=sanitize_text_field($_POST['shipping_address_1']);
				
			$shipping_address_2=sanitize_text_field($_POST['billing_address_2']);
			if($ship_to_different_address=="1")
				$shipping_address_2=sanitize_text_field($_POST['shipping_address_2']);
				
			if(!empty($shipping_country) && !empty($shipping_city) && !empty($shipping_postcode)){
				$state_name=WC()->countries->states[$shipping_country][$state];
				if(empty($state_name)){
					$state_name=$state;
				}
				
				if($shipping_country=='CN')
					$shipping_country='C2';
				
				$address_shipper=',"address": {
							  "country_code":"'.$shipping_country.'",
							  "address_line_1":"'.$shipping_address_1.'",
							  "address_line_2":"'.$shipping_address_2.'",
							  "admin_area_1":"'.$state_name.'",
							  "admin_area_2":"'.$shipping_city.'",
							  "postal_code":"'.$shipping_postcode.'"
							}';
				$shipping_preference='SET_PROVIDED_ADDRESS';
			}
			
			$return_url=wc_get_checkout_url().get_option('woocommerce_checkout_order_received_endpoint');
			
			$orderData = '{
				"intent":"'.$intent.'",
				"purchase_units":[ 
					{           
						"invoice_id":"'.$invoice.'",
						"amount":{
							"currency_code":"'.$currency.'",
							"value":"'.$total.'",
							"breakdown":{
								"item_total":{
									"currency_code":"'.$currency.'",
								    "value":"'.$item_total.'"
								},
								"shipping":{
									"currency_code": "'.$currency.'",
									"value": "'.$shipping.'"
								},
								"handling":{
									"currency_code": "'.$currency.'",
									"value": "'.$handling.'"
								},
								"tax_total":{
									"currency_code": "'.$currency.'",
									"value": "'.$tax_total.'"
								},
								"discount":{
									"currency_code": "'.$currency.'",
									"value": "'.$discount.'"
								}
							}	
						},
						"items":[
						    '.$item_list.'
						],
						"payee":{
							'.$merchant.'
							"email_address":"'.$merchant_email.'"
						},
						"shipping":{
							"name": {
							  "full_name":"'.sanitize_text_field($_POST['billing_first_name']).' '.sanitize_text_field($_POST['billing_last_name']).'"
							}
							'.$address_shipper.'
						}
					}
				],
				"application_context":{
					"brand_name":"'.$brand_name.'",
					"landing_page":"'.$landing_page.'",
					"locale":"'.$locale.'",
					"user_action":"PAY_NOW",
					"shipping_preference":"'.$shipping_preference.'",
					"return_url":"'.$return_url.'"
				},
				"payment_method":
				{
					"payee_preferred":"'.$payee_preferred.'",
					"payer_selected":"PAYPAL"
				}
			}';	

			logger_helper()->log('info', 'create order：'.$orderData);
			   
			$paypalHelper = new WCAPP_PayPal();
			$result=$paypalHelper->orderCreate($orderData);

			echo json_encode($result);
		}
		
		/**
		* call paypal api create order for order pay
		*/
		public function wcapp_create_order_for_order_pay_function() {
			if ( empty( sanitize_text_field($_POST['nonce'] )) || ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), '_wc_wcapp_create_order_for_order_pay_nonce' ) ) {
				wp_die( __( 'error', 'advanced-paypal-payments-for-woocommerce' ) );
			}
			$key=sanitize_text_field($_POST['key']);
			$order_id = wc_get_order_id_by_order_key($key);
			if( ! isset($order_id) ){
				return;
			}
			
			$order = wc_get_order( $order_id );
			if( $order ){
				$current_user_id=get_current_user_id();
				$customer_id = $order->get_user_id();
				if($current_user_id != $customer_id){
					return;
				}
				if($order->get_status() != 'pending'){
					return;
				}
				$total=$order->get_total();
				$item_total=$order->get_subtotal();
				$shipping=$order->get_total_shipping();
				$handling=$order->get_total_fees();
				if(empty($handling)){
					$handling=0;
				}
				$tax_total=$order->get_total_tax();
				$discount=$order->get_total_discount();
				$currency=$order->get_currency();
				$data = $order->get_data();
				
				if($shipping<0){
					$discount+=abs($shipping);
					$shipping=0;
				}
				if($handling<0){
					$discount+=abs($handling);
					$handling=0;
				}
				if($tax_total<0){
					$discount+=abs($tax_total);
					$tax_total=0;
				}
				
				// Get and Loop Over Order Items
				$item_list='';
				foreach ( $order->get_items() as $item_id => $item ) {
					$product_name = substr($item->get_name(),0,127);
					$quantity = substr($item->get_quantity(),0,10);
					
					$product = $item->get_product();
					$price   = $product->get_price();
					$sku   = substr($product->get_sku(),0,127);
					
					$item_list.='{
								"name": "'.$product_name.'",
								"sku": "'.$sku.'",
								"quantity": "'.$quantity.'",
								"unit_amount":
								{
									"currency_code": "'.$currency.'",
									"value": "'.$price.'"
								}
							},';
                    //$item_total+=$price*$quantity;
				}
				$item_list=substr($item_list,0,strlen($item_list)-1);
				
				$settings_array = setting_helper()->settings_array;
				$intent = $settings_array['payment_action'];
				$locale = get_bloginfo("language");
				$payee_preferred = $settings_array['instant_payments']=='yes'?'IMMEDIATE_PAYMENT_REQUIRED':'UNRESTRICTED';
				$brand_name = $settings_array['brand_name'];
				$landing_page = strtoupper($settings_array['landing_page']);
				
				$merchantid='';
				$merchant_email='';
				$env = $settings_array['env'];
				if($env=='sandbox'){
				   $clientid=$settings_array['client_id_sandbox'];
				   $merchantid=$settings_array['merchant_id_sandbox'];
				   $merchant_email=$settings_array['merchant_email_sandbox'];
				}else{
				   $clientid=$settings_array['client_id_production'];
				   $merchantid=$settings_array['merchant_id_production'];
				   $merchant_email=$settings_array['merchant_email_production'];
				}
				$merchant='';
				if(!empty($merchantid)){
					$merchant='"merchant_id":"'.$merchantid.'",';
				}
					
				$invoice='';
				$invoice=date("Ymdhis").rand(100, 999);
				if(!empty($settings_array['invoice_prefix']))
					$invoice=$settings_array['invoice_prefix'].date("Ymdhis").rand(100, 999);
				WC()->session->set( 'wcapp_invoice_id', $invoice );
				
				$address_shipper='';
				$shipping_preference='NO_SHIPPING';
				
				$shipping_country=$data['shipping']['country'];
				if(empty($shipping_country))
				    $shipping_country=$data['billing']['country'];
								
				$shipping_address_1=$data['shipping']['address_1'];
				if(empty($shipping_address_1))
				    $shipping_address_1=$data['billing']['address_1'];
					
				$shipping_address_2=$data['shipping']['address_2'];
				if(empty($shipping_address_2))
				    $shipping_address_2=$data['billing']['address_2'];

				$shipping_state=$data['shipping']['state'];
				if(empty($shipping_state))
				    $shipping_state=$data['billing']['state'];
				
					
				$shipping_city=$data['shipping']['city'];
				if(empty($shipping_city))
				    $shipping_city=$data['billing']['city'];
					
				$shipping_postcode=$data['shipping']['postcode'];
				if(empty($shipping_postcode))
				    $shipping_postcode=$data['billing']['postcode'];
				
				if(!empty($shipping_country) && !empty($shipping_city) && !empty($shipping_postcode)){
					$state_name=WC()->countries->states[$shipping_country][$shipping_state];
					if(empty($state_name)){
						$state_name=$shipping_state;
					}
					
					if($shipping_country=='CN')
						$shipping_country='C2';
					
					$address_shipper=',"address": {
								  "country_code":"'.$shipping_country.'",
								  "address_line_1":"'.$shipping_address_1.'",
								  "address_line_2":"'.$shipping_address_2.'",
								  "admin_area_1":"'.$state_name.'",
								  "admin_area_2":"'.$shipping_city.'",
								  "postal_code":"'.$shipping_postcode.'"
								}';
					$shipping_preference='SET_PROVIDED_ADDRESS';
				}
				
				$return_url=$order->get_checkout_order_received_url();
				
				$orderData = '{
					"intent":"'.$intent.'",
					"purchase_units":[ 
						{           
							"invoice_id":"'.$invoice.'",
							"amount":{
								"currency_code":"'.$currency.'",
								"value":"'.$total.'",
								"breakdown":{
									"item_total":{
										"currency_code":"'.$currency.'",
										"value":"'.$item_total.'"
									},
									"shipping":{
										"currency_code": "'.$currency.'",
										"value": "'.$shipping.'"
									},
									"handling":{
										"currency_code": "'.$currency.'",
										"value": "'.$handling.'"
									},
									"tax_total":{
										"currency_code": "'.$currency.'",
										"value": "'.$tax_total.'"
									},
									"discount":{
										"currency_code": "'.$currency.'",
										"value": "'.$discount.'"
									}
								}	                
							},
							"items":[
								'.$item_list.'
							],
							"payee":{
								'.$merchant.'
							    "email_address":"'.$merchant_email.'"
							},
							"shipping":{
								"name": {
								  "full_name":"'.$data['billing']['first_name'].' '.$data['billing']['last_name'].'"
								}
								'.$address_shipper.'
							}
						}
					],
					"application_context":{
						"brand_name":"'.$brand_name.'",
						"landing_page":"'.$landing_page.'",
						"locale":"'.$locale.'",
						"user_action":"PAY_NOW",
						"shipping_preference": "SET_PROVIDED_ADDRESS",
						"return_url":"'.$return_url.'"
					},
					"payment_method":
					{
						"payee_preferred":"'.$payee_preferred.'",
						"payer_selected":"PAYPAL"
					}
				}';
				
				logger_helper()->log('info', 'create order for order pay：'.$orderData);
				   
				$paypalHelper = new WCAPP_PayPal();
				$result=$paypalHelper->orderCreate($orderData);

				echo json_encode($result);
			}
		}
		
		/**
		* save env, sharedId, authCode, seller_nonce
		*/
		public function wcapp_credential_function() {
			$env=sanitize_text_field($_POST['env']);
			$shared_id=sanitize_text_field($_POST['shared_id']);
			$auth_code=sanitize_text_field($_POST['auth_code']);
			$seller_nonce=sanitize_text_field($_POST['seller_nonce']);
			
			//need to save args to database
			$setting_new_helper = setting_new_helper();
			$setting_new_helper->set( 'env', $env );
			$setting_new_helper->set( 'shared_id', $shared_id );
			$setting_new_helper->set( 'auth_code', $auth_code );
			$setting_new_helper->set( 'seller_nonce', $seller_nonce );
			$setting_new_helper->persist();
			
			echo "ok";
		}
		
		/**
		* disconnect account
		*/
		public function wcapp_disconnect_function() {
			$disconnect=sanitize_text_field($_POST['disconnect']);
			
			if($disconnect=='yes'){
				$arr=array('connect_status' => 'no');
				update_option( 'woocommerce_advanced-paypal-payments-for-woocommerce_settings', $arr );
			}
			echo "ok";
		}
		
		/**
		* go to real url
		*/
		public function wcapp_return_url_function() {
			$merchantId=sanitize_text_field($_GET['merchantId']);
			$new_url=home_url().'/wp-admin/admin.php?page=wc-settings&tab=checkout&section=advanced-paypal-payments-for-woocommerce&merchantId='.$merchantId;
			
			//header("Location: $new_url");
			wp_safe_redirect($new_url);
			exit;			
		}
		
		/**
		* call paypal api capture
		*/
		public function wcapp_capture_function() {
			if ( empty( sanitize_text_field($_POST['nonce'] )) || ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), '_wc_wcapp_capture_nonce' ) ) {
				wp_die( __( 'error', 'advanced-paypal-payments-for-woocommerce' ) );
			}
			$order_id=sanitize_text_field($_POST['order_id']);
			$paypalHelper = new WCAPP_PayPal();
			$result=$paypalHelper->orderCapture($order_id);
			echo json_encode($result);
		}
		
		/**
		* apple total
		*/
		public function wcapp_order_total_function() {
			if ( empty( sanitize_text_field($_POST['nonce'] )) || ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), '_wc_wcapp_order_total_nonce' ) ) {
				wp_die( __( 'error', 'advanced-paypal-payments-for-woocommerce' ) );
			}
			$total=WC()->cart->get_total( 'raw' );
			$result=array(
			        "label" => get_option( 'blogname' ),
					"amount" => $total,
					);
			echo json_encode($result);
		}
	}
	new WCAPP_AJAX_Handler();
}