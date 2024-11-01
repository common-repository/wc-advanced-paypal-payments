<?php
/*
Plugin Name: Advanced PayPal Payments for WooCommerce
Description: Very reliable checkout solution with quick & professional support by PayPal certificated Partner.
Version: 3.2.4
Author URI:  https://paypal.uin88.com/
Author: PNX Software Co., Ltd.
Text Domain: advanced-paypal-payments-for-woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( !defined('WCAPP_PLUGIN_ID') ) {
   define("WCAPP_PLUGIN_ID", "advanced-paypal-payments-for-woocommerce");// PayPal Plugin ID
}
if ( !defined('WCAPP_BN_Code') ) {
   define("WCAPP_BN_Code", "PNX_SI");// BN_Code
}
if ( !defined('WCAPP_WEBHOOK_URL') ) {
   define("WCAPP_WEBHOOK_URL", 'wp-json/wcapp/v1/webhook');// WEBHOOK_URL
}
if ( !defined('WCAPP_PLUGIN_DIR_PATH') ) {
   define("WCAPP_PLUGIN_DIR_PATH", plugin_dir_path( __FILE__ ));// PLUGIN_DIR_PATH
}
if ( !defined('WCAPP_PLUGIN_URL') ) {
   define("WCAPP_PLUGIN_URL", plugins_url( '/', __FILE__ ));// PLUGIN_URL
}

/**
* init function
*/
add_action( 'init', 'wcapp_payment_gateway_init' );
if( !function_exists('wcapp_payment_gateway_init') ){
    function wcapp_payment_gateway_init() {
        if( !class_exists('WC_Payment_Gateway') )  return;
		/**
		* include class.
		*/
		if ( is_woocommerce_activated() ) {
            require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-payment-gateway.php';
		    require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-paypal.php';
		    require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-settings.php';
			require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-logger.php';
			require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-cart.php';
			require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-checkout.php';
			require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-ajax.php';
			require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-webhook.php';
			require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-seller-protection.php';
			require_once WCAPP_PLUGIN_DIR_PATH . 'includes/class-wcapp-new-settings.php';
		}
    }
}

/**
* plugins loaded function
*/
add_action( 'plugins_loaded', 'wcapp_plugins_loaded_function', 20 );
function wcapp_plugins_loaded_function() {
	load_plugin_textdomain( 'advanced-paypal-payments-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	if ( ! is_woocommerce_activated() ) {
		add_action(
			'admin_notices',
			function() {
				echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Advanced PayPal Payments for WooCommerce requires WooCommerce to be installed and active. You can download %s here.', 'advanced-paypal-payments-for-woocommerce' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
			}
		);

		return;
	}
}

/**
* plugin setting links
*/
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'wcapp_action_links' );
function wcapp_action_links( $links ) {
	$plugin_links = array();
	$plugin_links[] = '<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout&section=advanced-paypal-payments-for-woocommerce').'">'.esc_html__('Settings', 'advanced-paypal-payments-for-woocommerce').'</a>';
	return array_merge( $plugin_links, $links );
}

/**
* plugin row meta
*/
add_filter(
	'plugin_row_meta',
	function( $links, $file ) {
		if ( plugin_basename( __FILE__ ) !== $file ) {
			return $links;
		}
        if(get_bloginfo("language")=='zh-CN'){
			return array_merge(
				$links,
				array(
					sprintf(
						'<a target="_blank" href="%1$s">%2$s</a>',
						esc_url('https://paypal.uin88.com/zh/'),
						esc_html__( 'Document', 'advanced-paypal-payments-for-woocommerce' )
					),
					sprintf(
						'<a target="_blank" href="%1$s">%2$s</a>',
						esc_url('https://paypal.uin88.com/zh/support-zh/'),
						esc_html__( 'Support', 'advanced-paypal-payments-for-woocommerce' )
					),
					sprintf(
						'<a target="_blank" href="%1$s">%2$s</a>',
						esc_url('https://paypal.uin88.com/zh/support-zh/bug-reports/'),
						esc_html__( 'Report a bug', 'advanced-paypal-payments-for-woocommerce' )
					),
					sprintf(
						'<a target="_blank" href="%1$s">%2$s</a>',
						esc_url('https://paypal.uin88.com/zh/about-us/'),
						esc_html__( 'About developer', 'advanced-paypal-payments-for-woocommerce' )
					)
				)
			);
		}else{
			return array_merge(
				$links,
				array(
					sprintf(
						'<a target="_blank" href="%1$s">%2$s</a>',
						esc_url('https://paypal.uin88.com/'),
						esc_html__( 'Document', 'advanced-paypal-payments-for-woocommerce' )
					),
					sprintf(
						'<a target="_blank" href="%1$s">%2$s</a>',
						esc_url('https://paypal.uin88.com/support/'),
						esc_html__( 'Support', 'advanced-paypal-payments-for-woocommerce' )
					),
					sprintf(
						'<a target="_blank" href="%1$s">%2$s</a>',
						esc_url('https://paypal.uin88.com/support/bug-reports/'),
						esc_html__( 'Report a bug', 'advanced-paypal-payments-for-woocommerce' )
					),
					sprintf(
						'<a target="_blank" href="%1$s">%2$s</a>',
						esc_url('https://paypal.uin88.com/about-us/'),
						esc_html__( 'About developer', 'advanced-paypal-payments-for-woocommerce' )
					)
				)
			);
		}
	},
	10,
	2
);

if ( ! function_exists( 'wcapp_enqueue_paypal_sdk_function' ) ) {
	function wcapp_enqueue_paypal_sdk_function() {
		$settings_array = setting_helper()->settings_array;
		if( 'yes' === $settings_array['enabled'] ) {
			if( isset( $settings_array['on_single_product_page'] ) && 'yes' === $settings_array['on_single_product_page'] || isset( $settings_array['on_cart_page'] ) && 'yes' === $settings_array['on_cart_page'] || isset( $settings_array['on_checkout'] ) && 'yes' === $settings_array['on_checkout']) {
				if( is_product() || is_cart() || is_checkout() || is_checkout_pay_page() ) {
					$env = $settings_array['env'];
					$clientid='';
					if($env=='sandbox'){
					   $clientid=$settings_array['client_id_sandbox'];
					}else{
					   $clientid=$settings_array['client_id_production'];
					}
                    $locale=get_locale();
					if(strtolower($locale)=='ja'){
						$locale='ja_JP';
					}else{
						str_replace('-','_',$locale);
					}
					
					$advanced_card_processing=$settings_array['advanced_card_processing'];
					$apple_pay=$settings_array['apple_pay_checked'];
					$google_pay=$settings_array['google_pay_checked'];
					$components='buttons,funding-eligibility,messages,marks';
					if($advanced_card_processing=='yes')
						$components.=',card-fields';
					if($apple_pay=='yes')
						$components.=',applepay';	
					if($google_pay=='yes')
						$components.=',googlepay';	
					$script_args = array(
						'client-id'   => $clientid,
						'intent'      => strtolower($settings_array['payment_action']),
						'locale'      => str_replace('-','_',$locale),
						'components'  => $components,
						'commit'      => 'true',
						'currency'    => get_woocommerce_currency(),
						'enable-funding' => $funding,
					);
					if (is_cart() || is_product() ) {
						$script_args['disable-funding']='card,venmo,paylater';
					}
					if( is_checkout() || is_checkout_pay_page() ) {
						$card=$settings_array['card'];
						$venmo=$settings_array['venmo'];
						$paylater=$settings_array['paylater'];
						$funding='';
						$disable_funding='';
						if($card=='yes')
							$funding.='card,';
						else if($advanced_card_processing!='yes')
							$disable_funding.='card,';
						if($venmo=='yes')
							$funding.='venmo,';
						else
							$disable_funding.='venmo,';
						if($paylater=='yes')
							$funding.='paylater,';
						else
							$disable_funding.='paylater,';
						if($funding!=''){
							$script_args['enable-funding']=rtrim($funding,',');
						}
						if($disable_funding!=''){
							$script_args['disable-funding']=rtrim($disable_funding,',');
						}
					}
					wp_enqueue_script( 'paypal-checkout-sdk', add_query_arg( $script_args, 'https://www.paypal.com/sdk/js' ), array(), null, true );
					if(!empty(constant("WCAPP_BN_Code"))) {
						add_filter( 'script_loader_tag', 'wcapp_add_paypal_sdk_namespace_attribute', 10, 3 );
					}
					if($google_pay=='yes'){
					    //add_filter('script_loader_tag', 'wcapp_add_async_attribute_to_google_pay', 10, 2);
					}
					if( is_checkout_pay_page() ) {
						wp_enqueue_script( 'wcapp-payment-button', WCAPP_PLUGIN_URL.'assets/js/wcapp-order-pay-payment-buttons.js', array(), '1.0', true );
					}else{
						wp_enqueue_script( 'wcapp-payment-button', WCAPP_PLUGIN_URL.'assets/js/wcapp-payment-buttons.js', array(), '1.0', true );

					}
					if($advanced_card_processing=='yes'){
						wp_enqueue_script( 'wcapp-payment-card-button', WCAPP_PLUGIN_URL.'assets/js/wcapp-payment-card-buttons.js', array(), '1.3', true );
					}
					if($apple_pay=='yes'){
						wp_enqueue_script( 'wcapp_apple_pay_sdk', 'https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js', array(), '1.0', true );
						wp_enqueue_script( 'wcapp-apple-pay-js', WCAPP_PLUGIN_URL.'assets/js/wcapp-apple-pay.js', array(), '2.0', true );
					}
					if($google_pay=='yes'){
                        wp_enqueue_script( 'wcapp_google_pay_sdk', 'https://pay.google.com/gp/p/js/pay.js', array(), '1.0', true );
						wp_enqueue_script( 'wcapp-google-pay-js', WCAPP_PLUGIN_URL.'assets/js/wcapp-google-pay.js', array(), '1.2', true );
					}
					$page='';
					if( is_product() || is_cart()){
						if( is_product() ){
							$page='product';
						}else if( is_cart() ){
							$page='cart';
						}
						$array = array(
							'on_checkout' => $settings_array['on_checkout'],
							'button_label' => $settings_array['button_label'],
							'button_cc_icons' => $settings_array['button_cc_icons'],
							'button_size' => $settings_array['button_size'],
							'button_style' => $settings_array['button_style'],
							'button_color' => $settings_array['button_color'],
							'page'      => $page,
							'direct_checkout_url'      => home_url().'/checkout/?payment=PayPal'
						);
						wp_localize_script( 'wcapp-payment-button', 'wc_wcapp_context_checkout', $array );
					}				
				}
			}
		}
	}
}

/**
 * Check if cart product price total is 0.
 * @return bool true if is 0, otherwise false.
 */
if ( ! function_exists( 'wcapp_is_cart_price_total_zero' ) ) {
	function wcapp_is_cart_price_total_zero(): bool {
		return WC()->cart && WC()->cart->get_total( 'numeric' ) == 0;
	}
}

/**
* Display paypal button on the checkout page.
*/
add_action( 'woocommerce_pay_order_before_submit', 'wcapp_display_paypal_button_on_checkout' );
add_action( 'woocommerce_review_order_after_payment', 'wcapp_display_paypal_button_on_checkout');
function wcapp_display_paypal_button_on_checkout() {
    $settings_array = setting_helper()->settings_array;
    if(isset( $settings_array['on_checkout'] ) && 'yes' === $settings_array['on_checkout']) {
        wcapp_enqueue_paypal_sdk_function();
		if( is_checkout_pay_page() ) {
			global $wp;
			$order_id = $wp->query_vars['order-pay'];
            $order = wc_get_order( $order_id );	
            $is_total_zero=1;
            if( $order->get_total()>0 ) {
               $is_total_zero=0;
			}				
			$array = array(
				'on_checkout' => $settings_array['on_checkout'],
				'button_label' => $settings_array['button_label'],
				'button_cc_icons' => $settings_array['button_cc_icons'],
				'button_size' => $settings_array['button_size'],
				'button_style' => $settings_array['button_style'],
				'button_color' => $settings_array['button_color'],
				'advanced_card_processing' => $settings_array['advanced_card_processing'],
				'create_order_for_order_pay_nonce' => wp_create_nonce( '_wc_wcapp_create_order_for_order_pay_nonce' ),
				'ajaxurl'        => WC_AJAX::get_endpoint( 'wcapp_create_order_for_order_pay' ),
				'create_order_nonce' => wp_create_nonce( '_wc_wcapp_create_order_nonce' ),
				'capture_url'        => WC_AJAX::get_endpoint( 'wcapp_capture' ),
				'capture_nonce'        => wp_create_nonce( '_wc_wcapp_capture_nonce' ),
				'total_url'        => WC_AJAX::get_endpoint( 'wcapp_order_total' ),
				'total_nonce'        => wp_create_nonce( '_wc_wcapp_order_total_nonce' ),
				'blogname'        => get_option( 'blogname' ),
				'currency'        => get_woocommerce_currency(),
				'is_checkout_pay_page' => '1',
				'key'        => $order->get_order_key(),
				'is_total_zero' => $is_total_zero,
				'env' => $settings_array['env'],
				'order_total' => $order->get_total()
			);
			wp_localize_script( 'wcapp-payment-button', 'wc_wcapp_context_checkout', $array );
		}else{
			$array = array(
				'on_checkout' => $settings_array['on_checkout'],
				'button_label' => $settings_array['button_label'],
				'button_cc_icons' => $settings_array['button_cc_icons'],
				'button_size' => $settings_array['button_size'],
				'button_style' => $settings_array['button_style'],
				'button_color' => $settings_array['button_color'],
				'advanced_card_processing' => $settings_array['advanced_card_processing'],
				'create_order_nonce' => wp_create_nonce( '_wc_wcapp_create_order_nonce' ),
				'validate_nonce' => wp_create_nonce( '_wc_wcapp_validate_nonce' ),
				'ajaxurl'        => WC_AJAX::get_endpoint( 'wcapp_create_order' ),
				'capture_url'        => WC_AJAX::get_endpoint( 'wcapp_capture' ),
				'capture_nonce'        => wp_create_nonce( '_wc_wcapp_capture_nonce' ),
				'validate_url'   => WC_AJAX::get_endpoint( 'wcapp_validate' ),
				'total_url'        => WC_AJAX::get_endpoint( 'wcapp_order_total' ),
				'total_nonce'        => wp_create_nonce( '_wc_wcapp_order_total_nonce' ),
				'blogname'        => get_option( 'blogname' ),
				'currency'        => get_woocommerce_currency(),
				'is_checkout_pay_page' => '0',
				'page'        => 'checkout',
				'is_total_zero' => wcapp_is_cart_price_total_zero(),
				'env' => $settings_array['env'],
				'cart_total' => WC()->cart->get_total( 'raw' )
			);
			wp_localize_script( 'wcapp-payment-button', 'wc_wcapp_context_checkout', $array );
		}
	}
	?>
	<div class="wcapp_paypal_button_wrapper" style="position:static;zoom:1;margin-top:30px;margin-bottom:30px">
		<div id="woo_wcapp_paypal_button"></div>
		<?php if($settings_array['apple_pay_checked']=="yes"){ //如果后面勾选“applepay”选项，才会出现下面的html ?>
		<div id="wcapp_applepay_container"></div>
		<style>
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
		</style>
		<?php } ?>
		<?php if($settings_array['google_pay_checked']=="yes"){ //如果后面勾选“applepay”选项，才会出现下面的html ?>
		<div id="wcapp_googlepay_container"></div>
		<!-- Result Modal -->
        <div id="resultModal" class="modal" style="display:none;">
            <span onclick="document.getElementById('resultModal').style.display='none'" class="close" title="Close Modal">&times;</span>
			<form class="modal-content">
			  <div class="modalContainer">
				<span class="modalHeader">Capture Order Result</span>
				<pre id=result class=json-container></pre>
			  </div>
			</form>
        </div>
		<style>
		.gpay-card-info-container {max-width:750px;width:100%;margin-top:6px;}
		</style>
		<?php } ?>
		<?php if($settings_array['advanced_card_processing']=="yes"){ //如果后面勾选“高级信用卡”选项，才会出现下面的html ?>
		<!-- Containers for Card Fields hosted by PayPal -->
		<div id="wcapp-card-form" class="wcapp_card_container">
		  <div class="line-or"><span>or</span></div>
		  <!--<div id="card-name-field-container"></div>-->
		  <div id="wcapp-card-number-field-container"></div>
		  <div id="wcapp-card-expiry-field-container"></div>
		  <div id="wcapp-card-cvv-field-container"></div>
		  <button id="wcapp-card-field-submit-button" type="button">Pay now with Card</button>
		</div>
		<p id="wcapp-result-message"></p>
		<style>
		.wcapp_card_container{display:none;max-width:750px;width:100%;}
		#wcapp-card-field-submit-button{color:#fff;background-color:#189CD7;width:calc(100% - 12px);margin:0.5em 6px;border-radius:4px;height:40px;line-height:40px;padding:0;border: none;font-weight: bold;cursor: pointer;}
		#wcapp-card-field-submit-button:hover{background-color:#29ade8;}
		#wcapp-result-message{font-size:0.9em;color:red;margin:0.5em 6px;}
		.line-or{text-align:center;margin:0.5em auto;}
		.line-or span{color:#aaa;font-size:1em;position:relative;}
		.line-or span:before{content:"";border-bottom:1px solid #ddd;width:100px;position:absolute;left: -105px;top: 0.7em;}
		.line-or span:after{content:"";border-bottom:1px solid #ddd;width:100px;position:absolute;right: -105px;top: 0.7em;}
		</style>
		<?php } ?>
	</div>
    <?php
    $payment=sanitize_text_field($_GET['payment']);
	if( $payment=='PayPal' ){
		echo '
		<script type="text/javascript">
			jQuery(document).ready(function(){
				function setPaymentMethod(){
					jQuery(".wc_payment_method").each(function() {
						if(jQuery( this ).hasClass( "payment_method_advanced-paypal-payments-for-woocommerce" )){
							jQuery("#payment_method_advanced-paypal-payments-for-woocommerce").click();
						}else{
							jQuery( this ).remove();
						}
					});
				}
				setTimeout(function(){ 
					setPaymentMethod();
				}, 1000);
			});	
		</script>';
	}
}

/**
 * Function for `woocommerce_order_refunded` action-hook.
 * 
 * @param  $order_id  
 * @param  $refund_id 
 *
 * @return void
 */
add_action( 'woocommerce_order_refunded', 'wcapp_order_refunded_function', 10, 2 );
function wcapp_order_refunded_function( $order_id, $refund_id ){
	$order = wc_get_order( $order_id );
	//$refunded_payment=get_post_meta($refund_id,'_refunded_payment', true);
	$refund_reason=get_post_meta($refund_id,'_refund_reason', true);
	$refund_amount=get_post_meta($refund_id,'_refund_amount', true);
	$order_currency=get_post_meta($refund_id,'_order_currency', true);
	if( $refund_reason == 'PayPal webhook callback. Refund initialized from your PayPal account.') {
		return;
	}
	logger_helper()->log('info', 'order_refunded: order_id：'.$order_id.' refund_id：'.$refund_id.' refund_reason：'.$refund_reason);
    $capture_id=get_post_meta( $order->get_id(), '_capture_id', true );
	$invoice_id=get_post_meta( $order->get_id(), '_invoice_id', true );
	$amount=get_post_meta( $order->get_id(), '_order_total', true );
	$currency=get_post_meta( $order->get_id(), '_order_currency', true );
	$payment_method=get_post_meta( $order->get_id(), '_payment_method', true );

    if( $payment_method == WCAPP_PLUGIN_ID )
	{
		$refundData = '{
					"amount":{
						"value":"'.$refund_amount.'",
						"currency_code":"'.$order_currency.'"
					},
					"invoice_id":"'.$invoice_id.'",
					"note_to_payer":"DefectiveProduct"
				}';	
		logger_helper()->log('info', 'order refund: order_id：'.$order_id.' capture_id：'.$capture_id.' refund_id：'.$refund_id.' refundData：'.$refundData);

		$paypalHelper = new WCAPP_PayPal();
		$result=$paypalHelper->capturesRefund($refundData,$capture_id);

		if(empty($result['data']['id'])){
			$order->add_order_note(
				sprintf(
					__( 'PayPal has failed to refund. Refunded amount：%s. Please manually refund in PayPal website.', 'advanced-paypal-payments-for-woocommerce' ),
					$refund_amount
				)
			);
		}else{
			update_post_meta( $order_id, '_manual_refunded', true );
			$order->add_order_note(
				sprintf(
					__( 'PayPal has successfully refunded. Refunded amount：%s', 'advanced-paypal-payments-for-woocommerce' ),
					$refund_amount
				)
			);
		}
	}
}

/**
* create webhook for paypal
* @param string   $env
* @param string   $client_id
* @param string   $client_secret
* @return array
*/
if ( ! function_exists( 'wcapp_webhook_create_function' ) ) { 
	function wcapp_webhook_create_function($env, $client_id, $client_secret){
		$paypalHelper = new WCAPP_PayPal($env);
		logger_helper()->log('info', 'webhook list request：'.$client_id.' '.$client_secret);
		$webhook_id=$paypalHelper->webhookList($client_id, $client_secret);
		if($webhook_id=='error'){
			return array(
				"is_validate" => false,
				"is_webhook" => false,
				"webhook_message" => 'validate failed'
			);
		}
		
		$is_delete=true;
		if($webhook_id!=''){
			logger_helper()->log('info', 'webhook delete request：'.$webhook_id);
			$is_delete=$paypalHelper->webhookDelete($webhook_id, $client_id, $client_secret);
		}
		if($is_delete){
			$url=home_url(constant("WCAPP_WEBHOOK_URL"));
			$data = '{
					  "url": "'.$url.'",
					  "event_types": [
						{
						  "name": "PAYMENT.CAPTURE.COMPLETED"
						},
						{
						  "name": "PAYMENT.CAPTURE.DENIED"
						},
						{
						  "name": "PAYMENT.CAPTURE.PENDING"
						},
						{
						  "name": "PAYMENT.CAPTURE.REFUNDED"
						},
						{
						  "name": "PAYMENT.CAPTURE.REVERSED"
						},
						{
						  "name": "PAYMENT.ORDER.CANCELLED"
						}
					  ]
					}';	
			logger_helper()->log('info', 'webhook create request：'.$data);
		
			return $paypalHelper->webhookCreate($data, $client_id, $client_secret);
		}
		return array(
			"is_validate" => true,
			"is_webhook" => false,
			"webhook_message" => 'created failed'
			
		);
	}
}

/**
* add BN_Code to paypal sdk namespace
*/
if ( ! function_exists( 'wcapp_add_paypal_sdk_namespace_attribute' ) ) { 
	function wcapp_add_paypal_sdk_namespace_attribute( $tag, $handle ) {
		if ( 'paypal-checkout-sdk' === $handle ) {
			if(wcapp_get_bn_code()!=""){
			    $tag = str_replace( ' src=', ' data-partner-attribution-id=\''.constant("WCAPP_BN_Code").'\' src=', $tag );
		    }
		}
		return $tag;
	}
}
/**
* add onload to googlepay sdk namespace
*/
if ( ! function_exists( 'wcapp_add_async_attribute_to_google_pay' ) ) {
	function wcapp_add_async_attribute_to_google_pay($tag, $handle) {
		// 仅为指定的脚本句柄添加 async 属性
		if ('wcapp_google_pay_sdk' === $handle) {
			// 添加 async 和 onload 属性
			return str_replace('<script ', '<script async onload="onGooglePayLoaded()" ', $tag);
		}
		return $tag;
	}
}
/**
* add head section to plugin configuration page
*/
add_action( 'woocommerce_sections_checkout', function() {
	$section = sanitize_text_field($_GET['section']);
	if($section === WCAPP_PLUGIN_ID) {
		if(get_bloginfo("language")=='zh-CN'){
			echo '
				<div class="settings-page-header">
					<img alt="PayPal" src="' . WCAPP_PLUGIN_URL . 'assets/images/paypal.png"/>
					<h4> <span class="inline-only">-</span> ' . __( 'Very reliable checkout solution with quick support by <c style="color:#169BD7;">PayPal certificated Partner</c>', 'advanced-paypal-payments-for-woocommerce' ) . '</h4>
					<a class="button" target="_blank" href="https://paypal.uin88.com/zh/">'
						. esc_html__( 'Document', 'advanced-paypal-payments-for-woocommerce' ) .
					'</a>
					<a class="button" target="_blank" href="https://paypal.uin88.com/zh/support-zh/">'
						. esc_html__( 'Support', 'advanced-paypal-payments-for-woocommerce' ) .
					'</a>
					<span class="right-align">
						<a target="_blank" href="https://paypal.uin88.com/zh/support-zh/bug-reports/">'
							. esc_html__( 'Report a bug', 'advanced-paypal-payments-for-woocommerce' ) .
						'</a>
						<a target="_blank" href="https://paypal.uin88.com/zh/about-us/">'
							. esc_html__( 'About developer', 'advanced-paypal-payments-for-woocommerce' ) .
						'</a>
					</span>
				</div>
			';
		}else{
			echo '
				<div class="settings-page-header">
					<img alt="PayPal" src="' . WCAPP_PLUGIN_URL . 'assets/images/paypal.png"/>
					<h4> <span class="inline-only">-</span> ' . __( 'Very reliable checkout solution with quick support by <c style="color:#169BD7;">PayPal certificated Partner</c>', 'advanced-paypal-payments-for-woocommerce' ) . '</h4>
					<a class="button" target="_blank" href="https://paypal.uin88.com/">'
						. esc_html__( 'Document', 'advanced-paypal-payments-for-woocommerce' ) .
					'</a>
					<a class="button" target="_blank" href="https://paypal.uin88.com/support/">'
						. esc_html__( 'Support', 'advanced-paypal-payments-for-woocommerce' ) .
					'</a>
					<span class="right-align">
						<a target="_blank" href="https://paypal.uin88.com/support/bug-reports/">'
							. esc_html__( 'Report a bug', 'advanced-paypal-payments-for-woocommerce' ) .
						'</a>
						<a target="_blank" href="https://paypal.uin88.com/about-us/">'
							. esc_html__( 'About developer', 'advanced-paypal-payments-for-woocommerce' ) .
						'</a>
					</span>
				</div>
			';
		}
	}
});

/**
 * Check if WooCommerce is active.
 * @return bool true if WooCommerce is active, otherwise false.
 */
if ( ! function_exists( 'is_woocommerce_activated' ) ) { 
	function is_woocommerce_activated(): bool {
		return class_exists( 'woocommerce' );
	}
}

/**
* get BN_Code
*/
if ( ! function_exists( 'wcapp_get_bn_code' ) ) { 
	function wcapp_get_bn_code() {
		$WCAPP_BN_Code=constant("WCAPP_BN_Code");
		$settings_array = setting_helper()->settings_array;
		if( 'yes' === $settings_array['enabled'] ) {
			$merchant_email_production=$settings_array['merchant_email_production'];
			if(!empty($merchant_email_production)){
				$file=WCAPP_PLUGIN_DIR_PATH.'assets'.DIRECTORY_SEPARATOR.'acc.txt';
				if (!file_exists($file))
				    return '';
				$handle = fopen($file, 'r');
				if ($handle) {
					while (($line = fgets($handle)) !== false) {
						if(strtolower(md5($merchant_email_production))==$line){
							fclose($handle);
							return '';
						}
					}
					fclose($handle);
				}
			}
	    }
		return $WCAPP_BN_Code;
	}
}

//add_action( 'woocommerce_cart_actions', 'wcapp_update_cart' );
//function wcapp_update_cart() { 
    //echo 'hello'; 
//}
