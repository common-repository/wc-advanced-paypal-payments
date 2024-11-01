jQuery(function(e){
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_payment_action")
		.on("change", (function() {
			"AUTHORIZE" == e(this)
				.val() ? e("#woocommerce_advanced-paypal-payments-for-woocommerce_instant_payments")
				.closest("tr")
				.hide() : e("#woocommerce_advanced-paypal-payments-for-woocommerce_instant_payments")
				.closest("tr")
				.show()
		}))
		.change(), e("#woocommerce_advanced-paypal-payments-for-woocommerce_ipn_notification")
		.on("change", (function() {
			1 == e(this)
				.is(":checked") ? e("#woocommerce_advanced-paypal-payments-for-woocommerce_ipn_notification_email")
				.closest("tr")
				.show() : e("#woocommerce_advanced-paypal-payments-for-woocommerce_ipn_notification_email")
				.closest("tr")
				.hide()
		}))
		.change(), e("#woocommerce_advanced-paypal-payments-for-woocommerce_env")
		.on("change", (function() {
			var o = e(this)
				.val(),
				c = e("h3.sandbox"),
				t = e("h3.live");
			"live" == o ? (c.hide()
				.next()
				.hide()
				.next()
				.hide(), t.show()
				.next()
				.show()
				.next()
				.show()) : "sandbox" == o && (c.show()
				.next()
				.show()
				.next()
				.show(), t.hide()
				.next()
				.hide()
				.next()
				.hide())
		}))
		.change(), e("#woocommerce_advanced-paypal-payments-for-woocommerce_on_checkout")
		.on("change", (function() {
			e(this)
				.is(":checked") ? e("#woocommerce_advanced-paypal-payments-for-woocommerce_gateway_description")
				.closest("tr")
				.show() : e("#woocommerce_advanced-paypal-payments-for-woocommerce_gateway_description")
				.closest("tr")
				.hide()
		}))
		.change()
   
   if(getUrlParam("section") == "advanced-paypal-payments-for-woocommerce" && getUrlParam("tab") == "checkout" && getUrlParam("page") == "wc-settings"){
       e("p.submit").hide();
   }
    
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_new_save").click(function(){
      e(".submit .woocommerce-save-button").click();
   });
   
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_resubscribe_webhooks").click(function(){
	  e("#woocommerce_advanced-paypal-payments-for-woocommerce_resubscribe_webhooks").after('<input class="" type="text" name="hidden_resubscribe_webhooks" id="hidden_resubscribe_webhooks" style="display:none;" value="Resubscribe">');
      e(".submit .woocommerce-save-button").click();
   });
    
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_connection").click(function(){
      if(e(".tab-pnx-selected").attr("id") == "woocommerce_advanced-paypal-payments-for-woocommerce_connection"){ return; }
      e(this).addClass("tab-pnx-selected");
      e("#woocommerce_advanced-paypal-payments-for-woocommerce_payments_settings").removeClass("tab-pnx-selected");
      e(".block-pnx-show").addClass("block-pnx-middle");
      e(".block-pnx-hide").removeClass("block-pnx-hide").addClass("block-pnx-show");
      e(".block-pnx-middle").removeClass("block-pnx-middle").removeClass("block-pnx-show").addClass("block-pnx-hide"); 
   });
    
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_payments_settings").click(function(){
      if(e(".tab-pnx-selected").attr("id") == "woocommerce_advanced-paypal-payments-for-woocommerce_payments_settings"){ return; }
      e(this).addClass("tab-pnx-selected");
      e("#woocommerce_advanced-paypal-payments-for-woocommerce_connection").removeClass("tab-pnx-selected");
      e(".block-pnx-show").addClass("block-pnx-middle");
      e(".block-pnx-hide").removeClass("block-pnx-hide").addClass("block-pnx-show");
      e(".block-pnx-middle").removeClass("block-pnx-middle").removeClass("block-pnx-show").addClass("block-pnx-hide"); 
   });
    
   if((e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_production").val() != "" && e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_production").val() != "" && e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_production").val() != "") || (e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_sandbox").val() != "" && e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_sandbox").val() != "" && e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_sandbox").val() != "")){
      
   }else{
      e(".tab-pnx-unselect").eq(0).click();
   }
   if(e("#woocommerce_advanced-paypal-payments-for-woocommerce_current_webhook_status").val()=='no' || e("#woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks").val()=="" || e("#woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks").val()==undefined){
   		e(".tab-pnx-unselect").eq(0).click();
   }
   
   function setCurrentAccount(){
      let env_mode = e("#woocommerce_advanced-paypal-payments-for-woocommerce_env").find("option:selected").text();
      let env_mode_val = e("#woocommerce_advanced-paypal-payments-for-woocommerce_env").val();
      let email_address;
      let client_id;
      let secret_key;
      let env_mode_title;
      let email_address_title;
      let client_id_title;
      let secret_key_title;
      env_mode_title = e("#woocommerce_advanced-paypal-payments-for-woocommerce_env").parent("fieldset").parent("td").prev().find("label").text().trim()+"：";
      if(env_mode_val == 'sandbox'){
          email_address = e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_sandbox").val();
          client_id = e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_sandbox").val();
          secret_key = e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_sandbox").val() == '' ? '' : '********************************************************************************';
          email_address_title = e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_sandbox").parent("fieldset").parent("td").prev().find("label").text().replace(jQuery("#woocommerce_advanced-paypal-payments-for-woocommerce_env").find("option:selected").text(),"").trim()+"：";
          client_id_title = e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_sandbox").parent("fieldset").parent("td").prev().find("label").text().replace(jQuery("#woocommerce_advanced-paypal-payments-for-woocommerce_env").find("option:selected").text(),"").trim()+"：";
          secret_key_title = e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_sandbox").parent("fieldset").parent("td").prev().find("label").text().replace(jQuery("#woocommerce_advanced-paypal-payments-for-woocommerce_env").find("option:selected").text(),"").trim()+"：";
       }else if(env_mode_val == 'live'){
          email_address = e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_production").val();
          client_id = e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_production").val();
          secret_key = e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_production").val() == '' ? '' : '********************************************************************************';
          email_address_title = e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_production").parent("fieldset").parent("td").prev().find("label").text().replace(jQuery("#woocommerce_advanced-paypal-payments-for-woocommerce_env").find("option:selected").text(),"").trim()+"：";
          client_id_title = e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_production").parent("fieldset").parent("td").prev().find("label").text().replace(jQuery("#woocommerce_advanced-paypal-payments-for-woocommerce_env").find("option:selected").text(),"").trim()+"：";
          secret_key_title = e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_production").parent("fieldset").parent("td").prev().find("label").text().replace(jQuery("#woocommerce_advanced-paypal-payments-for-woocommerce_env").find("option:selected").text(),"").trim()+"：";
       }
       e("#woocommerce_advanced-paypal-payments-for-woocommerce_current-account").html("<table id=\"current-account-table\"><tbody><tr><th>"+env_mode_title+"</th><td>"+env_mode+"</td></tr><tr><th>"+email_address_title+"</th><td>"+email_address+"</td></tr><tr><th>"+client_id_title+"</th><td>"+client_id+"</td></tr><tr><th>"+secret_key_title+"</th><td>"+secret_key+"</td></tr></tbody></table>");
   }
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_env").on("change", (function() {
      setCurrentAccount();
   })).change();
    
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_sandbox").on("change", (function() {
      setCurrentAccount();
   })).change();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_sandbox").on("change", (function() {
      setCurrentAccount();
   })).change();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_sandbox").on("change", (function() {
      setCurrentAccount();
   })).change();
   
   
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_production").on("change", (function() {
      setCurrentAccount();
   })).change();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_production").on("change", (function() {
      setCurrentAccount();
   })).change();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_production").on("change", (function() {
      setCurrentAccount();
   })).change();
   
   buildTableByJSON();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_resubscribe_webhooks").removeClass("regular-input");
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_resubscribe_webhooks").val(wc_wcapp_admin_context.resubscribe_button);
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").removeClass("regular-input");
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").val(e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").parent().parent().parent().find(".titledesc").find("label").html().trim());
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").parent().parent().prev().remove();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").removeClass("regular-input");
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").val(e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").parent().parent().parent().find(".titledesc").find("label").html().trim());
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").parent().parent().prev().remove();
   //e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_manual_credential_input").removeClass("regular-input");
   //e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_manual_credential_input").val(e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_manual_credential_input").parent().parent().parent().find(".titledesc").find("label").html().trim());
	//e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_manual_credential_input").parent().parent().prev().remove();
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_third_party").removeClass("regular-input");
    e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_third_party").val(e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_third_party").parent().parent().parent().find(".titledesc").find("label").html().trim());
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_third_party").parent().parent().prev().remove();
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_disconnect_from_paypal").removeClass("regular-input");
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_disconnect_from_paypal").val(wc_wcapp_admin_context.disconnect_account_label);
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_disconnect_from_paypal").before("<p style=\"color: green;\">"+wc_wcapp_admin_context.connect_status_label+"</p>");
   
	function buildTableByJSON(){
    if((e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_production").val() != "" && e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_production").val() != "" && e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_production").val() != "") || (e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_email_sandbox").val() != "" && e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_id_sandbox").val() != "" && e("#woocommerce_advanced-paypal-payments-for-woocommerce_client_secret_sandbox").val() != "")){
        if(e("#woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks").val()=="" || e("#woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks").val()==undefined){
			 e("#woocommerce_advanced-paypal-payments-for-woocommerce_current_webhook_status").after("<c style=\"color:red\">"+wc_wcapp_admin_context.subscribed_failed_label+"</c>");
            e("#woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks").parent().parent().parent().hide();
        }else{
            e("#woocommerce_advanced-paypal-payments-for-woocommerce_current_webhook_status").after("<c style=\"color:green\">"+wc_wcapp_admin_context.subscribed_success_label+"</c>");
            let jsonObj = e.parseJSON(e("#woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks").val());
            let tableHtml = "<table id=\"subscribed_webhooks_table\"><tbody><tr><th>URL</th><th>Tracked events</th></tr>";
			tableHtml += "<tr><td>"+jsonObj.url+"</td><td>"+jsonObj.subscribed_webhooks.split(",").join(",<br/>")+"</td>";
            tableHtml += "</tbody></table>";
            e("#woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks").after(tableHtml);
            e("#woocommerce_advanced-paypal-payments-for-woocommerce_subscribed_webhooks").hide();
        }
    }else{
    	e("#woocommerce_advanced-paypal-payments-for-woocommerce_webhook_status").hide();
        e("#woocommerce_advanced-paypal-payments-for-woocommerce_webhook_status").next().hide();
        e("#woocommerce_advanced-paypal-payments-for-woocommerce_webhook_status").next().next().hide();
    }
    e("#woocommerce_advanced-paypal-payments-for-woocommerce_current_webhook_status").hide();
	}
    
   if(e("#woocommerce_advanced-paypal-payments-for-woocommerce_payments_settings").html() == "支付选项"){
   		e("#woocommerce_advanced-paypal-payments-for-woocommerce_payments_settings").css("left", "186px");
   }
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_id_sandbox").parent().parent().parent().hide();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_merchant_id_production").parent().parent().parent().hide();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_webhook_id_sandbox").parent().parent().parent().hide();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_webhook_id_live").parent().parent().parent().hide();
    
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_payment_action").parent().parent().parent().hide();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_instant_payments").parent().parent().parent().parent().hide();
   
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_connect_status").parent().parent().parent().hide();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_shared_id").parent().parent().parent().hide();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_auth_code").parent().parent().parent().hide();
   e("#woocommerce_advanced-paypal-payments-for-woocommerce_seller_nonce").parent().parent().parent().hide();
   
  
	function getPaypalUrl(){
		let param = { 
			return_url: wc_wcapp_admin_context.return_url,
			website_name: wc_wcapp_admin_context.brand_name, 
			website_url: wc_wcapp_admin_context.website_url
		};
		e.ajax({
			//url: "https://paypal.uin88.com/marketplace/signup3.04-test.php",
			url: "https://paypal.uin88.com/marketplace/signup3.04.php",
			type: "POST",
			async: true,
			dataType : "jsonp",  
			jsonpCallback: 'success_jsonpCallback_PaypalUrl',
			data: param,
			success: function (data) {
				success_geturl(data);
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				console.log("err");
			}
		});
	}
	function success_jsonpCallback_PaypalUrl(data){		
    }
	function success_geturl(data){		
		console.log(data);
		if(data.ack){
			e("#signUpPaypalLive").attr("href",data.live.redirect_url+"&displayMode=minibrowser");
			e("#signUpPaypalLive").data("seller_nonce",data.live.seller_nonce);
			e("#signUpPaypal").attr("href",data.sandbox.redirect_url+"&displayMode=minibrowser");
			e("#signUpPaypal").data("seller_nonce",data.sandbox.seller_nonce);
		}else{
			//https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js
			//哪个js如果没加载成，404了，则报出一个提示
		}
    }

	e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").click(function(){
		if(e("#signUpPaypalLive").attr("href").indexOf("#pnx")==-1){
			document.getElementById("signUpPaypalLive").click();	
		}
	});

	e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").click(function(){
		if(e("#signUpPaypal").attr("href").indexOf("#pnx")==-1){
			document.getElementById("signUpPaypal").click();
		}
	});
	
	if(e("#woocommerce_advanced-paypal-payments-for-woocommerce_connect_status").val()=='no'){
   		e(".tab-pnx-unselect").eq(0).click();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_payments_settings").hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_account_setup").next().hide().next().hide().next().hide().next().hide().next().hide().next().hide().next().hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_webhook_status").hide().next().hide().next().hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_disconnect_from_paypal").parent().parent().parent().hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_new_save").hide();
		getPaypalUrl();
	}else{
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").parent().parent().parent().hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").parent().parent().parent().hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_manual_credential_input").parent().parent().parent().hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_toggle_to_third_party").parent().parent().parent().hide();
	}
	
	e("#toggleToManual").attr("href","javascript:void(0);").click(function(){
		if(e("#woocommerce_advanced-paypal-payments-for-woocommerce_env").parent().parent().parent().parent().parent().is(':hidden')){
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_account_setup").next().show();
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_env").trigger("change");
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_new_save").show();
		}else{
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_account_setup").next().hide().next().hide().next().hide().next().hide().next().hide().next().hide().next().hide();
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_webhook_status").hide().next().hide().next().hide();
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_new_save").hide();
		}
	});
	
	e("#toggleToThirdParty").attr("href","javascript:void(0);").click(function(){
		if(e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").parent().parent().parent().is(':hidden')){
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").parent().parent().parent().show();
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").parent().parent().parent().show();
		}else{
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").parent().parent().parent().hide();
			e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").parent().parent().parent().hide();
		}
	});
	
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_disconnect_from_paypal").click(function(){
      if (confirm(wc_wcapp_admin_context.disconnect_confirm_label)){ 
			let param = { 
				disconnect: "yes"
			};
			jQuery.ajax({
				url: wc_wcapp_admin_context.disconnect_url,
				type: "POST",
				data: param,
				success: function (data) {
					window.location=wc_wcapp_admin_context.plugin_url;
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					console.log("err");
				}
			});
      }
	});
	
	let intervalOfReload = setInterval(function(){ 
		if(window.onbeforeunload!=null){
			window.onbeforeunload=null;
			clearInterval(intervalOfReload);
		}
	}, 300);
	
	showManual();
	
	function showManual(){
		e("#toggleToThirdParty").parent().css("width","400px");
		e("#signUpPaypal").parent().css("width","400px");
		e("#toggleToThirdParty").parent().parent().parent().css("padding-left","0");
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").parent().parent().css("padding-left","0");
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").parent().parent().css("padding-left","0");
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_activate_paypal").parent().parent().parent().hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_test_payments_with_paypal_sandbox").parent().parent().parent().hide();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_account_setup").next().show();
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_env").trigger("change");
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_new_save").show();
	}
	
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_disconnect_from_paypal").parent().parent().prev().find("label").attr("for", "");
	e("#woocommerce_advanced-paypal-payments-for-woocommerce_resubscribe_webhooks").parent().parent().prev().find("label").attr("for", "");
	
	if(e("#woocommerce_advanced-paypal-payments-for-woocommerce_is_third_party_mode").val()=='yes'){
		e("#woocommerce_advanced-paypal-payments-for-woocommerce_seller_protection").attr("disabled","disabled");
	}

	e("input[name='woocommerce_advanced-paypal-payments-for-woocommerce_advanced_card_processing']").click(function(){
		if(e(this).get(0).checked==true){
			if(!confirm(wc_wcapp_admin_context.advanced_credit_card_confirm_label)){
				e(this).get(0).checked=false;
			}
		}
	});
	
	e("input[name='woocommerce_advanced-paypal-payments-for-woocommerce_apple_pay_checked']").click(function(){
		if(e(this).get(0).checked==true){
			if(!confirm(wc_wcapp_admin_context.apple_pay_confirm_label)){
				e(this).get(0).checked=false;
			}
		}
	});
	
	e("input[name='woocommerce_advanced-paypal-payments-for-woocommerce_google_pay_checked']").click(function(){
		if(e(this).get(0).checked==true){
			if(!confirm(wc_wcapp_admin_context.google_pay_confirm_label)){
				e(this).get(0).checked=false;
			}
		}
	});
	
});

function onboardedLiveCallback(authCode, sharedId) {
   let param = { 
      env: "live", 
      shared_id: sharedId,
      auth_code: authCode, 
      seller_nonce: jQuery("#signUpPaypalLive").data("seller_nonce")
   };
   jQuery.ajax({
      url: wc_wcapp_admin_context.ajax_credential_url,
      type: "POST",
      data: param,
      success: function (data) {
         console.log(data);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
         console.log("err");
      }
   });
}

function onboardedCallback(authCode, sharedId) {
   let param = { 
      env: "sandbox", 
      shared_id: sharedId,
      auth_code: authCode, 
      seller_nonce: jQuery("#signUpPaypal").data("seller_nonce")
   };
   jQuery.ajax({
      url: wc_wcapp_admin_context.ajax_credential_url,
      type: "POST",
      data: param,
      success: function (data) {
         console.log(data);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
         console.log("err");
      }
   });
}

function getUrlParam(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null){return unescape(r[2]);}
    return null;
}