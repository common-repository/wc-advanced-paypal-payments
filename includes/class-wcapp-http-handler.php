<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
*	Http helper class for http api requests.
*	
* 	A class that provides a http api instance with default setup.
*/

if ( ! class_exists( 'WCAPP_HttpHandler' ) ) {
	
	class WCAPP_HttpHandler {
		
		public $_url = null;
		public $_token = null;
		public $_headers = array();
		public $_bodys = array();
		
		/**
		* 	Class constructor.
		*	
		*/
		public function __construct() {

		}
		
		/**
		* 	Set http api headers using the class header array property.
		*
		* 	@return void
		*/
		private function _setHeaders($herder) {
			$this->$headers = $herder;
		}
		
		/**
		* 	Execute post request.
		*
		*	Set headers and body for http api instance.
		*	Run http api instance.
		*	Return result or trigger warning.
		*
		* 	@return array Result of the http api request
		*/
		private function _sendRequest() {
			$args = array(
			    'headers'     => $this->_headers,
				'body'        => $this->_bodys,
				'timeout'     => '30'
			);
			$args['headers']['Content-Type'] = 'application/json';
			$args['headers']['Authorization'] = 'Bearer ' . $this->_token;
			if ( defined('WCAPP_BN_Code') ) {
				if(wcapp_get_bn_code()!=""){
			        $args['headers']['PayPal-Partner-Attribution-Id'] = constant("WCAPP_BN_Code");
				}
			}
			
			$response = wp_remote_post( $this->_url, $args );
			
			$body = wp_remote_retrieve_body( $response );
			return json_decode($body,true);
		}
		
		/**
		* 	Execute post request.
		*
		*	Set headers and body for http api instance.
		*	Run http api instance.
		*	Return result or trigger warning.
		*
		* 	@return array Result of the http api request
		*/
		private function _sendCredential($clientid, $client_secret) {
			$args = array(
				'headers'     => $this->_headers,
				'body'        => $this->_bodys,
				'timeout'     => '30'
			);
			$args['headers']['Content-Type'] = 'application/json';
			
			logger_helper()->log('info','clientid：'.$clientid.' client_secret: '.$client_secret);
			$args['headers']['Authorization'] = 'Basic ' . base64_encode($clientid . ':' . $client_secret);
			
			if ( defined('WCAPP_BN_Code') ) {
				if(wcapp_get_bn_code()!=""){
			        $args['headers']['PayPal-Partner-Attribution-Id'] = WCAPP_BN_Code;
				}
			}

			$response = wp_remote_post( $this->_url, $args );
			
			$body = wp_remote_retrieve_body( $response );
			return json_decode($body,true);
		}
		
		/**
		* 	Execute post request.
		*
		*	Set headers and body for http api instance.
		*	Run http api instance.
		*	Return result or trigger warning.
		*
		* 	@return array Result of the http api request
		*/
		private function _sendCredentialSeller($shared_id, $auth_code, $seller_noncet) {
			$args = array(
				'headers'     => $this->_headers,
				'body'        => $this->_bodys,
				'timeout'     => '30'
			);
			$args['headers']['Content-Type'] = 'application/json';
			
			$args['headers']['Authorization'] = 'Basic ' . base64_encode($shared_id);
			
			if ( defined('WCAPP_BN_Code') ) {
				if(wcapp_get_bn_code()!=""){
			        $args['headers']['PayPal-Partner-Attribution-Id'] = WCAPP_BN_Code;
				}
			}

			$response = wp_remote_post( $this->_url, $args );
			
			$body = wp_remote_retrieve_body( $response );
			return json_decode($body,true);
		}
		
		/**
		* 	Execute get request.
		*
		*	Set headers and body for http api instance.
		*	Run http api instance.
		*	Return result or trigger warning.
		*
		* 	@return array Result of the http api request
		*/
		private function _sendGet() {
			$args = array(
			    'headers'     => $this->_headers,
				'body'        => $this->_bodys,
				'timeout'     => '30'
			);
			$args['headers']['Content-Type'] = 'application/json';
			$args['headers']['Authorization'] = 'Bearer ' . $this->_token;
			if ( defined('WCAPP_BN_Code') ) {
				if(wcapp_get_bn_code()!=""){
			        $args['headers']['PayPal-Partner-Attribution-Id'] = constant("WCAPP_BN_Code");
				}
			}
			
			$response = wp_remote_get( $this->_url, $args );
			
			$body = wp_remote_retrieve_body( $response );
			return json_decode($body,true);
		}
		
		/**
		* 	Execute patch request.
		*
		*	Set headers and body for http api instance.
		*	Run http api instance.
		*	Return result or trigger warning.
		*
		* 	@return array Result of the http api request
		*/
		private function _sendPatch() {
			$args = array(
			    'headers'     => $this->_headers,
				'body'        => $this->_bodys,
				'timeout'     => '30',
				'method'      => 'PATCH'
			);
			$args['headers']['Content-Type'] = 'application/json';
			$args['headers']['Authorization'] = 'Bearer ' . $this->_token;
			if ( defined('WCAPP_BN_Code') ) {
				if(wcapp_get_bn_code()!=""){
			        $args['headers']['PayPal-Partner-Attribution-Id'] = constant("WCAPP_BN_Code");
				}
			}
			
			$response = wp_remote_post( $this->_url, $args );
			
			$body = wp_remote_retrieve_body( $response );
			return json_decode($body,true);
		}
		
		/**
		* 	Execute delete request.
		*
		*	Set headers and body for http api instance.
		*	Run http api instance.
		*	Return result or trigger warning.
		*
		* 	@return array Result of the http api request
		*/
		private function _sendDelete() {
			$args = array(
			    'headers'     => $this->_headers,
				'body'        => $this->_bodys,
				'timeout'     => '30',
				'method'      => 'DELETE'
			);
			$args['headers']['Content-Type'] = 'application/json';
			$args['headers']['Authorization'] = 'Bearer ' . $this->_token;
			if ( defined('WCAPP_BN_Code') ) {
				if(wcapp_get_bn_code()!=""){
			        $args['headers']['PayPal-Partner-Attribution-Id'] = constant("WCAPP_BN_Code");
				}
			}
			
			$response = wp_remote_post( $this->_url, $args );
			$http_code = wp_remote_retrieve_response_code( $response );
			return $http_code;
		}
		
		/**
		* 	Execute post request.
		*
		*	Set headers and body for http api instance.
		*	Run http api instance.
		*	Return result or trigger warning.
		*
		* 	@return array Result of the http api request
		*/
		private function _sendRequestByToken($token) {
			$args = array(
			    'headers'     => $this->_headers,
				'body'        => $this->_bodys,
				'timeout'     => '30'
			);
			$args['headers']['Content-Type'] = 'application/json';
			$args['headers']['Authorization'] = 'Bearer ' . $token;
			if ( defined('WCAPP_BN_Code') ) {
				if(wcapp_get_bn_code()!=""){
			        $args['headers']['PayPal-Partner-Attribution-Id'] = constant("WCAPP_BN_Code");
				}
			}

			$response = wp_remote_get( $this->_url, $args );
			
			$body = wp_remote_retrieve_body( $response );
			return json_decode($body,true);
		}
		
		/**
		* 	Reset the helper.
		*	
		*	Re-initialize the http api instance.
		*	Reset class header array property.
		*
		* 	@return void
		*/
		public function resetHelper() {
			$this->_url = null;
			$this->_headers = array();
			$this->_bodys = array();
		}
		
		/**
		* 	Set http api url.
		*	
		*	@param string $url Url to be called using http api
		* 	@return void
		*/
		public function setUrl($url) {
			$this->_url = $url;
		}
		
		/**
		* 	Set http api token.
		*	
		*	@param string $url Url to be called using http api
		* 	@return void
		*/
		public function setToken($token) {
			$this->_token = $token;
		}
		
		/**
		* 	Set body of the http api request.
		*	
		*	URL encode the request body.
		*	Check if data is array and url encode.
		*	Set the http api body.
		*	Set http api data
		*	
		*	@param array|string $data http api request body
		* 	@return void
		*/
		public function setBody($data) {
			$this->_bodys = $data;
		}
		
		/**
		* 	Push header values into class header array property.
		*	
		*	@param string $header Header key-value as string
		* 	@return void
		*/
		public function addHeader($header) {
			$this->_headers[] = $header;
		}
		
		/**
		* 	Public function to start execution of http api request.
		*	
		*	Check if post type request and setup http api options.
		*	Call internal function to execute http api instance.
		*
		* 	@return array http api response
		*/
		public function request() {
			return $this->_request();
		}
		
		/**
		* 	Public function to start execution of http api request.
		*	
		*	Check if post type request and setup http api options.
		*	Call internal function to execute http api instance.
		*
		* 	@return array http api response
		*/
		public function sendRequest() {
			return $this->_sendRequest();
		}
		
		/**
		* 	Public function to start execution of http api request.
		*	
		*	Check if get type request and setup http api options.
		*	Call internal function to execute http api instance.
		*
		* 	@return array http api response
		*/
		public function sendGet() {
			return $this->_sendGet();
		}
		
		/**
		* 	Public function to start execution of http api request.
		*	
		*	Check if patch type request and setup http api options.
		*	Call internal function to execute http api instance.
		*
		* 	@return array http api response
		*/
		public function sendPatch() {
			return $this->_sendPatch();
		}
		
		/**
		* 	Public function to start execution of http api request.
		*	
		*	Check if delete type request and setup http api options.
		*	Call internal function to execute http api instance.
		*
		* 	@return array http api response
		*/
		public function sendDelete() {
			return $this->_sendDelete();
		}
		
		/**
		* 	Public function to start execution of http api request.
		*	
		*	Check if post type request and setup http api options.
		*	Call internal function to execute http api instance.
		*
		* 	@return array http api response
		*/
		public function sendCredential($clientid=null, $client_secret=null) {
			if(empty($clientid)){
				$settings_array = setting_helper()->settings_array;
				if($settings_array['env']=='sandbox'){
				   $clientid=$settings_array['client_id_sandbox'];
				   $client_secret=$settings_array['client_secret_sandbox'];
				}else{
				   $clientid=$settings_array['client_id_production'];
				   $client_secret=$settings_array['client_secret_production'];
				}
			}
			return $this->_sendCredential($clientid, $client_secret);
		}

		
		/**
		* 	Public function to start execution of http api request.
		*	
		*	Check if post type request and setup http api options.
		*	Call internal function to execute http api instance.
		*
		* 	@return array http api response
		*/
		public function sendCredentialSeller($shared_id, $auth_code, $seller_noncet) {
			return $this->_sendCredentialSeller($shared_id, $auth_code, $seller_noncet);
		}
		
		/**
		* 	Public function to start execution of http api request.
		*	
		*	Check if post type request and setup http api options.
		*	Call internal function to execute http api instance.
		*
		* 	@return array http api response
		*/
		public function sendRequestByToken($token) {
			return $this->_sendRequestByToken($token);
		}
	}
}