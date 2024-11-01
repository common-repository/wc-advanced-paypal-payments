<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// PAYPAL_ENDPOINTS
if (!defined('WCAPP_PAYPAL_ENDPOINTS')) {
    define("WCAPP_PAYPAL_ENDPOINTS", array(
	    "sandbox" => "https://api.sandbox.paypal.com",
	    "live" => "https://api.paypal.com"
    ));
}

// PARTNER_MERCHANT_ID
if (!defined('WCAPP_PARTNER_MERCHANT_ID')) {
    define("WCAPP_PARTNER_MERCHANT_ID", array(
	    "sandbox" => "XPTMWEM2CQWAE",
	    "live" => "XPTMWEM2CQWAE"
    ));
}

include_once('class-wcapp-http-handler.php');

/**
*	PayPal helper class for REST API requests.
*	
*/
if ( ! class_exists( 'WCAPP_PayPal' ) ) {
	
	class WCAPP_PayPal {
		
		private $_http = null;
		private $_apiUrl = null;
		private $_token = null;
		private $_env = null;
		
		/**
		* 	Class constructor.
		*	
		*/
		public function __construct($env=null) {
			$this->_http = new WCAPP_HttpHandler();
			if(empty($env)){
				$settings_array = setting_helper()->settings_array;
				$env = $settings_array['env'];
		    }
			$this->_env=$env;
			$this->_apiUrl = WCAPP_PAYPAL_ENDPOINTS[$env];
		}
		
		/**
		* 	Create the PayPal REST endpoint url.
		*
		*	Use the configurations and combine resources to create the endpoint.
		*
		*	@param string $resource Url to be called using http
		* 	@return string REST API url depending on environment.
		*/
		private function _createApiUrl($resource) {
			if($resource == 'oauth2/token')
				$url=$this->_apiUrl . "/v1/" . $resource;
			else
				$url=$this->_apiUrl . "/v2/" . $resource;
			
			logger_helper()->log('info', 'api url：'.$url);

			
			return $url;
		}
		
		/**
		* 	Create the PayPal REST endpoint url(v1).
		*
		*	Use the configurations and combine resources to create the endpoint.
		*
		*	@param string $resource Url to be called using http
		* 	@return string REST API url depending on environment.
		*/
		private function _createApiUrlV1($resource) {
			$url=$this->_apiUrl . "/v1/" . $resource;
			
			logger_helper()->log('info', 'api url：'.$url);
			
			return $url;
		}
		
		/**
		* 	Request for PayPal REST oath bearer token.
		*	
		* 	Reset http helper. 
		*	Set default PayPal headers.
		*	Set http url.
		*	Set http credentials.
		*	Set http body.
		*	Set class token attribute with bearer token.
		*
		* 	@return void
		*/
		private function _getToken() {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("oauth2/token"));
			
            $body = 'grant_type=client_credentials';
			$this->_http->setBody($body);

			$returnData = $this->_http->sendCredential();
			$this->_token = $returnData['access_token'];
			$this->_http->setToken($this->_token);
		}
		
		/**
		* 	Request for PayPal REST oath bearer token.
		*
		* 	@param $client_id
		* 	@param $client_secret
		*	
		* 	Reset http helper. 
		*	Set default PayPal headers.
		*	Set http url.
		*	Set http credentials.
		*	Set http body.
		*	Set class token attribute with bearer token.
		*
		* 	@return void
		*/
		private function _getTokenByRefer($client_id, $client_secret) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("oauth2/token"));

            $body = 'grant_type=client_credentials';
			$this->_http->setBody($body);
			
			$returnData = $this->_http->sendCredential($client_id, $client_secret);
			$this->_token = $returnData['access_token'];
			$this->_http->setToken($this->_token);
		}
		
		/**
		* 	Request for PayPal REST oath bearer seller token.
		*
		* 	@param $shared_id
		* 	@param $auth_code
		* 	@param $seller_nonce
		*	
		* 	Reset http helper. 
		*	Set default PayPal headers.
		*	Set http url.
		*	Set http credentials.
		*	Set http body.
		*	Set class token attribute with bearer token.
		*
		* 	@return void
		*/
		private function _getTokenSeller($shared_id, $auth_code, $seller_nonce) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("oauth2/token"));

            //$body = 'grant_type=client_credentials';
			$body = "grant_type=authorization_code&code=".$auth_code."&code_verifier=".$seller_nonce;
			$this->_http->setBody($body);
			
			$returnData = $this->_http->sendCredentialSeller($shared_id, $auth_code, $seller_nonce);
			return $returnData['access_token'];
		}
		
		/**
		* 	Request for PayPal REST oath bearer token.
		*	
		* 	Reset http helper. 
		*	Set default PayPal headers.
		*	Set http url.
		*	Set http credentials.
		*	Set http body.
		*	Set class token attribute with bearer token.
		*
		* 	@return string
		*/
		public function getToken($env,$sandbox_client_id,$sandbox_client_secret,$live_client_id,$live_client_secret) {
			$this->_http->resetHelper();
			$this->_apiUrl = WCAPP_PAYPAL_ENDPOINTS[$env];
			$this->_http->setUrl($this->_createApiUrl("oauth2/token"));
			
			$clientid='';
			$client_secret='';
			if($env=='sandbox'){
			   $clientid=$sandbox_client_id;
			   $client_secret=$sandbox_client_secret;
			}else{
			   $clientid=$live_client_id;
			   $client_secret=$live_client_secret;
			}
			
            $body = 'grant_type=client_credentials';
			$this->_http->setBody($body);
			
			$returnData = $this->_http->sendCredential($client_id, $client_secret);
			$this->_token = $returnData['access_token'];	
			return $this->_token;
		}
		
		/**
		* 	Actual call to http helper to create an order using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		*	@param array $postData Url to be called using http
		* 	@return array PayPal REST create response
		*/
		private function _createOrder($postData) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("checkout/orders"));
			$this->_http->setBody($postData);
			return $this->_http->sendRequest(); 
		}
		
		/**
		* 	Actual call to http helper to an order capture using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		*	@param array $postData Url to be called using http
		* 	@return array PayPal REST refund response
		*/
		private function _orderCapture($order_id) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("checkout/orders/".$order_id."/capture"));
			$this->_http->setBody('{}');
			return $this->_http->sendRequest(); 
		}
		
		/**
		* 	Actual call to http helper to an order refund using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		*	@param array $postData Url to be called using http
		* 	@return array PayPal REST refund response
		*/
		private function _capturesRefund($postData, $capture_id) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("payments/captures/".$capture_id."/refund"));
			$this->_http->setBody($postData);
			return $this->_http->sendRequest(); 
		}
        
		/**
		* 	Actual call to http helper to get a payment using PayPal REST APIs.
		*
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*
		* 	@param array $postData Url to be called using http
		* 	@return array PayPal REST execute response
		*/
		private function _getOrderDetails() {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("checkout/orders/" . $_SESSION['order_id']));
			return $this->_http->sendRequest();
		}
		
		/**
		* 	Actual call to http helper to execute a payment using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		*	@param array $postData Url to be called using http
		* 	@return array PayPal REST execute response
		*/
		private function _patchOrder($postData) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("checkout/orders/" . $_SESSION['order_id']));
			$this->_http->setBody($postData);
			return $this->_http->sendPatch();
		}
		
		/**
		* 	Actual call to http helper to execute a payment using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		*	@param array $postData Url to be called using http
		* 	@return array PayPal REST execute response
		*/
		private function _getCapture($capture_id) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrl("payments/captures/".$capture_id));
			$this->_http->setBody($postData);
			return $this->_http->sendGet();
		}
		
		/**
		* 	Actual call to http helper to an order track shipping using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		*	@param array $postData Url to be called using http
		* 	@return array PayPal REST track shipping response
		*/
		private function _trackShipping($postData) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrlV1("shipping/trackers-batch"));
			$this->_http->setBody($postData);
			return $this->_http->sendRequest(); 
		}

		/**
		* 	Actual call to http helper to an webhook list using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		* 	@return array PayPal REST webhook create response
		*/
		private function _webhookList() {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrlV1("notifications/webhooks"));
			//$this->_http->setBody('{}');
			return $this->_http->sendGet(); 
		}
		
		/**
		* 	Actual call to http helper to an webhook create using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		*	@param array $postData Url to be called using http
		* 	@return array PayPal REST webhook create response
		*/
		private function _webhookCreate($postData) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrlV1("notifications/webhooks"));
			$this->_http->setBody($postData);
			return $this->_http->sendRequest(); 
		}
        
		/**
		* 	Actual call to http helper to an webhook delete using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		*	@param array $postData Url to be called using http
		* 	@return array PayPal REST webhook delete response
		*/
		private function _webhookDelete($webhook_id) {
			$this->_http->resetHelper();
			$this->_http->setUrl($this->_createApiUrlV1("notifications/webhooks/".$webhook_id));
			//$postData='{}';
			//$this->_http->setBody($postData);
			return $this->_http->sendDelete();
		}

		/**
		* 	Actual call to http helper to an customer credential using PayPal REST APIs.
		*	
		* 	Reset http helper.
		*	Set default PayPal headers.
		* 	Set API call specific headers.
		*	Set http url.
		*	Set http body.
		*
		* 	@return array PayPal REST webhook create response
		*/
		private function _customerCredential($token) {
			$this->_http->resetHelper();
			
			$merchant_id=WCAPP_PARTNER_MERCHANT_ID[$this->_env];
			$this->_http->setUrl($this->_createApiUrlV1("customer/partners/".$merchant_id."/merchant-integrations/credentials"));
			//$this->_http->setBody('{}');
			return $this->_http->sendRequestByToken($token); 
		}
		
		/**
		* 	Call private order create class function to forward post request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST create order function.
		*
		*	@param array $postData Url to be called using http
		* 	@return array Formatted API response
		*/
		public function orderCreate($postData) {
			try{
				if($this->_token === null) {
					$this->_getToken();
				}			
				$returnData = $this->_createOrder($postData);
				
				logger_helper()->log('info', 'order create response：'.json_encode($returnData));
				
				$_SESSION['order_id'] = $returnData['id'];
				return array(
					"ack" => true,
					"details" => $returnData['details'],
					"data" => array(
						"id" => $returnData['id']
					)
				);
			}catch( Exception $e ){
				logger_helper()->log('error', 'orderCreate：'.$e->getMessage());
				return array(
					"ack" => false
				);
			}
		}
		
		/**
		* 	Call private order refund class function to forward post request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST order refund function.
		*
		*	@param array $postData Url to be called using http
		* 	@return array Formatted API response
		*/
		public function capturesRefund($postData, $capture_id) {
			try{
				if($this->_token === null) {
					$this->_getToken();
				}
				$returnData = $this->_capturesRefund($postData, $capture_id);
				
				logger_helper()->log('info', 'order refund response：'.json_encode($returnData));
				
				return array(
					"ack" => true,
					"data" => array(
						"id" => $returnData['id']
					)
				);
			}catch( Exception $e ){
				logger_helper()->log('error', 'capturesRefund：'.$e->getMessage());
				return array(
					"ack" => false
				);
			}
		}
		        
		/**
		* 	Call private payment get class function to forward get request to helper.
		*
		* 	Check for bearer token.
		*	Call internal REST get order details function.
		*
		*  @param array $postData Url to be called using http
		* 	@return array Formatted API response
		*/
		public function orderGet() {
			try{
				if($this->_token === null) {
					$this->_getToken();
				}
				$returnData = $this->_getOrderDetails();
				return array(
					"ack" => true,
					"data" => $returnData
				);
			}catch( Exception $e ){
				logger_helper()->log('error', 'orderGet：'.$e->getMessage());
				return array(
					"ack" => false
				);
			}
		}
		
		/**
		* 	Call private patch order class function to forward patch request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST patch order function.
		*
		*   @param array $postData Url to be called using http
		* 	@return array Formatted API response
		*/
		public function orderPatch($postData) {
			try{
				if($this->_token === null) {
					$this->_getToken();
				}
				$returnData = $this->_patchOrder($postData);
				return array(
					"ack" => true,
					"data" => $returnData
				);
			}catch( Exception $e ){
				logger_helper()->log('error', 'orderPatch：'.$e->getMessage());
				return array(
					"ack" => false
				);
			}
		}
		
		/**
		* 	Call private capture order class function to forward get request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST capture order function.
		*
		*   @param array $postData Url to be called using http
		* 	@return array Formatted API response
		*/
		public function orderCapture($order_id) {
			try{
				if($this->_token === null) {
					$this->_getToken();
				}
				$returnData = $this->_orderCapture($order_id);
				logger_helper()->log('info', 'orderCapture response：'.json_encode($returnData));
				return array(
					"ack" => true,
					"data" => $returnData
				);
			}catch( Exception $e ){
				logger_helper()->log('error', 'orderCapture：'.$e->getMessage());
				return array(
					"ack" => false
				);
			}
		}
		
		/**
		* 	Call private get capture class function to forward get request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST get capture function.
		*
		*   @param array $postData Url to be called using http
		* 	@return array Formatted API response
		*/
		public function getCapture($capture_id) {
			try{
			    if($this->_token === null) {
				    $this->_getToken();
			    }
				$returnData = $this->_getCapture($capture_id);
				logger_helper()->log('info', 'getCapture response：'.json_encode($returnData));
				return array(
					"ack" => true,
					"data" => $returnData
				);
			}catch( Exception $e ){
				logger_helper()->log('error', 'getCapture：'.$e->getMessage());
				return array(
					"ack" => false
				);
			}
		}
		
		/**
		* 	Call private order track shipping class function to forward post request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST order track shipping function.
		*
		*	@param array $postData Url to be called using http
		* 	@return array Formatted API response
		*/
		public function trackShipping($postData) {
			try{
				if($this->_token === null) {
					$this->_getToken();
				}

				$returnData = $this->_trackShipping($postData);
				
				$id=null;
				$tracker_identifiers=$returnData['tracker_identifiers'];
				if(count($tracker_identifiers)>0)
					$id=$tracker_identifiers[0]["transaction_id"];

				logger_helper()->log('info', 'track shipping response：'.json_encode($returnData));
				
				return array(
					"ack" => true,
					"data" => array(
						"id" => $id
					)
				);
			}catch( Exception $e ){
				logger_helper()->log('error', 'trackShipping：'.$e->getMessage());
				return array(
					"ack" => false
				);
			}
		}
		
		/**
		* 	Call private webhook List class function to forward get request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST webhook List function.
		*
		*	@param array $postData Url to be called using http
		*	@param $client_id
		*	@param $client_secret
		*
		* 	@return array Formatted API response
		*/
		public function webhookList( $client_id, $client_secret) {
			try{
				if($this->_token === null) {
					$this->_getTokenByRefer($client_id, $client_secret);
					if(!isset($this->_token) || empty($this->_token)){
						return 'error';
					}
				}

				$returnData = $this->_webhookList();
				logger_helper()->log('info', 'webhook list response：'.json_encode($returnData));
				
				$webhooks=$returnData['webhooks'];
				$webhook_id='';
				if(count($webhooks)>0){
					$url=home_url(constant("WCAPP_WEBHOOK_URL"));
					foreach($webhooks as $item){
						if($item["url"]==$url){
							$webhook_id=$item["id"];
							break;
						}
					}
				}
				return $webhook_id;
			}catch( Exception $e ){
				logger_helper()->log('error', 'webhookList：'.$e->getMessage());
				return '';
			}
		}
		
		/**
		* 	Call private webhook create class function to forward post request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST webhook create function.
		*
		*	@param array $postData Url to be called using http
		*	@param $client_id
		*	@param $client_secret
		*
		* 	@return array Formatted API response
		*/
		public function webhookCreate($postData, $client_id, $client_secret) {
			try{
				if($this->_token === null) {
					$this->_getTokenByRefer($client_id, $client_secret);
				}

				$returnData = $this->_webhookCreate($postData);
				logger_helper()->log('info', 'webhook create response：'.json_encode($returnData));
				if(empty($returnData['id'])){
					//failed
					$message='';
					if(!empty($returnData['details'])&&count($returnData['details'])>0){
						$message=$returnData['details'][0]['issue'];
					}
					return array(
						"is_validate" => true,
						"is_webhook" => false,
						"webhook_message" => $message
					);
				}
				else{
					//success
					$arr=$returnData['event_types'];
					if(count($arr)>0){
						$subscribed_webhooks='';
						foreach($arr as $item){
							$subscribed_webhooks.=$item["name"].',';
						}
						$subscribed_webhooks=rtrim($subscribed_webhooks,',');
					}
					return array(
						"is_validate" => true,
						"is_webhook" => true,
						"webhook_id" => $returnData['id'],
						"url" => $returnData['url'],
						"subscribed_webhooks" => $subscribed_webhooks,
						"webhook_message" => 'created success'
					);
				}
			}catch( Exception $e ){
				logger_helper()->log('error', 'webhookCreate：'.$e->getMessage());
				return array(
					"is_validate" => false,
					"is_webhook" => false,
					"webhook_message" => $e->getMessage()
				);
			}
		}
		
		/**
		* 	Call private webhook delete class function to forward delete request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST webhook delete function.
		*
		*	@param array $postData Url to be called using http
		*	@param $client_id
		*	@param $client_secret
		*
		* 	@return array Formatted API response
		*/
		public function webhookDelete($webhook_id, $client_id, $client_secret) {
			try{
				if($this->_token === null) {
					$this->_getTokenByRefer($client_id, $client_secret);
				}

				$returnData = $this->_webhookDelete($webhook_id);
				
				logger_helper()->log('info', 'webhook delete response：'.$returnData);
				
				if($returnData=='204')
					return true;
				else
					return false;
			}catch( Exception $e ){
				logger_helper()->log('error', 'webhookDelete：'.$e->getMessage());
				return false;
			}
		}
		
		/**
		* Verifies if the current request is a legit webhook event.
		*
		* @param \WP_REST_Request $request The request.
		*
		* @return bool
		* @throws RuntimeException If the request fails.
		*/
		public function verify_current_request_for_webhook( \WP_REST_Request $request ): bool {
            $settings_array = setting_helper()->settings_array;
			if($settings_array['env']=='sandbox'){
				$webhook_id=$settings_array['webhook_id_sandbox'];
			}
			else{
				$webhook_id=$settings_array['webhook_id_live'];
			}
				
            if (empty($webhook_id)) {
				$error = new RuntimeException(
					__( 'Not a valid webhook to verify.', 'advanced-paypal-payments-for-woocommerce' )
				);
				logger_helper()->log('warning', $error->getMessage());
				throw $error;
			}

			$expected_headers = array(
				'PAYPAL-AUTH-ALGO'         => '',
				'PAYPAL-CERT-URL'          => '',
				'PAYPAL-TRANSMISSION-ID'   => '',
				'PAYPAL-TRANSMISSION-SIG'  => '',
				'PAYPAL-TRANSMISSION-TIME' => '',
			);
			$headers = getallheaders();
			foreach ( $headers as $key => $header ) {
				$key = strtoupper( $key );
				if ( isset( $expected_headers[ $key ] ) ) {
					$expected_headers[ $key ] = $header;
				}
			};

			foreach ( $expected_headers as $key => $value ) {
				if ( ! empty( $value ) ) {
					continue;
				}

				$error = new RuntimeException(
					sprintf(
						// translators: %s is the headers key.
						__(
							'Not a valid webhook event. Header %s is missing',
							'advanced-paypal-payments-for-woocommerce'
						),
						$key
					)
				);
				logger_helper()->log('warning', $error->getMessage());
				throw $error;
			}

			$request_body = json_decode( file_get_contents( 'php://input' ) );
			
			return $this->verify_event(
				$expected_headers['PAYPAL-AUTH-ALGO'],
				$expected_headers['PAYPAL-CERT-URL'],
				$expected_headers['PAYPAL-TRANSMISSION-ID'],
				$expected_headers['PAYPAL-TRANSMISSION-SIG'],
				$expected_headers['PAYPAL-TRANSMISSION-TIME'],
				$webhook_id,
				$request_body ? $request_body : new \stdClass()
			);
		}
		
		/**
		 * Verifies if a webhook event is legitimate.
		 *
		 * @param string    $auth_algo The auth algo.
		 * @param string    $cert_url The cert URL.
		 * @param string    $transmission_id The transmission id.
		 * @param string    $transmission_sig The transmission signature.
		 * @param string    $transmission_time The transmission time.
		 * @param string    $webhook_id The webhook id.
		 * @param \stdClass $webhook_event The webhook event.
		 *
		 * @return bool
		 * @throws RuntimeException If the request fails.
		 */
		public function verify_event(
			string $auth_algo,
			string $cert_url,
			string $transmission_id,
			string $transmission_sig,
			string $transmission_time,
			string $webhook_id,
			\stdClass $webhook_event
		): bool {
            if($this->_token === null) {
				$this->_getToken();
			}
			
			$url      = trailingslashit( $this->_apiUrl ) . 'v1/notifications/verify-webhook-signature';
			
			$args     = array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->_token,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'transmission_id'   => $transmission_id,
						'transmission_time' => $transmission_time,
						'cert_url'          => $cert_url,
						'auth_algo'         => $auth_algo,
						'transmission_sig'  => $transmission_sig,
						'webhook_id'        => $webhook_id,
						'webhook_event'     => $webhook_event,
					)
				),
			);
			$response = $this->request( $url, $args );
			if ( is_wp_error( $response ) ) {
				$error = new RuntimeException(
					__( 'Not able to verify webhook event.', 'advanced-paypal-payments-for-woocommerce' )
				);
				logger_helper()->log('warning',$error->getMessage().' '.json_encode(array(
						'args'     => $args,
						'response' => $response,
					)));
				throw $error;
			}
			logger_helper()->log('info','webhook signature response：'.json_encode($response));
				
			$json = json_decode( $response['body'] );
			return isset( $json->verification_status ) && 'SUCCESS' === $json->verification_status;
		}
		
		/**
		 * Performs a request
		 *
		 * @param string $url The URL to request.
		 * @param array  $args The arguments by which to request.
		 *
		 * @return array|WP_Error
		 */
		private function request( string $url, array $args ) {
			$args['timeout'] = 30;
			if ( defined('WCAPP_BN_Code') ) {
				if(wcapp_get_bn_code()!=""){
			       $args['headers']['PayPal-Partner-Attribution-Id'] = WCAPP_BN_Code;
				}
			}
			$response = wp_remote_get( $url, $args );
			return $response;
		}
		
		/**
		* 	Call private get client info class function to forward http request to helper.
		*	
		* 	Check for bearer token.
		*	Call internal REST webhook event transmission function.
		*
		*	@param array $postData Url to be called using http
		* 	@return array Formatted API response
		*/
		public function Client_Info($website_url) {
			try{
				$url="https://paypal.uin88.com/marketplace/getclientinfo.php";
				$body = array(
				    'website_url' => $website_url
				);
				$args = array(
				    'body' => $body
				);
				$response = wp_remote_post($url, $args);
				
				logger_helper()->log('info','get client info response：'.json_encode($response));
				
				$body = wp_remote_retrieve_body( $response );

				return json_decode($body,true);
			}catch( Exception $e ){
				logger_helper()->log('error', 'Client_Info：'.$e->getMessage());
				return array(
					"ack" => false
				);
			}
		}
		
		/**
			* 	Call private get customer credential class function to forward curl request to helper.
			*	
			* 	Check for bearer token.
			*	Call internal REST get customer credential function.
			*
			*	@param array $postData Url to be called using curl
			* 	@return array Formatted API response
		*/
		public function customerCredential($shared_id, $auth_code, $seller_nonce) {
			try{
				$token_seller=$this->_getTokenSeller($shared_id, $auth_code, $seller_nonce);
				if(empty($token_seller))
					return array(
						"ack" => false
					);
					
				$returnData = $this->_customerCredential($token_seller);
				if(isset($returnData)){
					logger_helper()->log('info', "customer credential response：".json_encode($returnData));
					
					if(!empty($returnData['client_id'])){
						return array(
							"ack" => true,
							"client_id" => $returnData['client_id'],
							"client_secret" => $returnData['client_secret'],
							"payer_id" => $returnData['payer_id'],
							"env" => $this->_env
						);
					}
				}
				return array(
					"ack" => false
				);
			}catch( Exception $e ){
				return array(
					"ack" => false
				);
			}
		}
	}
}