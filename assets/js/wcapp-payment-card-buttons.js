; (function ($, window, document) {
  'use strict';

async function createOrderCallback() {
  //order-pay-page
  if(wc_wcapp_context_checkout.is_checkout_pay_page!='1')
  {
	  try {
		  $("input[name='nonce']").remove();
		  var validate_formdata = $('form[name=checkout]')
						  .append($('<input type="hidden" name="nonce" /> ')
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
				$.each(validateData.messages,function(index,value){
				   messages+='<li>'+value+'</li>';
				});
				$('.woocommerce-info').html('<ul class="woocommerce-error" role="alert"><a href="top"></a>' + messages + '</ul>').show();
				location.href="#top";
				return;
			}
	  } catch (error) {
		console.error(error);
		resultMessage(`Could not initiate PayPal Checkout...<br>${error}`);
	  }	
  }  
				  
  try {
	$("input[name='nonce']").remove();
	var formdata='';
	if(wc_wcapp_context_checkout.is_checkout_pay_page!='1'){
		//checkout-page
		formdata = $('form[name=checkout]')
					  .append($('<input type="hidden" name="nonce" /> ')
						.attr( 'value', wc_wcapp_context_checkout.create_order_nonce )
					  )
					  .serialize();
	}else{
		//order-pay-page
		formdata = $('form[id=order_review]').eq(0)
             .append($('<input type="hidden" name="key" />').attr('value', wc_wcapp_context_checkout.key))
				  .append($('<input type="hidden" name="nonce" />').attr( 'value', wc_wcapp_context_checkout.create_order_for_order_pay_nonce))
				  .serialize();
	}
    const response = await fetch(wc_wcapp_context_checkout.ajaxurl, {
      method: "POST",
      headers: {
        "Content-Type": 'application/x-www-form-urlencoded',
      },
      // use the "body" param to optionally pass additional order information
      // like product ids and quantities
      body: formdata,
    });

    const orderData = await response.json();

    if (orderData.data.id) {
      return orderData.data.id;
    } else {
      /*const errorDetail = orderData?.details?.[0];
      const errorMessage = errorDetail
        ? `${errorDetail.issue} ${errorDetail.description} (${orderData.debug_id})`
        : JSON.stringify(orderData);*/

      //throw new Error(errorMessage);
	  const details = orderData.details;
	  resultMessage(`Could not initiate PayPal Checkout...<br>${details}`);
    }
  } catch (error) {
    console.error(error);
    resultMessage(`Could not initiate PayPal Checkout...<br>${error}`);
  }
}

async function onApproveCallback(data, actions) {
  try {
	$("input[name='nonce']").remove();
    if(wc_wcapp_context_checkout.is_checkout_pay_page!='1'){	
	    var capture_formdata = $('form[name=checkout]')
				  .append($('<input type="hidden" name="nonce" /> ')
					.attr( 'value', wc_wcapp_context_checkout.capture_nonce )
				  )
				  .append($('<input type="hidden" name="order_id" /> ')
					.attr( 'value', data.orderID )
				  )
				  .serialize();
    }else{
        var capture_formdata = $('form[id=order_review]').eq(0)
				  .append($('<input type="hidden" name="nonce" /> ')
					.attr( 'value', wc_wcapp_context_checkout.capture_nonce )
				  )
				  .append($('<input type="hidden" name="order_id" /> ')
					.attr( 'value', data.orderID )
				  )
				  .serialize();
    }		
    const response = await fetch(wc_wcapp_context_checkout.capture_url, {
      method: "POST",
      headers: {
        "Content-Type": 'application/x-www-form-urlencoded',
      },
	  body: capture_formdata,
    });

    const orderData = await response.json();
    // Three cases to handle:
    //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
    //   (2) Other non-recoverable errors -> Show a failure message
    //   (3) Successful transaction -> Show confirmation or thank you message
    
    /*const transaction =
      orderData?.data?.purchase_units?.[0]?.payments?.captures?.[0] ||
      orderData?.data?.purchase_units?.[0]?.payments?.authorizations?.[0];
    const errorDetail = orderData?.data?.details?.[0];*/
	
	var transaction = "";
    var errorDetail = "";
	try{
		transaction = orderData.data.purchase_units[0].payments.captures[0] || orderData.data.purchase_units[0].payments.authorizations[0];
        errorDetail = orderData.data.details[0];
	}catch(err){
		
	}
	var issue="";
	if(errorDetail && errorDetail.issue){
		issue=errorDetail.issue;
	}

    // this actions.restart() behavior only applies to the Buttons component
    if (issue === "INSTRUMENT_DECLINED" && !data.card && actions) {
      // (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
      // recoverable state, per https://developer.paypal.com/docs/checkout/standard/customize/handle-funding-failures/
      return actions.restart();
    } else if (
      errorDetail ||
      !transaction ||
      transaction.status === "DECLINED"
    ) {
      // (2) Other non-recoverable errors -> Show a failure message
      let errorMessage;
      if (transaction) {
        errorMessage = `Transaction ${transaction.status}: ${transaction.id}`;
      } else if (errorDetail) {
        errorMessage = `${errorDetail.description} (${orderData.debug_id})`;
      } else {
        errorMessage = JSON.stringify(orderData);
      }

      //throw new Error(errorMessage);
	  resultMessage(
		`Sorry, your transaction could not be processed...<br>${errorMessage}`,
	  );
    } else {
      // (3) Successful transaction -> Show confirmation or thank you message
      // Or go to another URL:  actions.redirect('thank_you.html');
      /*resultMessage(
        `Transaction ${transaction.status}: ${transaction.id}<br>See console for all available details`,
      );*/
      console.log(
        "Capture result",
        orderData,
        JSON.stringify(orderData, null, 2),
      );
      
	  if(orderData.data.status=='COMPLETED'){
		  //order-pay-page
		  if(wc_wcapp_context_checkout.is_checkout_pay_page!='1')
		  {
			  $( 'form.checkout' )
							.append( $( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', orderData.data.id ) )
							.append( $( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', 'CAPTURE' ) )
							.append( $( '<input type="hidden" name="invoice_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].invoice_id ) )
							.append( $( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].id ) )
							.append( $( '<input type="hidden" name="payment_source" /> ' ).attr( 'value', 'card' ) )
							.append( $( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(orderData)))
							.trigger( 'submit' );
			  return;
		  }else{
			  $('form[id=order_review]').eq(0)
					  .append( $( '<input type="hidden" name="transaction_id" /> ' ).attr( 'value', orderData.data.id ) )
					  .append( $( '<input type="hidden" name="paypal_intent" /> ' ).attr( 'value', 'CAPTURE' ) )
					  .append( $( '<input type="hidden" name="capture_id" /> ' ).attr( 'value', orderData.data.purchase_units[0].payments.captures[0].id ) )
					  .append( $( '<input type="hidden" name="payment_source" /> ' ).attr( 'value', 'card' ) )
					  .append( $( '<input type="hidden" name="paypal_return_detail" /> ' ).attr( 'value', JSON.stringify(orderData)))
				 .trigger( 'submit' );
			  return;
			  
		  }
	  }else{
		 resultMessage(
		  `Sorry, your transaction could not be processed...<br>${orderData.data.status}`,
		 ); 
	  }

    }
  } catch (error) {
    console.error(error);
    resultMessage(
      `Sorry, your transaction could not be processed...<br>${error}`,
    );
  }
}

/*window.paypal
  .Buttons({
    createOrder: createOrderCallback,
    onApprove: onApproveCallback,
  })
  .render("#paypal-button-container");*/
  
const cardStyle = {
	'input': {
		'padding': '1.0rem 0.75rem',
		'font-size': '0.96rem',
	},
};  

const cardField = window.paypal.CardFields({
  createOrder: createOrderCallback,
  onApprove: onApproveCallback,
  style: cardStyle,
});

// Render each field after checking for eligibility
if (cardField.isEligible() && wc_wcapp_context_checkout.advanced_card_processing == 'yes') {
  $(".wcapp_card_container").show();
  //const nameField = cardField.NameField();
  //nameField.render("#wcapp-card-name-field-container");

  const numberField = cardField.NumberField();
  numberField.render("#wcapp-card-number-field-container");

  const cvvField = cardField.CVVField();
  cvvField.render("#wcapp-card-cvv-field-container");

  const expiryField = cardField.ExpiryField({placeholder:"Expiry (MM/YY)"});
  expiryField.render("#wcapp-card-expiry-field-container");

  // Add click listener to submit button and call the submit function on the CardField component
  document
    .getElementById("wcapp-card-field-submit-button")
    .addEventListener("click", () => {
	  resultMessage("");
      cardField
        .submit()
        .catch((error) => {
		  var str=error.toString();
		  if(str.indexOf('"description":')>0){
			  var regex=new RegExp(/"description":"([^"]*)/);
			  var match=str.match(regex);
			  if (match) {
			     str="Error:"+match[0].substr(15);
			 }
		  }
          resultMessage(
            `Sorry, your transaction could not be processed...<br>${str}`,
          );
        });
    });
} else {
  // Hides card fields if the merchant isn't eligible
  document.querySelector("#wcapp-card-form").style = "display: none";
}

// Example function to show a result to the user. Your site's UI library can be used instead.
function resultMessage(message) {
  const container = document.querySelector("#wcapp-result-message");
  container.innerHTML = message;
}

})(jQuery, window, document);