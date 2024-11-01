<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_New_Settings' ) ) {
	/*
	* WCAPP Settings
	*/
	class WCAPP_New_Settings {
		const KEY = 'woocommerce_advanced-paypal-payments-for-woocommerce_settings';
		
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
			//$this->settings_array = get_option( 'woocommerce_advanced-paypal-payments-for-woocommerce_settings', array() );	
		}
		
		/**
		* get WCAPP_New_Settings instance
		*/
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
		
		/**
		 * Returns the value for an id.
		 *
		 * @param string $id The value identificator.
		 *
		 * @return mixed
		 * @throws NotFoundException When nothing was found.
		 */
		public function get( $id ) {
			if ( ! $this->has( $id ) ) {
				throw new NotFoundException();
			}
			return $this->settings_array[ $id ];
		}

		/**
		 * Whether a value exists.
		 *
		 * @param string $id The value identificator.
		 *
		 * @return bool
		 */
		public function has( $id ) {
			$this->load();
			return array_key_exists( $id, $this->settings_array );
		}

		/**
		 * Sets a value.
		 *
		 * @param string $id The value identificator.
		 * @param mixed  $value The value.
		 */
		public function set( $id, $value ) {
			$this->load();
			$this->settings_array[ $id ] = $value;
		}

		/**
		 * Stores the settings to the database.
		 */
		public function persist() {
			return update_option( self::KEY, $this->settings_array );
		}


		/**
		 * Loads the settings.
		 *
		 * @return bool
		 */
		private function load(): bool {

			if ( $this->settings_array ) {
				return false;
			}
			$this->settings_array = get_option( self::KEY, array() );
			
			$defaults = array(
				'connect_status' => 'no',
			);
			foreach ( $defaults as $key => $value ) {
				if ( isset( $this->settings_array[ $key ] ) ) {
					continue;
				}
				$this->settings_array[ $key ] = $value;
			}
			return true;
		}
	}
    
	/**
	* get WCAPP_New_Settings class instance
	*/
	function setting_new_helper() {
		return WCAPP_New_Settings::get_instance();
	}
}