<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_Seller_Protection' ) ) {
	
	/**
	 * WCAPP Seller Protection Class.
	 */
	class WCAPP_Seller_Protection {
		
		/**
		 * constructor.
		 *
		 */
		public function __construct() {
			add_action( 'woocommerce_admin_order_totals_after_total', array( $this, 'wcapp_woocommerce_admin_order_totals_after_total_action' ) );//woocommerce_admin_order_totals_after_refunded
			add_action( 'add_meta_boxes', array( $this, 'wcapp_add_meta_box_function' ), 10, 2 );
			add_action( 'wp_ajax_wcapp_shipment_tracking_save_form', array( $this, 'wcapp_save_meta_box_ajax_function' ) );
			add_action( 'woocommerce_admin_order_data_after_payment_info', array( $this, 'wcapp_order_data_after_payment_info') );
		}
		
		/**
		 * Function for `woocommerce_admin_order_data_after_payment_info` action-hook.
		 * 
		 * @param $order $order WC_Order The order object being displayed.
		 *
		 * @return void
		 */
		public function wcapp_order_data_after_payment_info( $order ){
			$order_id = $order->get_id();
			$payment_source=get_post_meta( $order_id, '_payment_source', true );
			if( $payment_source!='' ){
				if($payment_source=='card'){
			        echo '<div style="margin-top:8px;">'.__( 'Payment Source', 'advanced-paypal-payments-for-woocommerce' ).': Card</div>';
				}
				if($payment_source=='apple_pay'){
			        echo '<div style="margin-top:8px;">'.__( 'Payment Source', 'advanced-paypal-payments-for-woocommerce' ).': Apple Pay</div>';
				}
				if($payment_source=='google_pay'){
			        echo '<div style="margin-top:8px;">'.__( 'Payment Source', 'advanced-paypal-payments-for-woocommerce' ).': Google Pay</div>';
				}
			}
		}
		
		/**
		 * Function for `woocommerce_admin_order_totals_after_total` action-hook.
		 * 
		 * @param  $order 
		 *
		 * @return void
		 */
		public function wcapp_woocommerce_admin_order_totals_after_total_action( $order_id ) {
			$order = wc_get_order( $order_id );
			$payment_method=get_post_meta( $order->get_id(), '_payment_method', true );
			$settings_array = setting_helper()->settings_array;
			if( isset( $settings_array['seller_protection'] ) && 'yes' === $settings_array['seller_protection'] && $payment_method == WCAPP_PLUGIN_ID ) {
				$seller_protection_status=get_post_meta( $order_id, '_seller_protection_status', true );
				if( !empty($seller_protection_status) ) {
					echo '<table class="wc-order-totals" style="border-top: 1px solid #999; margin-top:12px; padding-top:12px">
								<tbody>
									<tr>
										<td class="label label-highlight">'.__( 'PayPal seller protection', 'advanced-paypal-payments-for-woocommerce' ).':</td>
										<td width="1%"></td>
										<td class="total"><span class="seller_protection_status_'.esc_attr(strtolower($seller_protection_status)).'">'.esc_html__( $seller_protection_status, 'advanced-paypal-payments-for-woocommerce' ).'</span></td>
									</tr>
								</tbody>
						  </table>';
				}
			}
		}

		/**
		 * Add the meta box for shipment info on the order page
		 */
		public function wcapp_add_meta_box_function($post_type, $post) {
			$order = ( $post instanceof WP_Post ) ? wc_get_order( $post->ID ) : $post;
			if($order != false && $order->get_status()=='pending'){
				return;
			}
			$settings_array = setting_helper()->settings_array;
			if( isset($settings_array['is_third_party_mode']) && 'yes' === $settings_array['is_third_party_mode'] ){
				return;
			}
			
			$screen = 'shop_order';
			add_meta_box( 'advanced-paypal-payments-for-woocommerce-seller-protection', __( 'PayPal shipment tracking', 'advanced-paypal-payments-for-woocommerce' ), array( $this, 'wcapp_meta_box_content' ), $screen, 'side', 'high' );
		}

		/**
		 * Show the meta box for shipment info on the order page
		 */
		public function wcapp_meta_box_content( $post_or_order_object ) {
			global $wpdb;
			$order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
			$order_id = $order->get_id();
			$payment_method=get_post_meta( $order_id, '_payment_method', true );
			$seller_protection_status=get_post_meta( $order_id, '_seller_protection_status', true );
            $settings_array = setting_helper()->settings_array;
			
			if( isset( $settings_array['seller_protection'] ) && 'yes' === $settings_array['seller_protection'] && $payment_method == WCAPP_PLUGIN_ID && $seller_protection_status != 'NOT_ELIGIBLE') {
				$order_status = $order->get_status();
				$WC_Countries = new WC_Countries();
				$countries = $WC_Countries->get_countries();
				$carriers = include 'Settings/wcapp-seller-protection-carriers.php';
				
				echo '<div id="seller_protection_form">'; 
				$tracking_number=get_post_meta( $order_id, '_tracking_number', true );
				$tracking_provider=get_post_meta( $order_id, '_tracking_provider', true );
				$shipment_status=get_post_meta( $order_id, '_shipment_status', true );
				?>
				<p class="form-field tracking_number_field ">
					<label for="tracking_number"><?php esc_html_e( 'Tracking number:', 'advanced-paypal-payments-for-woocommerce' ); ?></label>
					<input type="text" class="short" style="" name="wcapp_tracking_number" id="wcapp_tracking_number" value="<?php echo esc_attr($tracking_number);?>" autocomplete="off"> 
				</p>
				<?php								
				echo '<p class="form-field tracking_provider_field"><label for="wcapp_tracking_provider">' . esc_html__( 'Shipping Provider:', 'advanced-paypal-payments-for-woocommerce' ) . '</label><br/><select id="wcapp_tracking_provider" name="wcapp_tracking_provider" class="chosen_select tracking_provider_dropdown" style="width:100%;">';	
						
				echo '<option value="">' . esc_html__( 'Select Provider', 'advanced-paypal-payments-for-woocommerce' ) . '</option>';
				foreach ( $carriers as $key => $value ) {
					echo '<optgroup label="'.esc_attr($key).'">';
					foreach($value as $item)
					{
						$selected = ( $item[1] == $tracking_provider ) ? 'selected' : '';
						echo '<option value="' . esc_attr($item[1]) . '" ' . esc_attr( $selected ) . '>' . esc_html( $item[0] ) . '</option>';
					}
					echo '</optgroup>';
				}
				echo '</select> ';
				
				$selected = ( $shipment_status == 'DELIVERED' ) ? 'checked="true"' : '';
				echo '<fieldset class="form-field change_order_to_shipped_field">
					<span></span>
					<ul class="wc-radios">
						<li>
							<label>
								<input id="wcapp_shipment_status" type="checkbox" class="select short mark_shipped_checkbox" '.esc_attr($selected).'">' . esc_html__( 'Already delivered to customer', 'advanced-paypal-payments-for-woocommerce' ) . '</label>
						</li>
					</ul>
					<div style="margin-bottom: 10px;">'.__( 'Send tracking information to PayPal', 'advanced-paypal-payments-for-woocommerce' ).'</div>
				</fieldset>';

				woocommerce_wp_hidden_input( array(
					'id'    => 'wcapp_shipment_tracking_create_nonce',
					'value' => wp_create_nonce( 'create-tracking-item' ),
				) );
				
				if ( 'auto-draft' != $order_status ) {
					echo '<button id="save_tracking_btn" type="button" class="button button-primary btn_ast2 button-save-form">' . esc_html__( 'Save Tracking', 'advanced-paypal-payments-for-woocommerce' ) . '</button>';
				}
						
				echo '</div>';
				?>
				<script>
				jQuery( function( $ ) {
					jQuery(document).on("click", "#save_tracking_btn", function(){	
						
						if ( jQuery( '#wcapp_tracking_number' ).val() === '' ) {
							jQuery( '#wcapp_tracking_number' ).css("border-color","red");
							return;
						}else{
							jQuery( '#wcapp_tracking_number' ).css("border-color","");
						}
						if ( jQuery( '#wcapp_tracking_provider' ).val() === '' ) {
							jQuery("#wcapp_tracking_provider").siblings('.select2-container').find('.select2-selection').css('border-color','red');
							return;
						}else{
							jQuery("#wcapp_tracking_provider").siblings('.select2-container').find('.select2-selection').css('border-color','#ddd');
						}
						
						jQuery( '#seller_protection_form' ).block( {
							message: null,
							overlayCSS: {
								background: '#fff',
								opacity: 0.6
							}
						} );
						
						var shipment_status="";
						if(jQuery( '#wcapp_shipment_status' ).prop('checked')) {
							shipment_status="1";
						}
						
						var data = {
							action:                   'wcapp_shipment_tracking_save_form',
							order_id:                 <?php echo esc_html($order_id);?>,//woocommerce_admin_meta_boxes.post_id,
							tracking_provider:        jQuery( '#wcapp_tracking_provider' ).val(),
							tracking_number:          jQuery( '#wcapp_tracking_number' ).val(),
							shipment_status:          shipment_status,
							security:                 jQuery( '#wcapp_shipment_tracking_create_nonce' ).val()
						};
						
						jQuery.ajax({
							url: woocommerce_admin_meta_boxes.ajax_url,		
							data: data,
							type: 'POST',				
							success: function(response) {
								if(response==1)	{						
									$( '#seller_protection_form' ).unblock();

									if ( response == 'reload' ) {
										location.reload(true);
										return false;
									}
									
									//jQuery('#order_status').val('wc-completed');
									jQuery('#order_status').select2().trigger('change');
									jQuery('#post').before('<div id="order_updated_message" class="updated notice notice-success is-dismissible"><p>'+"<?php esc_html_e( 'Order updated.', 'advanced-paypal-payments-for-woocommerce' ); ?>"+'</p><button type="button" class="notice-dismiss update-dismiss"><span class="screen-reader-text">'+"<?php esc_html_e( 'Dismiss this notice.', 'advanced-paypal-payments-for-woocommerce' ); ?>"+'</span></button></div>');
									alert("<?php esc_html_e( 'Order updated.', 'advanced-paypal-payments-for-woocommerce' ); ?>");
									window.location=window.location;
								}else{
									alert(response);
									$( '#seller_protection_form' ).unblock();
								}
							},
							error: function(response) {
								console.log(response);			
							}
						});
						
					});
				} );
				</script>
				<?php
			} else {
				if( $payment_method != WCAPP_PLUGIN_ID ) {
				?>
				    <div style="text-align:center;"><?php esc_html_e( 'The order is not paid via PayPal.', 'advanced-paypal-payments-for-woocommerce' ); ?></div>
				<?php
				}
				if( $seller_protection_status === 'NOT_ELIGIBLE' ) {
				?>
				    <div style="text-align:center;"><?php esc_html_e( 'The order is not eligible for seller protection.', 'advanced-paypal-payments-for-woocommerce' ); ?></div>
				<?php
				}
				if( empty( $settings_array['seller_protection'] ) || 'no' === $settings_array['seller_protection'] ) {
				?>
				    <div style="text-align:center;"><?php echo sprintf(
					__( 'PayPal shipment tracking is not enabled. <br><a href="%1$s">Click to enable</a>.', 'advanced-paypal-payments-for-woocommerce' ), esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=advanced-paypal-payments-for-woocommerce') )); ?></div>
				<?php
				}
			}
		}

		/**
		 * Order Tracking Save AJAX
		 *
		 * Function for saving tracking items via AJAX
		 */
		public function wcapp_save_meta_box_ajax_function() {
			
			check_ajax_referer( 'create-tracking-item', 'security', true );
			
			$tracking_provider = isset( $_POST['tracking_provider'] ) ? wc_clean( $_POST['tracking_provider'] ) : '';
			$tracking_number = isset( $_POST['tracking_number'] ) ? wc_clean( $_POST['tracking_number'] ) : '';
			$tracking_number = str_replace( ' ', '', $tracking_number );
            $shipment_status = 'SHIPPED';
            if( isset( $_POST['shipment_status'] ) && sanitize_text_field($_POST['shipment_status']) == 1 )
			{
                $shipment_status = 'DELIVERED';
			}else{
                $shipment_status = 'SHIPPED';
			}				
			
			if ( strlen( $tracking_number ) > 0 && '' != $tracking_provider ) {	

				$order_id = isset( $_POST['order_id'] ) ? wc_clean( $_POST['order_id'] ) : '';
				$order = new WC_Order( $order_id );
				
				$order->update_meta_data( '_tracking_number', $tracking_number );
				$order->update_meta_data( '_tracking_provider', $tracking_provider );
				$order->update_meta_data( '_shipment_status', $shipment_status );
				$order->save();
				
				$transaction_id=get_post_meta( $order->get_id(), '_capture_id', true );
				$result=$this->wcapp_woocommerce_order_trackShipping_function( $transaction_id, $tracking_number, $tracking_provider, $shipment_status );
				
				if( $result ) {
					$note = sprintf( __( 'PayPal shipment tracking: The tracking information has been successfully sent to PayPal. (tracking number is: %1$s, tracking provider is: %2$s,shipped status is: %3$s)', 'advanced-paypal-payments-for-woocommerce' ), $tracking_number, $tracking_provider, $shipment_status );
					
					// Add the note
					$order->add_order_note( $note );	
				}else {
					$note = sprintf( __( 'PayPal shipment tracking: Failed to send tracking information to PayPal. Please resubmit.', 'advanced-paypal-payments-for-woocommerce' ) );
					echo esc_html($note);
					die();
				}
				
				echo '1';
				die();
			}
			die();	
		}
		
		 /**
		 * Function for `woocommerce_order_trackShipping` action-hook.
		 * 
		 * @param  $transaction_id  
		 * @param  $tracking_number 
		 * @param  $tracking_provider 
		 * @param  $status  
		 *
		 * @return void
		 */
		public function wcapp_woocommerce_order_trackShipping_function( $transaction_id, $tracking_number,$tracking_provider, $status ){
			$data = '{"trackers": [{
						"transaction_id":"'.$transaction_id.'",
						"tracking_number":"'.$tracking_number.'",
						"status":"'.$status.'",
						"carrier": "'.$tracking_provider.'"
					}]}';
			logger_helper()->log('info', 'track Shipping: transaction_id：'.$transaction_id.' tracking_number：'.$tracking_number.' status：'.$status);

			$paypalHelper = new WCAPP_PayPal();
			$result=$paypalHelper->trackShipping($data);
			
			if(empty($result['data']['id'])){
			    return false;
			}else{
			    return true;
			}
		}
		
	}
	new WCAPP_Seller_Protection();
}