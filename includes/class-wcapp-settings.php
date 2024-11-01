<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_Settings' ) ) {
	/*
	* WCAPP Settings
	*/
	class WCAPP_Settings {
		
		/**
		 * instance.
		 */
		protected static $instance;
		
		/*
		* settings 
		*/
		public $settings_array;
		
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->settings_array = (array) get_option( 'woocommerce_advanced-paypal-payments-for-woocommerce_settings', array() );	
		}
		
		/**
		* get WCAPP_Settings instance
		*/
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
    
	/**
	* get WCAPP_Settings class instance
	*/
	function setting_helper() {
		return WCAPP_Settings::get_instance();
	}
}