/* global wc_wcapp_generate_cart_context */
;(function( $, window, document ) {
	'use strict';

	var generate_cart = function( callback ) {
		var data = {
			'nonce': wc_wcapp_generate_cart_context.generate_cart_nonce,
			'attributes': {},
		};
        var form = $( 'form.cart' );
		var field_pairs = form.serializeArray();

		for ( var i = 0; i < field_pairs.length; i++ ) {
			// Prevent the default WooCommerce PHP form handler from recognizing this as an "add to cart" call
			if ( 'add-to-cart' === field_pairs[ i ].name ) {
				field_pairs[ i ].name = 'wcapp-add-to-cart';
			}

			// Save attributes as a separate prop in `data` object,
			// so that `attributes` can be used later on when adding a variable product to cart
			if ( -1 !== field_pairs[ i ].name.indexOf( 'attribute_' ) ) {
				data.attributes[ field_pairs[ i ].name ] = field_pairs[ i ].value;
				continue;
			}

			data[ field_pairs[ i ].name ] = field_pairs[ i ].value;
		}

		// If this is a simple product, the "Submit" button has the product ID as "value", we need to include it explicitly
		data[ 'wcapp-add-to-cart' ] = $( '[name=add-to-cart]' ).val();

		$.ajax( {
			type:    'POST',
			data:    data,
			url:     wc_wcapp_generate_cart_context.ajaxurl,
			success: callback,
		} );
	};

	window.wc_wcapp_generate_cart = generate_cart;

	// Non-SPB mode click handler, namespaced as 'legacy' as it's replaced by `payment` callback of Button API.
	$( '#product_paypal_button' ).on( 'click.legacy', function( event ) {
		event.preventDefault();

		$( '#product_paypal_button' ).trigger( 'disable' );

		var href = $(this).attr( 'href' );

		if( $( ".single_add_to_cart_button" ).hasClass( "disabled" ) ) {
			$( ".single_add_to_cart_button" ).trigger( "click" );
			return;
		}
		generate_cart( function() {
			window.location.href = wc_wcapp_generate_cart_context.checkout_url;
		} );
	} );

})( jQuery, window, document );
