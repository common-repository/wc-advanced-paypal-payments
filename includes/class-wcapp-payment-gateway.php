<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_PayPal_Payment_Gateway' ) ) {
	/**
	 * WCAPP_PayPal_Payment_Gateway class.
	 *
	 * @extends WC_Payment_Gateway
	 */
	class WCAPP_PayPal_Payment_Gateway extends WC_Payment_Gateway {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$settings_array = setting_helper()->settings_array;
			$payment_title=esc_html( $settings_array['title'] );
			if($payment_title==''){
				$payment_title='PayPal';
			}

			$this->id				  = WCAPP_PLUGIN_ID;
			$this->has_fields         = true;
			//$this->supports[]         = 'refunds';
			$this->method_title       = 'Advanced PayPal Payments for WooCommerce';
			$this->method_description = __( 'Very reliable checkout solution with quick & professional support by PayPal certificated Partner.', 'advanced-paypal-payments-for-woocommerce' );
			
			$this->title              =  $payment_title; 
			$this->description        ='Pay via PayPal.';
			$this->instructions       = $this->get_option( 'instructions', $this->description );
			
			$this->init_form_fields();
			$this->init_settings();
			
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'update_admin_options' ) );
		}		
		
		/**
		 * Initialise Gateway Settings Form Fields.
		 */
		public function init_form_fields() {
			$setting_new_helper = setting_new_helper();
			$settings_array = setting_helper()->settings_array;
			$merchantId=sanitize_text_field($_GET['merchantId']);//need more args
			if ( is_admin() && !empty($merchantId) && $settings_array['connect_status']=='no') {
				//sleep(3);
				
				//need to reload args from database
				$env=$setting_new_helper->get('env');
				$shared_id=$setting_new_helper->get('shared_id');
				$auth_code=$setting_new_helper->get('auth_code');
				$seller_nonce=$setting_new_helper->get('seller_nonce');
				$paypalHelper = new WCAPP_PayPal($env);
				$result=$paypalHelper->customerCredential($shared_id,$auth_code,$seller_nonce);
				if(!empty($result)&&$result['ack']==true){
					//$env=$result['env'];
					$client_id=$result['client_id'];
					$client_secret=$result['client_secret'];
					$payer_id=$result['payer_id'];
					
					
					if($env=="sandbox"){
						//$setting_new_helper->set( 'env', $env );
						$setting_new_helper->set( 'merchant_id_sandbox', $payer_id );
						$setting_new_helper->set( 'client_id_sandbox', $client_id );
						$setting_new_helper->set( 'client_secret_sandbox', $client_secret );
						$setting_new_helper->set( 'merchant_email_sandbox', $merchantId );
					}else if($env=="live"){
						//$setting_new_helper->set( 'env', $env );
						$setting_new_helper->set( 'merchant_id_production', $payer_id );
						$setting_new_helper->set( 'client_id_production', $client_id );
						$setting_new_helper->set( 'client_secret_production', $client_secret );
						$setting_new_helper->set( 'merchant_email_production', $merchantId );
					}
                    
					logger_helper()->log('info','env:'.$env.' and client_id:'.$client_id.' and client_secret:'.$client_secret);
					$result=wcapp_webhook_create_function($env, $client_id, $client_secret);
					logger_helper()->log('info','Webhook return info:'.json_encode($result));
					if(!$result['is_validate']){
						add_action(
							'admin_notices',
							function () {
								printf(
									'<div class="notice notice-error"><p>'.__( 'Authentication with PayPal failed: Could not create token.', 'advanced-paypal-payments-for-woocommerce' ).'</p><p>'.__( 'Please verify your API Credentials and try again to connect your PayPal account. Visit the <a href="https://paypal.uin88.com/" target="_blank">plugin documentation</a> for more information about the setup.', 'advanced-paypal-payments-for-woocommerce' ).'</p></div>'
								);
							}
						);
						
						//connect_status change to no
						$setting_new_helper->set( 'connect_status', 'no' );
					}else{
						//connect_status change to yes
						$setting_new_helper->set( 'connect_status', 'yes' );
						//is_third_party_mode change to yes
						$setting_new_helper->set( 'is_third_party_mode', 'yes' );
						//seller_protection change to no
						$setting_new_helper->set( 'seller_protection', 'no' );
					}
				
					if($result['is_webhook']){
			        	if($env=='sandbox'){
							$setting_new_helper->set( 'current_webhook_status', 'yes' );
							$setting_new_helper->set( 'subscribed_webhooks', json_encode($result) );
							$setting_new_helper->set( 'webhook_id_sandbox', $result['webhook_id'] );
						}else if($env=='live'){
							$setting_new_helper->set( 'current_webhook_status', 'yes' );
							$setting_new_helper->set( 'subscribed_webhooks', json_encode($result) );
							$setting_new_helper->set( 'webhook_id_live', $result['webhook_id'] );
						}
					}else{
						$setting_new_helper->set( 'current_webhook_status', 'no' );
						if($result['is_validate']){
							add_action(
								'admin_notices',
								function () {
									printf('<div class="notice notice-error"><p>'.__( 'Subscribed failed: Not a valid webhook URL.', 'advanced-paypal-payments-for-woocommerce' ).'</p></div>');
								}
							);
						}
					}
					$setting_new_helper->persist();
				}
			}
			$this->form_fields = include 'Settings/wcapp-settings.php';
			if( is_admin() ){
				wp_enqueue_script( 'pp_admin_js', WCAPP_PLUGIN_URL.'assets/js/admin/wcapp-admin.js' ); 
				wp_enqueue_script( 'paypal', 'https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js', array( 'jquery' ), null, true );
				wp_enqueue_style( 'pp_admin_style', WCAPP_PLUGIN_URL.'assets/css/admin/admin.css' ); 
				$array = array(
					'resubscribe_button' => __( 'Resubscribe', 'advanced-paypal-payments-for-woocommerce' ),
					'subscribed_failed_label' => __( 'Subscribed failed', 'advanced-paypal-payments-for-woocommerce' ),
					'subscribed_success_label' => __( 'Subscribed success', 'advanced-paypal-payments-for-woocommerce' ),
					'return_url' => home_url().'/?wc-ajax=wcapp_return_url',
					'website_url' => home_url(),
					'brand_name' => $settings_array['brand_name'],
					'save_seller_nonce' => wp_create_nonce( '_wc_wcapp_save_seller_nonce' ),
					'ajaxurl' => WC_AJAX::get_endpoint( 'wcapp_save_seller' ),
					'ajax_credential_url'=> WC_AJAX::get_endpoint( 'wcapp_credential' ),		
					'disconnect_url'=> WC_AJAX::get_endpoint( 'wcapp_disconnect' ),
					'plugin_url' => admin_url('admin.php?page=wc-settings&tab=checkout&section=advanced-paypal-payments-for-woocommerce'),
					'connect_status_label' => __( 'Status: Connected', 'advanced-paypal-payments-for-woocommerce' ),
					'disconnect_account_label' => __( 'Disconnect Account', 'advanced-paypal-payments-for-woocommerce' ),
					'disconnect_confirm_label' => __( 'Are you sure to disconnect your PayPal?', 'advanced-paypal-payments-for-woocommerce' ),
					'advanced_credit_card_confirm_label' => __( 'The advanced credit card gateway is to allow your customer and input credit card on your web site, which requires PayPal business account. Can you confirm your PayPal account is business account?', 'advanced-paypal-payments-for-woocommerce' ),
					'apple_pay_confirm_label' => __( 'Apple Pay gateway is to allow your customer use Apple Pay on your web site, which requires PayPal business account. Can you confirm your PayPal account is business account?', 'advanced-paypal-payments-for-woocommerce' ),
					'google_pay_confirm_label' => __( 'Google Pay gateway is to allow your customer use Google Pay on your web site, which requires PayPal business account. Can you confirm your PayPal account is business account?', 'advanced-paypal-payments-for-woocommerce' ),
				);
				wp_localize_script( 'pp_admin_js', 'wc_wcapp_admin_context', $array );
			}
		}
      
		/*
		
		 *  Payment gateway form fields
		 */
		 public function payment_fields() {
			$settings_array = setting_helper()->settings_array;
			$gateway_description=$settings_array['gateway_description']==''?'PayPal':$settings_array['gateway_description'];
			echo esc_html($gateway_description); 
		}

		/*
		 * Custom CSS and JS
		 */
		public function payment_scripts() {

		}

		/*
		 * Fields validation
		 */
		public function validate_fields() {

		// code here

		}
		/*
		 * Add webhook, like PayPal IPN, etc
		 */
		public function webhook() {

		// code here
					
		}
		
		/**
		* process payment
		* @param int $order_id
		*/
		public function process_payment( $order_id ) {
			logger_helper()->log('info', 'order id：'.$order_id.' transaction_id：'.sanitize_text_field($_POST['transaction_id']));
			$pay_for_order=sanitize_text_field($_GET['pay_for_order']);
			$capture_id = sanitize_text_field($_POST['capture_id']);
			$payment_source = sanitize_text_field($_POST['payment_source']);
			
			$paypalHelper = new WCAPP_PayPal();
		    $result=$paypalHelper->getCapture($capture_id);	
			if($result['ack']==true){
				$paypal_amount=floatval($result['data']['amount']['value']);
			    $paypal_status=$result['data']['status'];
				$paypal_invoice_id=$result['data']['invoice_id'];
				$seller_protection_status=$result['data']['seller_protection']['status'];
				if($paypal_status == 'COMPLETED' && $paypal_invoice_id == WC()->session->get( 'wcapp_invoice_id')){
					$order = wc_get_order( $order_id );
					$order_amount=$order->get_total();
					if ( isset( $_POST['transaction_id'] )) {
						update_post_meta( $order_id, '_transaction_id', sanitize_text_field($_POST['transaction_id']) );
						
						$order->add_order_note(
							sprintf(
								/* translators: %s is the PayPal transaction ID */
								__( 'PayPal transaction ID: %s ; capture ID : %s ', 'advanced-paypal-payments-for-woocommerce' ),
								sanitize_text_field($_POST['transaction_id']), $capture_id
							)
						);
					}
					
					if ( isset( $_POST['paypal_intent'] ) ) {
						update_post_meta( $order_id, '_paypal_intent', sanitize_text_field($_POST['paypal_intent']) );
					}
					
					if ( isset( $paypal_invoice_id ) ) {
						update_post_meta( $order_id, '_invoice_id', $paypal_invoice_id );
					}
					
					if ( isset( $capture_id ) ) {
						update_post_meta( $order_id, '_capture_id', $capture_id );
					}

					if ( isset( $paypal_status ) ) {
						update_post_meta( $order_id, '_capture_status', $paypal_status );
					}
					
					if ( isset( $seller_protection_status ) ) {
						update_post_meta( $order_id, '_seller_protection_status', $seller_protection_status );
					}
					
					if ( isset( $payment_source ) ) {
						update_post_meta( $order_id, '_payment_source', $payment_source );
					}
					
					logger_helper()->log('info', 'capture_id：'.$capture_id.' paypal_return_detail：'.sanitize_text_field($_POST['paypal_return_detail']).' pay_for_order：'.$pay_for_order.' payment_source：'.$payment_source.' invoice_id：'.$paypal_invoice_id);
				
					if($paypal_status=="COMPLETED" && $paypal_amount==$order_amount){
						$order->update_status( 'processing','complete payment');
						// Reduce stock levels
						$order->reduce_order_stock();
					}
							
					if ( isset( $pay_for_order ) && 'true' === $pay_for_order ) {			
						
					}else{
						// Remove cart
						WC()->cart->empty_cart();
					}
					WC()->session->set( 'wcapp_invoice_id', '' );		
					// Return thankyou redirect
					return array(
						'result'    => 'success',
						'redirect'  => $this->get_return_url( $order )
					);
				}
			}
		}
		/**
		 * Output for the order received page.
		 */
		public function thankyou_page() {
			if ( $this->instructions ) {
				echo esc_html(wpautop( wptexturize( $this->instructions ) ));
			}
		}
		
		/**
		* save paypal admin options
		*/
		public function update_admin_options() {
			$env=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_env']);
			$sandbox_client_id=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_client_id_sandbox']);
			$sandbox_client_secret=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_sandbox']);
			$live_client_id=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_client_id_production']);
			$live_client_secret=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_production']);
			
			$client_id='';
			$client_secret='';
			$hidden_resubscribe_webhooks=sanitize_text_field($_POST['hidden_resubscribe_webhooks']);
		
			$settings_array = setting_helper()->settings_array;
			$do_getToken=true;
			if($env=='sandbox'){
				if($settings_array['client_id_sandbox']==$sandbox_client_id && $settings_array['client_secret_sandbox']==$sandbox_client_secret){
					$do_getToken=false;
				}
				$client_id=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_client_id_sandbox']);
				$client_secret=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_sandbox']);
			}else if($env=='live'){
				if($settings_array['client_id_production']==$live_client_id && $settings_array['client_secret_production']==$live_client_secret){
					$do_getToken=false;
				}
				$client_id=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_client_id_production']);
				$client_secret=sanitize_text_field($_POST['woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_production']);
			}
			
			if($do_getToken || $hidden_resubscribe_webhooks=='Resubscribe' || $env != $settings_array['env'] || $settings_array['connect_status']=='no'){
				logger_helper()->log('info','env:'.$env.' and client_id:'.$client_id.' and client_secret:'.$client_secret);
				$result=wcapp_webhook_create_function($env, $client_id, $client_secret);
				logger_helper()->log('info','Webhook return info:'.json_encode($result));
				if(!$result['is_validate']){
					add_action(
						'admin_notices',
						function () {
							printf(
								'<div class="notice notice-error"><p>'.__( 'Authentication with PayPal failed: Could not create token.', 'advanced-paypal-payments-for-woocommerce' ).'</p><p>'.__( 'Please verify your API Credentials and try again to connect your PayPal account. Visit the <a href="https://paypal.uin88.com/" target="_blank">plugin documentation</a> for more information about the setup.', 'advanced-paypal-payments-for-woocommerce' ).'</p></div>'
							);
						}
					);
					//connect_status change to no
					$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_connect_status"] = 'no';
				}else{
					//connect_status change to yes
					$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_connect_status"] = 'yes';
					//is_third_party_mode change to no
					$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_is_third_party_mode"] = 'no';
				}
				if($result['is_webhook']){
					$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_current_webhook_status"] = 'yes';
					$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks"] = json_encode($result);
			        if($env=='sandbox'){
						$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_webhook_id_sandbox"] = $result['webhook_id'];
					}else if($env=='live'){
						$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_webhook_id_live"] = $result['webhook_id'];
					}
				}else{
					$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_current_webhook_status"] = 'no';
					$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks"] = '';
			        if($env=='sandbox'){
						$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_webhook_id_sandbox"] = '';
					}else if($env=='live'){
						$_POST["woocommerce_advanced-paypal-payments-for-woocommerce_webhook_id_live"] = '';
					}
					setting_new_helper()->set( 'current_webhook_status', 'no' );
					if($result['is_validate']){
						add_action(
							'admin_notices',
							function () {
								printf('<div class="notice notice-error"><p>'.__( 'Subscribed failed: Not a valid webhook URL.', 'advanced-paypal-payments-for-woocommerce' ).'</p></div>');
							}
						);
					}
				}
			}
			parent::process_admin_options();
		}
		
		/**
		 * Get the transaction URL.
		 *
		 * @param  WC_Order $order Order object.
		 * @return string
		 */
		public function get_transaction_url( $order ) {
			$capture_id=get_post_meta($order->get_id(),'_capture_id', true);
			$settings_array = setting_helper()->settings_array;			
			if( isset( $settings_array['env'] ) && 'sandbox' === $settings_array['env'])
			{
				$this->view_transaction_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id='.$capture_id;
			} else {
				$this->view_transaction_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id='.$capture_id;
			}
			return parent::get_transaction_url( $order );
		}
	}
	/**
	* add gateway for woocommerce 
	*/
	function wcapp_payment_add_to_gateways( $gateways ) {
		$gateways[] = 'WCAPP_PayPal_Payment_Gateway';
		return $gateways;
	}
	add_filter( 'woocommerce_payment_gateways', 'wcapp_payment_add_to_gateways', 10, 1 );
}