
async function setupApplepay() {
  const applepay = paypal.Applepay();
    const {
      isEligible,
      countryCode,
      currencyCode,
      merchantCapabilities,
      supportedNetworks,
    } = await  applepay.config();

  if (!isEligible) {
    throw new Error("applepay is not eligible");
  }

  document.getElementById("wcapp_applepay_container").innerHTML =
    '<apple-pay-button id="btn-appl" buttonstyle="black" type="buy" locale="en">';

  document.getElementById("btn-appl").addEventListener("click", onClick);

  async function onClick() {
	if(wc_wcapp_context_checkout.page=='product'){
		document.getElementById("product_paypal_button").click();
		return;
	}
	if(wc_wcapp_context_checkout.page=='cart'){
		window.location.href=wc_wcapp_context_checkout.direct_checkout_url;
		return;
	}
	/*jQuery("input[name='nonce']").remove();
	var total_formdata = jQuery('form[name=checkout]')
					  .append(jQuery('<input type="hidden" name="nonce" /> ')
						.attr('value', wc_wcapp_context_checkout.order_total_nonce)
					  )
					  .serialize();  
	
	const total_response = await fetch(wc_wcapp_context_checkout.total_url, {
			  method: "POST",
			  headers: {
				"Content-Type": 'application/x-www-form-urlencoded',
			  },
			  body: total_formdata,
			});

	const totalData = await total_response.json();
	console.log("totalData："+totalData.amount+"+"+totalData.label);*/
	
	var cart_amount=jQuery(".order-total .woocommerce-Price-amount").text();
	if(cart_amount != ""){
		cart_amount=parseFloat(cart_amount).toString();
	}else{
		cart_amount=wc_wcapp_context_checkout.order_total;
	}
	  
	const paymentRequest = {
      countryCode,
      currencyCode: wc_wcapp_context_checkout.currency,
      merchantCapabilities,
      supportedNetworks,
      requiredBillingContactFields: [
        "name",
        "phone",
        "email",
        "postalAddress",
      ],
      requiredShippingContactFields: [
      ],
      total: {
        label: wc_wcapp_context_checkout.blogname,
        amount: cart_amount,
        type: "final",
      },
    };

    // eslint-disable-next-line no-undef
    let session = new ApplePaySession(4, paymentRequest); 
	  
    console.log({ merchantCapabilities, currencyCode, supportedNetworks })
    
	if(wc_wcapp_context_checkout.is_checkout_pay_page!='1')
    {
		//validate form field before click apple pay 
		try {
			  jQuery("input[name='nonce']").remove();
			  var validate_formdata = jQuery('form[name=checkout]')
							  .append(jQuery('<input type="hidden" name="nonce" /> ')
								.attr('value', wc_wcapp_context_checkout.validate_nonce)
							  )
							  .serialize();
			  const validate_response = await fetch(wc_wcapp_context_checkout.validate_url, {
				  method: "POST",
				  headers: {
					"Content-Type": 'application/x-www-form-urlencoded',
				  },
				  body: validate_formdata,
				});

				const validateData = await validate_response.json();	
				if (validateData.result=='error') {
					var messages='';
					jQuery.each(validateData.messages,function(index,value){
					   messages+='<li>'+value+'</li>';
					});
					jQuery('.woocommerce-info').html('<ul class="woocommerce-error" role="alert"><a href="top"></a>' + messages + '</ul>').show();
					location.href="#top";
					return;
				}
		} catch (error) {
			throw new Error("error validate")
		}
	}

    session.onvalidatemerchant = (event) => {
      applepay
        .validateMerchant({
          validationUrl: event.validationURL,
        })
        .then((payload) => {
          session.completeMerchantValidation(payload.merchantSession);
        })
        .catch((err) => {
          console.error(err);
          session.abort();
        });
    };

    session.onpaymentmethodselected = () => {
      session.completePaymentMethodSelection({
        newTotal: paymentRequest.total,
      });
    };
	
	session.onpaymentauthorized = (event) => {
		console.log('Your billing address is:', event.payment.billingContact);
		console.log('Your shipping address is:', event.payment.shippingContact);

		jQuery("input[name='nonce']").remove();
		var createorder_formdata='';
		if(wc_wcapp_context_checkout.is_checkout_pay_page!='1'){
			createorder_formdata = jQuery('form[name=checkout]')
							  .append(jQuery('<input type="hidden" name="nonce" /> ')
								.attr('value', wc_wcapp_context_checkout.create_order_nonce)
							  )
							  .serialize();
        }else{
            createorder_formdata = jQuery('form[id=order_review]').eq(0)
			                  .append(jQuery('<input type="hidden" name="key" /> ')
								.attr('value', wc_wcapp_context_checkout.key)
							  )
							  .append(jQuery('<input type="hidden" name="nonce" /> ')
								.attr('value', wc_wcapp_context_checkout.create_order_for_order_pay_nonce)
							  )
							  .serialize();
		}			
		
		fetch(wc_wcapp_context_checkout.ajaxurl,{
          method:'POST',
          headers : {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
		  body: createorder_formdata,
        })
		.then(res => res.json())
		.then((createOrderData) => {
          if(!createOrderData.ack) {	
            throw new Error("error creating order")
          }			
		  var orderId = createOrderData.data.id;
		  applepay.confirmOrder({
			orderId: orderId,
			token: event.payment.token,
			billingContact: event.payment.billingContact
		  })
		  .then(confirmResult => {
			session.completePayment(ApplePaySession.STATUS_SUCCESS);
			
			fetch(wc_wcapp_context_checkout.capture_url, {
			  method: 'POST',
			  headers : {
				'Content-Type': 'application/x-www-form-urlencoded'
			  },
			  body: new URLSearchParams({ order_id: orderId, nonce: wc_wcapp_context_checkout.capture_nonce }),
			})
			.then(res => res.json())
			.then(orderData => {
			//.then(function (orderData) {
			    console.log(orderData);
				if(wc_wcapp_context_checkout.is_checkout_pay_page!='1')
		        {
					jQuery( 'form.checkout' )
								.append( jQuery( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', orderData.data.id ) )
								.append( jQuery( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', 'CAPTURE' ) )
								.append( jQuery( '<input type="hidden" name="invoice_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].invoice_id ) )
								.append( jQuery( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].id ) )
								.append( jQuery( '<input type="hidden" name="payment_source" /> ' ).attr( 'value', 'apple_pay' ) )
								.append( jQuery( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(orderData)))
								.trigger( 'submit' );
					return;
				}else{
					jQuery('form[id=order_review]').eq(0)
								.append( jQuery( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', orderData.data.id ) )
								.append( jQuery( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', 'CAPTURE' ) )
								.append( jQuery( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].id ) )
								.append( jQuery( '<input type="hidden" name="payment_source" /> ' ).attr( 'value', 'apple_pay' ) )
								.append( jQuery( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(orderData)))
								.trigger( 'submit' );
					return;
				}
			})
			.catch(captureError => console.error(captureError));
		  })
		  .catch(confirmError => {
			if (confirmError) {
			  console.error('Error confirming order with applepay token');
			  console.error(confirmError);
			  session.completePayment(ApplePaySession.STATUS_FAILURE);
			}
		  });
		});
	};

    session.oncancel  = () => {
      console.log("Apple Pay Cancelled !!")
    }

    session.begin();
  }
}

document.addEventListener("DOMContentLoaded", () => {

  // eslint-disable-next-line no-undef
  //if(ApplePaySession?.supportsVersion(4) && ApplePaySession?.canMakePayments()) {
    setupApplepay().catch(console.error);
  //}
});
