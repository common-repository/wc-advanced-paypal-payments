<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WCAPP_Checkout' ) ) {
    
	/**
	 * WCAPP Checkout Class.
	 */
	class WCAPP_Checkout extends WC_Checkout {
	    
		/**
		 * Validates that the checkout has enough info to proceed.
		 *
		 * @since  1.0.0
		 * @param  array    $data   An array of posted data.
		 * @param  WP_Error $errors Validation errors.
		 */
		public function wcapp_validate_checkout( &$data, &$errors ){
			$this->validate_checkout( $data, $errors );
		}
	}
}
