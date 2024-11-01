/* global wc_wcapp_context_checkout */
; (function ($, window, document) {
  'use strict';
	
	$('input[name="payment_method"]').click(function(){
		var isWCAPP= $(this).is('#payment_method_advanced-paypal-payments-for-woocommerce');
		var toggleWCAPP=isWCAPP?'show':'hide';
		var toggleSubmit=isWCAPP?'hide':'show';
		$('#woo_wcapp_paypal_button').animate({opacity:toggleWCAPP,height:toggleWCAPP,padding:toggleWCAPP},230);
		$('#wcapp-card-form').animate({opacity:toggleWCAPP,height:toggleWCAPP,padding:toggleWCAPP},230);
		$('#place_order').animate({opacity:toggleSubmit,height:toggleSubmit,padding:toggleSubmit},230);
	});

	$('.et_pb_wc_order_pay').remove();
    
    setTimeout(function(){
		var paypal_order_id = "";
		
		var style_option={
			layout: 'vertical',
			color:  wc_wcapp_context_checkout.button_color,
			shape:  wc_wcapp_context_checkout.button_style,
			label:  wc_wcapp_context_checkout.button_label,
		};
		
		var height=0;
		if(wc_wcapp_context_checkout.button_size=='small')
		   height=25;
		else if(wc_wcapp_context_checkout.button_size=='medium')
		   height=40;
		else if(wc_wcapp_context_checkout.button_size=='large')
		   height=55;
		if(height>0)
			style_option.height=height;
		
		if(typeof(paypal) === 'undefined') {
            $('#woo_wcapp_paypal_button').html('<div style="padding:15px;color:#f00">Network error. Please reload the page and try again.</div>');
			return; 
        }
		var hasError=0;
		paypal.Buttons({
			style: style_option,
			onClick: function(data, actions) {
				return actions.resolve();
			}
			, createOrder: function (data, actions) {
             hasError=0;
			 var formdata = $('form[id=order_review]').eq(0)
             .append($('<input type="hidden" name="key" />').attr('value', wc_wcapp_context_checkout.key))
				  .append($('<input type="hidden" name="nonce" />').attr( 'value', wc_wcapp_context_checkout.create_order_for_order_pay_nonce))
				  .serialize();
				return fetch(
				  wc_wcapp_context_checkout.ajaxurl,
				  {
					method: 'post',
					cache: 'no-cache',
					credentials: 'same-origin',
					headers: {
					  'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: formdata
				  }
				).then(function (response) {
				  return response.json();
				}).then(function (resJson) {
				  paypal_order_id = resJson.data.id;
				  if(paypal_order_id==null){
					  var details=resJson.details;
					  if(details != "" && details != null){
						  var ul='<ul class="woocommerce-error" role="alert"><a href="top"></a>';
						  for(var i=0;i<details.length;i++){
							  var item=details[i];
							  ul+='<li>'+item.issue+' '+item.description+'</li>';
						  }
						  ul+='</ul>';
						  $('.woocommerce-info').html(ul).show();
					      location.href="#top";
						  hasError=1;
					  }
				  }
				  return paypal_order_id;
				});
			}
			, onApprove: function (data, actions) {
				return actions.order.capture().then(function (details) {
             var formdata = $('form[id=order_review]').eq(0)
				  .append( $( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', details.id ) )
				  .append( $( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', details.intent ) )
				  .append( $( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', details.purchase_units[0].payments.captures[0].id ) )
				  .append( $( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(details)))
             .trigger( 'submit' );
				  return;
				});
			}
			, onCancel: function (data) {
				alert("You have canceled the payment.");
			}
			, onError: function (err) {
				if(hasError==0){
					$('.woocommerce-info').html('<ul class="woocommerce-error" role="alert"><a href="top"></a><li>Something went wrong. Please try again or choose another payment source.</li></ul>').show();
					location.href="#top";
				}
			}
		}).render('#woo_wcapp_paypal_button');
		
		if(wc_wcapp_context_checkout.is_total_zero==1){
			$(".wcapp_paypal_button_wrapper").hide();
		}
	}, 1000);
  
})(jQuery, window, document);