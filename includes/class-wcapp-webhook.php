<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_WEB_Hook' ) ) {
	
	/**
	 * WCAPP Web Hook Class.
	 */
	class WCAPP_WEB_Hook {
		
		/**
		 * constructor.
		 *
		 */
		public function __construct() {
			add_action( 'rest_api_init',  array( $this, 'register_routes' ) );
		}
		
		/**
		 * Registers REST routes under 'wcapp/v1/webhook'.
		 */
		public function register_routes() {
			register_rest_route(
				'wcapp/v1',
				'webhook',
				array(
					'methods'             => array(
						'POST',
					),
					'callback'            => array(
						$this,
						'handle_request',
					),
					'permission_callback' => array(
						$this,
						'verify_request',
					),
				)
			);
		}

		/**
		 * Verifies the current request.
		 *
		 * @param \WP_REST_Request $request The request.
		 *
		 * @return bool
		 */
		public function verify_request( \WP_REST_Request $request ): bool {
			try {
				$array=array(
					'PAYMENT.CAPTURE.COMPLETED',
					'PAYMENT.CAPTURE.REFUNDED',
					'PAYMENT.CAPTURE.PENDING',
					'PAYMENT.CAPTURE.DENIED',
					'PAYMENT.CAPTURE.REVERSED',
					'PAYMENT.ORDER.CANCELLED'
				);
				
				if( in_array($request['event_type'],$array) ) {
					//logger_helper()->log('info','Verify Request Body:'.$request->get_body());
					$paypalHelper = new WCAPP_PayPal();
					return $paypalHelper->verify_current_request_for_webhook($request);
				}else{
					return false;
				}
			} catch ( RuntimeException $exception ) {
				logger_helper()->log('warning', 'Webhook verification failed: ' . $exception->getMessage());
				return false;
			}		
		}

		/**
		 * Handles the request.
		 *
		 * @param \WP_REST_Request $request The request.
		 *
		 * @return \WP_REST_Response
		 */
		public function handle_request( \WP_REST_Request $request ): \WP_REST_Response {
			logger_helper()->log('info','Callback Request Body:'.$request->get_body());
			
			if( $request['event_type']=="PAYMENT.CAPTURE.COMPLETED" ) {
				return $this->payment_capture_completed($request);
			}else if( $request['event_type']=="PAYMENT.CAPTURE.REFUNDED" ) {
				return $this->payment_capture_refunded($request);
			}else if( $request['event_type']=="PAYMENT.CAPTURE.PENDING" ) {
				return $this->payment_capture_pending($request);
			}else if( $request['event_type']=="PAYMENT.CAPTURE.DENIED" ) {
				return $this->payment_order_cancelled($request);
			}else if( $request['event_type']=="PAYMENT.CAPTURE.REVERSED" ) {
				return $this->payment_order_cancelled($request);
			}else if( $request['event_type']=="PAYMENT.ORDER.CANCELLED" ) {
				return $this->payment_order_cancelled($request);
			}else{
				$message = sprintf(
					// translators: %s is the request type.
					__( 'Could not find handler for request type %s', 'advanced-paypal-payments-for-woocommerce' ),
					$request['event_type']
				);
	
				$response = array(
					'success' => false,
					'message' => $message,
				);
				return rest_ensure_response( $response );
			}
		}
		
		/**
		* Handles the PAYMENT.CAPTURE.COMPLETED request.
		* @param WP_REST_Request $request
		*/
		public function payment_capture_completed( \WP_REST_Request $request ): \WP_REST_Response {
			$response = array( 'success' => false );
			$id=$request['resource']['id'];
			$order_id=$this->get_complete_meta( '_capture_id', $id );
			if( $order_id==false ){
				$message=sprintf(
					// translators: %s is the order id.
					'WC order for PayPal ID %s not found.',
					(string) $id
				);
				logger_helper()->log('warning', $message);
				$response['message'] = $message;
				return new WP_REST_Response( $response );
			}
			$order = wc_get_order( $order_id );
			if ( ! is_a( $order, \WC_Order::class ) ) {
				$message=sprintf(
					// translators: %s is the order id.
					'No order for webhook event %s was found.',
					(string) $order->get_id()
				);
				logger_helper()->log('warning', $message);
				
				$response['message'] = $message;
				return new WP_REST_Response( $response );
			}
			
			if ( $order->get_status() !== 'on-hold' ) {
				$response['success'] = true;
			    return new WP_REST_Response( $response );
			}
			
			$order->add_order_note(
				__( 'Payment successfully captured.', 'advanced-paypal-payments-for-woocommerce' )
			);
			$order->payment_complete();
			
			$response['success'] = true;
		    return new WP_REST_Response( $response );
		}
		
		/**
		* Handles the PAYMENT.CAPTURE.REFUNDED request.
		* @param WP_REST_Request $request
		*/
		public function payment_capture_refunded( \WP_REST_Request $request ): \WP_REST_Response {
		    $response  = array( 'success' => false );
			$id=$request['resource']['invoice_id'];
			$order_id=$this->get_complete_meta( '_invoice_id', $id );
			if( $order_id==false ){
				$message=sprintf(
					// translators: %s is the order id.
					'WC order for PayPal ID %s not found.',
					(string) $id
				);
				logger_helper()->log('warning', $message);

				$response['message'] = $message;
				return new WP_REST_Response( $response );
			}
			$order = wc_get_order( $order_id );
			if ( ! is_a( $order, \WC_Order::class ) ) {
				$message = sprintf(
					// translators: %s is the PayPal refund Id.
						'Order for PayPal refund %s not found.',
						$id
					);
				logger_helper()->log('warning', $message);
				
				$response['message'] = $message;
			    return new WP_REST_Response( $response );
			}
			
			$manual_refunded=get_post_meta( $order_id, '_manual_refunded', true );
			if($manual_refunded){
				logger_helper()->log('warning', 'manual_refunded:'.$manual_refunded);
				$response['success'] = true;
				return new WP_REST_Response( $response );
			}
			
            $amount=$request['resource']['amount']['value'];			
			$refund = wc_create_refund( array(
				'order_id'       => $order->get_id(),
				'amount'         => $amount,
				'reason'         => 'PayPal webhook callback. Refund initialized from your PayPal account.',
				//'refund_payment' => true,
				//'restock_items'  => true,
			) );
			
			if ( is_wp_error( $refund ) ) {
				$message = sprintf(
					// translators: %s is the order id.
					'Order %s could not be refunded.',
					(string) $order->get_id()
				);
				logger_helper()->log('warning', $message.' error_message：'.$refund->get_error_message().' refund amount：'.$amount);
				
				$response['message'] = $refund->get_error_message();
				return new WP_REST_Response( $response );
			}
			
			
			$message = sprintf(
				// translators: %1$s is the order id %2$s is the amount which has been refunded.
				'Order %1$s has been refunded with %2$s through PayPal',
				(string) $order->get_id(),
				(string) $refund->get_amount()
			);
			logger_helper()->log('info', $message);
			
			$order->add_order_note(
				sprintf(
					__( 'PayPal has successfully refunded. Refunded amount：%s', 'advanced-paypal-payments-for-woocommerce' ),
					$amount
				)
			);
			
			$response['success'] = true;
		    return new WP_REST_Response( $response );
		}
		
		/**
		* Handles the PAYMENT.CAPTURE.PENDING request.
		* @param WP_REST_Request $request
		*/
		public function payment_capture_pending( \WP_REST_Request $request ): \WP_REST_Response {
		    $response = array( 'success' => false );
			$id=$request['resource']['id'];
			$order_id=$this->get_complete_meta( '_capture_id', $id );
			if( $order_id==false ){
				$message=sprintf(
					// translators: %s is the order id.
					'WC order for PayPal ID %s not found.',
					(string) $id
				);
				logger_helper()->log('warning', $message);
				$response['message'] = $message;
				return new WP_REST_Response( $response );
			}
			$order = wc_get_order( $order_id );
			if ( ! is_a( $order, \WC_Order::class ) ) {
				$message = sprintf(
					'WC order for PayPal ID %s not found.',
					$request['resource'] !== null && isset( $request['resource']['id'] ) ? $request['resource']['id'] : ''
				);
				logger_helper()->log('warning', $message);

				$response['message'] = $message;
				return new WP_REST_Response( $response );
			}
			
			if ( $order->get_status() === 'pending' ) {
				$order->update_status( 'on-hold', __( 'Payment initiation was successful, and is waiting for the buyer to complete the payment.', 'advanced-paypal-payments-for-woocommerce' ) );

			}

			$response['success'] = true;
			return new WP_REST_Response( $response );
		}
		
		/**
		* Handles the PAYMENT.ORDER.CANCELLED request.
		* @param WP_REST_Request $request
		*/
		public function payment_order_cancelled( \WP_REST_Request $request ): \WP_REST_Response {
		    $response = array( 'success' => false );
			$invoice_id=$request['resource']['invoice_id'];
			$id=$request['resource']['id'];
			$order_id=$this->get_complete_meta( '_invoice_id', $invoice_id );
			if( $order_id==false ){
				$order_id=$this->get_complete_meta( '_capture_id', $id );
			}
			if( $order_id==false ){
				$order_id=$this->get_complete_meta( '_transaction_id', $id );
			}
			if( $order_id==false ){
				$message=sprintf(
					// translators: %s is the order id.
					'WC order for PayPal ID %s not found.',
					(string) $id
				);
				logger_helper()->log('warning', $message);
				$response['message'] = $message;
				return new WP_REST_Response( $response );
			}
			$order = wc_get_order( $order_id );
			if ( ! is_a( $order, \WC_Order::class ) ) {
				$message = sprintf(
					'Order for PayPal %s not found.',
					isset( $request['resource']['id'] ) ? $request['resource']['id'] : ''
				);
				logger_helper()->log('warning', $message);
				
				$response['message'] = $message;
			    return rest_ensure_response( $response );
			}
			$response['success'] = (bool) $order->update_status( 'cancelled', __( 'Order %1$s has been cancelled through PayPal', 'advanced-paypal-payments-for-woocommerce' ) );
			
			$message = $response['success'] ? sprintf(
				// translators: %1$s is the order id.
				'Order %1$s has been cancelled through PayPal',
				(string) $order->get_id()
			) : sprintf(
				// translators: %1$s is the order id.
				'Failed to cancel order %1$s through PayPal',
				(string) $order->get_id()
			);
			
			if($response['success']){
				logger_helper()->log('info', $message);
			}else{
				logger_helper()->log('warning', $message);
			}
			return rest_ensure_response( $response );
		}
		
		/**
		* Get meta by meta_key & meta_value
		* @param string $meta_key
		* @param string $meta_value
		* @return int post_id
		*/
		private function get_complete_meta( $meta_key, $meta_value ) {
		    global $wpdb;
		    $result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_key = %d AND meta_value= %s", $meta_key, $meta_value) );
		    if( count($result)>0 ) {
			    return $result[0]->post_id;
			}
		    return false;
		}
		
	}
	new WCAPP_WEB_Hook();
}