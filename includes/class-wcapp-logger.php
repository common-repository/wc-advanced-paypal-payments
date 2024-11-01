<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_Logger' ) ) {
	/*
	* WCAPP Logger
	*/
	class WCAPP_Logger {
		
		/**
		 * instance.
		 */
		protected static $instance;
		
		/*
		* logger 
		*/
		public $logger;
		
		/**
		 * Constructor.
		 */
		public function __construct() {
			$settings_array = setting_helper()->settings_array;	
			if($settings_array['log_enabled']=='yes'){
				$this->$logger = new WC_Logger();
			}
		}
		
		/**
		* write log
		* @param string   $level
		* @param string   $message
		*/
		public function log( $level, $message ) {
			if( !is_null($this->$logger) ){
			   $this->$logger->log( $level, $message , array( 'source' => 'advanced-paypal-payments-for-woocommerce' ) );
			}
		}
		
		/**
		* get WCAPP_Logger instance
		*/
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
    
	/**
	* get WCAPP_Logger class instance
	*/
	function logger_helper() {
		return WCAPP_Logger::get_instance();
	}
}