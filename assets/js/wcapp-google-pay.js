/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */


/**
 * An initialized google.payments.api.PaymentsClient object or null if not yet set
 * An initialized paypal.Googlepay().config() response object or null if not yet set
 *
 * @see {@link getGooglePaymentsClient}
 */
let paymentsClient = null, googlepayConfig = null;


/**
 * 
 * @returns Fetch the Google Pay Config From PayPal 
 */
async function getGooglePayConfig(){
  if(googlepayConfig === null){
    googlepayConfig = await paypal.Googlepay().config();
    console.log(" ===== Google Pay Config Fetched ===== ");

  }
  return googlepayConfig;
}

/**
 * Configure support for the Google Pay API
 *
 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#PaymentDataRequest|PaymentDataRequest}
 * @returns {object} PaymentDataRequest fields
 */
async function getGooglePaymentDataRequest() {
  const {allowedPaymentMethods,merchantInfo, apiVersion, apiVersionMinor , countryCode} = await getGooglePayConfig();
  const baseRequest = {
    apiVersion,
    apiVersionMinor
  }
  const paymentDataRequest = Object.assign({}, baseRequest);
  
  /*jQuery("input[name='nonce']").remove();
  var total_formdata = jQuery('form[name=checkout]')
					  .append(jQuery('<input type="hidden" name="nonce" /> ')
						.attr('value', wc_wcapp_context_checkout.total_nonce)
					  )
					  .serialize();  
	
  const total_response = await fetch(wc_wcapp_context_checkout.total_url, {
			  method: "POST",
			  headers: {
				"Content-Type": 'application/x-www-form-urlencoded',
			  },
			  body: total_formdata,
			});

  const totalData = await total_response.json();*/
  
  var cart_amount=jQuery(".order-total .woocommerce-Price-amount").text();
  if(cart_amount != ""){
     cart_amount=parseFloat(cart_amount).toString();
  }else{
	 cart_amount=wc_wcapp_context_checkout.order_total;
  }
  
  paymentDataRequest.allowedPaymentMethods = allowedPaymentMethods;
  paymentDataRequest.transactionInfo = getGoogleTransactionInfo(countryCode,cart_amount);
  paymentDataRequest.merchantInfo =merchantInfo;

  paymentDataRequest.callbackIntents = ["PAYMENT_AUTHORIZATION"];

  return paymentDataRequest;
}

  /**
 * Handles authorize payments callback intents.
 *
 * @param {object} paymentData response from Google Pay API after a payer approves payment through user gesture.
 * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentData object reference}
 *
 * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentAuthorizationResult}
 * @returns Promise<{object}> Promise of PaymentAuthorizationResult object to acknowledge the payment authorization status.
 */
  function onPaymentAuthorized(paymentData) {
    return new Promise(function(resolve, reject) {
      processPayment(paymentData)
        .then(function() {
          resolve({transactionState: 'SUCCESS'});
        })
        .catch(function() {
          resolve({transactionState: 'ERROR'});
        });
    });
  }
  

/**
 * Return an active PaymentsClient or initialize
 *
 * @see {@link https://developers.google.com/pay/api/web/reference/client#PaymentsClient|PaymentsClient constructor}
 * @returns {google.payments.api.PaymentsClient} Google Pay API client
 */
function getGooglePaymentsClient() {
  var env = wc_wcapp_context_checkout.env;
  if(env == 'sandbox'){
	env = 'TEST';
  }else{
	env = 'PRODUCTION';  
  }
  if (paymentsClient === null) {
    paymentsClient = new google.payments.api.PaymentsClient({
      environment: env,//'PRODUCTION''TEST'
      /*merchantInfo: {
		merchantName: wc_wcapp_context_checkout.blogname,
		merchantId: "XPTMWEM2CQWAE"
	  },*/
	  paymentDataCallbacks: {
        onPaymentAuthorized: onPaymentAuthorized
      }
    });
  }
  return paymentsClient;
}




/**
 * Initialize Google PaymentsClient after Google-hosted JavaScript has loaded
 *
 * Display a Google Pay payment button after confirmation of the viewer's
 * ability to pay.
 */
async function onGooglePayLoaded() {
  const paymentsClient = getGooglePaymentsClient();
  const { allowedPaymentMethods, apiVersion, apiVersionMinor } = await getGooglePayConfig();
  paymentsClient.isReadyToPay({allowedPaymentMethods, apiVersion,apiVersionMinor})
    .then(function(response) {
      if (response.result) {
        addGooglePayButton();
      }
    })
    .catch(function(err) {
      // show error in developer console for debugging
      console.error(err);
    });
}

/**
 * Add a Google Pay purchase button alongside an existing checkout button
 *
 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#ButtonOptions|Button options}
 * @see {@link https://developers.google.com/pay/api/web/guides/brand-guidelines|Google Pay brand guidelines}
 */
function addGooglePayButton() {
  const paymentsClient = getGooglePaymentsClient();
  const button =
    paymentsClient.createButton({
      onClick: onGooglePaymentButtonClicked
    });
  document.getElementById('wcapp_googlepay_container').appendChild(button);
}

/**
 * Provide Google Pay API with a payment amount, currency, and amount status
 *
 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#TransactionInfo|TransactionInfo}
 * @returns {object} transaction info, suitable for use as transactionInfo property of PaymentDataRequest
 */
function getGoogleTransactionInfo(countryCode,amount) {
  return {
    /*displayItems: [{
        label: "Subtotal",
        type: "SUBTOTAL",
        price: "0.09",
      },
      {
        label: "Tax",
        type: "TAX",
        price: "0.01",
      }
    ],*/
    countryCode: countryCode,
    currencyCode: wc_wcapp_context_checkout.currency,
    totalPriceStatus: "FINAL",
    totalPrice: amount,
    totalPriceLabel: "Total"
  };
}


/**
 * Show Google Pay payment sheet when Google Pay payment button is clicked
 */
async function onGooglePaymentButtonClicked() {
	if(wc_wcapp_context_checkout.page=='product'){
		document.getElementById("product_paypal_button").click();
		return;
	}
	if(wc_wcapp_context_checkout.page=='cart'){
		window.location.href=wc_wcapp_context_checkout.direct_checkout_url;
		return;
	}
	if(wc_wcapp_context_checkout.is_checkout_pay_page!='1')
    {
		//validate form field before click google pay 
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
    
  const paymentDataRequest = await getGooglePaymentDataRequest();
  const paymentsClient = getGooglePaymentsClient();
  paymentsClient.loadPaymentData(paymentDataRequest);
}

/**
 * Process payment data returned by the Google Pay API
 *
 * @param {object} paymentData response from Google Pay API after user approves payment
 * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentData|PaymentData object reference}
 */
async function processPayment(paymentData) {
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
	
  const resultElement = document.getElementById("result");
  const modal = document.getElementById("resultModal");
  resultElement.innerHTML = ""
  try {  
    //const { id } = await fetch(wc_wcapp_context_checkout.ajaxurl,{
	const createorder_response = await fetch(wc_wcapp_context_checkout.ajaxurl,{
      method:'POST',
      headers : {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
	  body: createorder_formdata,
    //}).then((res) => res.json());
	});
	const createorderData = await createorder_response.json();
	var id = "";
	if(createorderData.ack == true){
		id=createorderData.data.id;
	}else{
		throw new Error("error create order")
	}

  console.log(" ===== Order Created ===== ");
  /** Approve Payment */

  const {status} = await paypal.Googlepay().confirmOrder({
    orderId: id,
    paymentMethodData: paymentData.paymentMethodData
  });

    if(status === 'PAYER_ACTION_REQUIRED'){
          console.log(" ===== Confirm Payment Completed Payer Action Required ===== ")
          paypal.Googlepay().initiatePayerAction({orderId: id}).then( async () => {

                /**
                 *  GET Order 
                 */
                /*const orderResponse = await fetch(`/api/orders/${id}`, {
                  method: "GET"
                }).then(res =>res.json())

                console.log(" ===== 3DS Contingency Result Fetched ===== ");
                console.log(orderResponse?.payment_source?.google_pay?.card?.authentication_result)*/
                /*
                 * CAPTURE THE ORDER
                 */
                console.log(" ===== Payer Action Completed ===== ")

                modal.style.display = "none";//block
                resultElement.classList.add("spinner");
                /*const captureResponse = await fetch(`/api/orders/${id}/capture`, {
                  method: "POST"
                }).then(res =>res.json())*/
								
				const captureResponse = await fetch(wc_wcapp_context_checkout.capture_url, {
				method: "POST",
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
					body: new URLSearchParams({ order_id: id, nonce: wc_wcapp_context_checkout.capture_nonce }),
				});
				const orderData = await captureResponse.json();

                console.log(orderData);
				if(wc_wcapp_context_checkout.is_checkout_pay_page!='1')
		        {
					jQuery( 'form.checkout' )
						.append( jQuery( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', orderData.data.id ) )
						.append( jQuery( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', 'CAPTURE' ) )
						.append( jQuery( '<input type="hidden" name="invoice_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].invoice_id ) )
						.append( jQuery( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].id ) )
						.append( jQuery( '<input type="hidden" name="payment_source" /> ' ).attr( 'value', 'google_pay' ) )
						.append( jQuery( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(orderData)))
						.trigger( 'submit' );
				}else{
                    jQuery('form[id=order_review]').eq(0)
						.append( jQuery( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', orderData.data.id ) )
						.append( jQuery( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', 'CAPTURE' ) )
						.append( jQuery( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].id ) )
						.append( jQuery( '<input type="hidden" name="payment_source" /> ' ).attr( 'value', 'google_pay' ) )
						.append( jQuery( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(orderData)))
						.trigger( 'submit' );
				}					

                console.log(" ===== Order Capture Completed ===== ")
                resultElement.classList.remove("spinner");
                resultElement.innerHTML = prettyPrintJson.toHtml(captureResponse,{
                  indent: 2
                });
                

          })
    } else if (status === "APPROVED") {
        /*
         * CAPTURE THE ORDER
         */
        
        /*const response = await fetch(`/api/orders/${id}/capture`, {
                  method: "POST"
        }).then(res =>res.json())*/
		/*jQuery("input[name='nonce']").remove();
		jQuery("input[name='order_id']").remove();
		var capture_formdata = jQuery('form[name=checkout]')
						  .append(jQuery('<input type="hidden" name="nonce" /> ')
							.attr('value', wc_wcapp_context_checkout.create_order_nonce)
						  )
						  .append(jQuery('<input type="hidden" name="order_id" /> ')
							.attr('value', id)
						  )
						  .serialize();*/
		const captureResponse = await fetch(wc_wcapp_context_checkout.capture_url, {
			  method: "POST",
			  headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			  },
			  body: new URLSearchParams({ order_id: id, nonce: wc_wcapp_context_checkout.capture_nonce }),
			});
		const orderData = await captureResponse.json();

        console.log(orderData);
		if(wc_wcapp_context_checkout.is_checkout_pay_page!='1')
		{
			jQuery( 'form.checkout' )
				.append( jQuery( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', orderData.data.id ) )
				.append( jQuery( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', 'CAPTURE' ) )
				.append( jQuery( '<input type="hidden" name="invoice_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].invoice_id ) )
				.append( jQuery( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].id ) )
				.append( jQuery( '<input type="hidden" name="payment_source" /> ' ).attr( 'value', 'google_pay' ) )
				.append( jQuery( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(orderData)))
				.trigger( 'submit' );
		}else{
			jQuery('form[id=order_review]').eq(0)
				.append( jQuery( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', orderData.data.id ) )
				.append( jQuery( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', 'CAPTURE' ) )
				.append( jQuery( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].id ) )
				.append( jQuery( '<input type="hidden" name="payment_source" /> ' ).attr( 'value', 'google_pay' ) )
				.append( jQuery( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(orderData)))
				.trigger( 'submit' );
		}	

        console.log(" ===== Order Capture Completed ===== ")
        modal.style.display = "none";//block
        resultElement.innerHTML = prettyPrintJson.toHtml(captureResponse,{
          indent: 2
        });        
    }else {
		return { transactionState: "ERROR" };
	}
	

  return { transactionState: 'SUCCESS' }


} catch(err){
  return {
    transactionState: 'ERROR',
    error: {
      message: err.message
    }
  }
}
}

document.addEventListener("DOMContentLoaded", (event) => {
if (google && paypal.Googlepay) {
  onGooglePayLoaded().catch(console.log);
}
});
