<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Settings for Advanced PayPal Payments for WooCommerce.
 */
return apply_filters(
	'wcapp_setting_options',
	array(
    	  'connection'           => array(
            'title' => __( 'Connect PayPal', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'tab-pnx-unselect',
        ),
        'payments_settings'           => array(
            'title' => __( 'Payments Options', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'tab-pnx-unselect tab-pnx-selected',
        ),
        'tab-bottom-line'           => array(
            'title' => '',
            'type'  => 'title',
    		'class'  => 'tab-bottom-line',
        ),
			'original_account_setup'           => array(
            'title' => __( 'Account Setup', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-hide',
        ),
		/*'activate_paypal'           => array(
            'title' => __( 'Activate PayPal', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'button',
            'description' => __( '<a id="signUpPaypalLive" target="_blank" data-seller_nonce="" data-paypal-onboard-complete="onboardedLiveCallback" href="#" data-paypal-button="true">Sign up for PayPal Live</a>', 'advanced-paypal-payments-for-woocommerce' ),
    		'class'  => 'block-pnx-hide',
        ),
        'test_payments_with_paypal_sandbox'           => array(
            'title' => __( 'Test payments with PayPal sandbox', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'button',
            'description' => '<a id="signUpPaypal" target="_blank" data-seller_nonce="" data-paypal-onboard-complete="onboardedCallback" href="#" data-paypal-button="true">Sign up for PayPal</a>'.__( 'You can test payments in a safe PayPal sandbox environment.', 'advanced-paypal-payments-for-woocommerce' ),
    		'class'  => 'block-pnx-hide',
        ),
        'toggle_to_manual_credential_input'           => array(
            'title' => __( 'Toggle to manual credential input', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'button',
    			'description' => '<a id="toggleToManual" href="javascript:void(0);" data-paypal-button="true">'.__( 'Toggle to manual credential input', 'advanced-paypal-payments-for-woocommerce' ).'</a>',
    		'class'  => 'block-pnx-hide',
        ),*/
		'disconnect_from_paypal'           => array(
            'title' => __( 'PayPal status', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'button',
            'description' => __( 'Click to reset current credentials and use another account', 'advanced-paypal-payments-for-woocommerce' ),
    		'class'  => 'block-pnx-hide',
        ),
        'account_setup'           => array(
            'title' => __( 'Account Setup', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-hide',
        ),
        'env'                     => array(
            'title'   => __( 'Environment', 'advanced-paypal-payments-for-woocommerce' ),
            'type'    => 'select',
            'label'   => __( 'Choose whether to activate the plugin in live or sandbox mode', 'advanced-paypal-payments-for-woocommerce' ),
            'options' => array(
                'live'    => __( 'Live', 'advanced-paypal-payments-for-woocommerce' ),
                'sandbox' => __( 'Sandbox', 'advanced-paypal-payments-for-woocommerce' ),
            ),
            'default' => 'live',
            'class'  => 'block-pnx-hide',
        ),
        'sandbox_api_credentials' => array(
            'title'       => __( 'Enter your sandbox credentials here and connect your PayPal account', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'title',
            'class'       => 'sandbox block-pnx-hide',
            'description' => __( 'You have to connect to PayPal. You can connect an existing account or create a new one', 'advanced-paypal-payments-for-woocommerce' ),
        ),
        'merchant_email_sandbox'    => array(
            'title' => __( 'Sandbox Email address', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
    			'class'  => 'block-pnx-hide',
    			'description'  => __( 'The email address of your PayPal account.', 'advanced-paypal-payments-for-woocommerce' ),
                'desc_tip'    => true,
        ),
        'merchant_id_sandbox'    => array(
            'title' => __( 'Sandbox Merchant ID', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
    			'class'  => 'block-pnx-hide',
    			'description'  => __( 'The merchant ID of your account', 'advanced-paypal-payments-for-woocommerce' ),
                'desc_tip'    => true,
        ),
        'client_id_sandbox'   => array(
            'title' => __( 'Sandbox Client ID', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
    			'class'  => 'block-pnx-hide',
    			'description'  => __( 'The client ID of your account', 'advanced-paypal-payments-for-woocommerce' ),
                'desc_tip'    => true,
        ),
        'client_secret_sandbox'     => array(
            'title'       => __( 'Sandbox Secret Key', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( 'If you process transactions on behalf of someone else\'s PayPal account, enter their email address or their protected merchant account ID (also known as payment ID) here. Generally, you must possess API authorizations of the other account to process any operation other than sale transactions.', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'password',
    			'class'  => 'block-pnx-hide',
    			'description'  => __( 'The secret key of your account', 'advanced-paypal-payments-for-woocommerce' ),
                'desc_tip'    => true,
        ),
        'live_api_credentials'    => array(
            'title'       => __( 'Enter your live credentials and connect your PayPal account', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'title',
            'class'       => 'live block-pnx-hide',
            'description' => __( 'You have to connect to PayPal. You can connect an existing account or create a new one', 'advanced-paypal-payments-for-woocommerce' ),
        ),
        'merchant_email_production'       => array(
            'title' => __( 'Live Email address', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
    			'class'  => 'block-pnx-hide',
    			'description'  => __( 'The email address of your PayPal account.', 'advanced-paypal-payments-for-woocommerce' ),
                'desc_tip'    => true,
        ),
        'merchant_id_production'       => array(
            'title' => __( 'Live Merchant ID', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
    			'class'  => 'block-pnx-hide',
    			'description'  => __( 'The merchant ID of your account', 'advanced-paypal-payments-for-woocommerce' ),
                'desc_tip'    => true,
        ),
        'client_id_production'      => array(
            'title' => __( 'Live Client ID', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
    			'class'  => 'block-pnx-hide',
    			'description'  => __( 'The client ID of your account', 'advanced-paypal-payments-for-woocommerce' ),
                'desc_tip'    => true,
        ),
        'client_secret_production'        => array(
            'title'       => __( 'Live Secret Key', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( 'If you process transactions on behalf of someone else\'s PayPal account, enter their email address or their protected merchant account ID (also known as payment ID) here. Generally, you must possess API authorizations of the other account to process any operation other than sale transactions.', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'password',
    			'class'  => 'block-pnx-hide',
    			'description'  => __( 'The secret key of your account', 'advanced-paypal-payments-for-woocommerce' ),
                'desc_tip'    => true,
        ),
		'toggle_to_third_party_title'           => array(
            'title' => __( 'toggle_to_third_party_title', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-hide',
        ),
		'toggle_to_third_party'           => array(
            'title' => __( 'Toggle to third party', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'button',
    			'description' => '<a id="toggleToThirdParty" href="javascript:void(0);" data-paypal-button="true">'.__( 'Toggle to automatically obtain credentials', 'advanced-paypal-payments-for-woocommerce' ).'</a>',
    		'class'  => 'block-pnx-hide',
        ),
		'activate_paypal'           => array(
            'title' => __( 'Activate PayPal', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'button',
            'description' => __( '<a id="signUpPaypalLive" target="_blank" data-seller_nonce="" data-paypal-onboard-complete="onboardedLiveCallback" href="#pnx" data-paypal-button="true">Sign up for PayPal Live</a>', 'advanced-paypal-payments-for-woocommerce' ),
    		'class'  => 'block-pnx-hide',
        ),
        'test_payments_with_paypal_sandbox'           => array(
            'title' => __( 'Test payments with PayPal sandbox', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'button',
            'description' => '<a id="signUpPaypal" target="_blank" data-seller_nonce="" data-paypal-onboard-complete="onboardedCallback" href="#pnx" data-paypal-button="true">Sign up for PayPal</a>'.__( 'You can test payments in a safe PayPal sandbox environment.', 'advanced-paypal-payments-for-woocommerce' ),
    		'class'  => 'block-pnx-hide',
        ),
        'webhook_status'           => array(
            'title' => __( 'Webhook Status', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
            'class'  => 'block-pnx-title block-pnx-hide',
            'description' => sprintf(__( 'Status of the webhooks subscription. More information about the webhooks is available in the %1$sWebhook Status documentation%2$s.', 'advanced-paypal-payments-for-woocommerce' ),'<a href="https://paypal.uin88.com/installation-manual/#Webhook_Status" target="_blank">','</a>'),
        ),
    	  'current_webhook_status'        => array(
            'title'       => __( 'Webhook status', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'text',
            'description' => '',
            'default'     => 'no',
    		'class'  => 'block-pnx-hide',
        ),
        'subscribed_webhooks'        => array(
            'title'       => __( 'Subscribed webhooks', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'table',
            'label'       => '',
            'description' => '',
    		'class'  => 'block-pnx-hide',
        ),
        'resubscribe_webhooks'        => array(
            'title'       => __( 'Resubscribe webhooks', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'button',
            'description' => __( 'Click to remove the current webhook subscription and subscribe again, for example, if the website domain or URL structure changed.', 'advanced-paypal-payments-for-woocommerce' ),
    		'class'  => 'resubscribe_webhooks_btn block-pnx-hide',
        ),
        'webhook_id_live'        => array(
            'title'       => __( 'Webhook ID Live', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'text',
    		'class'  => 'webhook_id_text block-pnx-hide',
        ),
        'webhook_id_sandbox'        => array(
            'title'       => __( 'Webhook ID Sandbox', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'text',
    		'class'  => 'webhook_id_text block-pnx-hide',
        ),
        'current-account'           => array(
            'title' => '',
            'type'  => 'title',
    		'class'  => 'current-account block-pnx-show',
        ),
        'general'           => array(
            'title' => __( 'General', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-show',
        ),
        'enabled'                 => array(
            'title'   => __( 'Enable/Disable', 'advanced-paypal-payments-for-woocommerce' ),
            'label'   => __( 'Enable Advanced PayPal Payments for WooCommerce', 'advanced-paypal-payments-for-woocommerce' ),
            'type'    => 'checkbox',
            'default' => 'no',
			'description' => __( 'In order to use PayPal or Advanced Card Processing, you need to enable the Plugin.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'button_ec'               => array(
            'title' => __( 'Choose where to show PayPal Express Checkout option', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-show',
        ),
        'on_cart_page'            => array(
            'title'       => __( 'Cart Page', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable PayPal checkout on the cart page.', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( 'Enable or disable the option to show PayPal Express Checkout button on the cart page', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
            'default'     => 'no',
    			'class'  => 'block-pnx-show',
        ),
        'on_single_product_page'  => array(
            'title'       => __( 'Single Product Page', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable PayPal checkout on single product page.', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( 'Show Express Checkout button on each single product page.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
            'default'     => 'no',
    			'class'  => 'block-pnx-show',
        ),
        'on_checkout'             => array(
            'title'       => __( 'Checkout Page', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable PayPal checkout on checkout page.', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( 'Show PayPal Express Checkout on checkout page.', 'advanced-paypal-payments-for-woocommerce' ),
            'default'     => 'yes',
    		'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'advanced_card'           => array(
            'title' => __( 'ADVANCED CREDIT CARD PROCESSING', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-show',
        ),
        'advanced_card_processing'             => array(
            'title'       => __( 'Advanced credit card', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enabled advanced credit card gateway', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( "Advanced credit and debit card functionality requires that <span style='color:red;'>your business account be evaluated and approved by PayPal</span>.", 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => false,
            'default'     => 'no',
    		'class'  => 'block-pnx-show',
        ),
        'apple_pay'           => array(
            'title' => __( 'Apple Pay', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-show',
        ),
        'apple_pay_checked'             => array(
            'title'       => __( 'Apple Pay', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enabled Apple Pay', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( "Apple Pay functionality requires that <span style='color:red;'>your business account be evaluated and approved by PayPal</span>.", 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => false,
            'default'     => 'no',
    		'class'  => 'block-pnx-show',
        ),
        'google_pay'           => array(
            'title' => __( 'Google Pay', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-show',
        ),
        'google_pay_checked'             => array(
            'title'       => __( 'Google Pay', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enabled Google Pay', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( "Google Pay functionality requires that <span style='color:red;'>your business account be evaluated and approved by PayPal</span>.", 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => false,
            'default'     => 'no',
    		'class'  => 'block-pnx-show',
        ),
        'seller_protection_title'               => array(
            'title' => __( 'Enable PayPal shipment tracking', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    			'class'  => 'block-pnx-title block-pnx-show',
        ),
        'seller_protection'             => array(
            'title'       => __( 'Enable/Disable', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Add tracking information for PayPal transactions.', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( "Before enabling the function, you need to activate the shipment tracking permission for your PayPal account. If you have not yet activated it, please visit <a href='https://www.paypal-techsupport.com/' target='_blank'>https://www.paypal-techsupport.com/</a> for help.", 'advanced-paypal-payments-for-woocommerce' ),
            'default'     => 'yes',
    			'desc_tip'    => false,
    			'class'  => 'block-pnx-show',
        ),
        'standard_settings'               => array(
            'title' => __( 'Standard Settings', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
    		'class'  => 'block-pnx-title block-pnx-show',
        ),
        'title'                   => array(
            'title'       => __( 'Title', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'text',
            'description' => __( 'This controls the title that users see during the checkout.', 'advanced-paypal-payments-for-woocommerce' ),
            'default'     => __( 'PayPal', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    			'class'  => 'block-pnx-show',
        ),
        'gateway_description'     => array(
            'title'       => __( 'Description', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'text',
            'default'     => __( 'Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( 'This is the description that the customer will see on checkout page', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    			'class'  => 'block-pnx-show',
        ),
        'brand_name'              => array(
            'title'       => __( 'Brand Name', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'text',
    			'default'     =>  get_bloginfo( 'name' ),
            'description' => __( 'Enter the company/shop/website name as it will be shown on PayPal checkout.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'landing_page'          => array(
            'title'       => __( 'Landing Page', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'select',
            'options'     => array(
                'LOGIN'   => __( 'Login (PayPal account login)', 'advanced-paypal-payments-for-woocommerce' ),
                'BILLING' => __( 'Billing (No PayPal account)', 'advanced-paypal-payments-for-woocommerce' ),
            ),
            'default'     => 'LOGIN',
            'description' => __( 'Type of PayPal page to show.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'custom_button'           => array(
            'title' => __( 'Custom Button', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
            'class'  => 'block-pnx-title block-pnx-show',
        ),
        'button_label'            => array(
            'title'       => __( 'Button Label', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'select',
            'options'     => array(
                'checkout'       => __( 'PayPal Checkout', 'advanced-paypal-payments-for-woocommerce' ),
                'pay'            => __( 'Pay with PayPal', 'advanced-paypal-payments-for-woocommerce' ),
                'buynow'         => __( 'Buy Now', 'advanced-paypal-payments-for-woocommerce' ),
                'buynow-branded' => __( 'Buy Now (with PayPal logo)', 'advanced-paypal-payments-for-woocommerce' ),
                'paypal'         => __( 'PayPal', 'advanced-paypal-payments-for-woocommerce' ),
            ),
            'default'     => 'paypal',
            'description' => __( 'Pick the button label among the following ones provided by PayPal APIs.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'button_size'             => array(
            'title'       => __( 'Button Size', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'select',
            'options'     => array(
                'small'      => __( 'Small', 'advanced-paypal-payments-for-woocommerce' ),
                'medium'     => __( 'Medium', 'advanced-paypal-payments-for-woocommerce' ),
                'large'      => __( 'Large', 'advanced-paypal-payments-for-woocommerce' ),
                'responsive' => __( 'Responsive', 'advanced-paypal-payments-for-woocommerce' ),
            ),
            'default'     => 'medium',
            'description' => __( 'Select the button size.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'button_style'            => array(
            'title'       => __( 'Button Style', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'select',
            'options'     => array(
                'pill' => __( 'Rounded', 'advanced-paypal-payments-for-woocommerce' ),
                'rect' => __( 'Square', 'advanced-paypal-payments-for-woocommerce' ),
            ),
            'default'     => 'rect',
            'description' => __( 'Select the style of button.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'button_color'            => array(
            'title'       => __( 'Button Color', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'select',
            'options'     => array(
                'gold'   => __( 'Gold', 'advanced-paypal-payments-for-woocommerce' ),
                'blue'   => __( 'Blue', 'advanced-paypal-payments-for-woocommerce' ),
                'silver' => __( 'Silver', 'advanced-paypal-payments-for-woocommerce' ),
                'black'  => __( 'Black', 'advanced-paypal-payments-for-woocommerce' ),
            ),
            'default'     => 'gold',
            'description' => __( 'Select the color of button.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'other_setting'           => array(
            'title' => __( 'Other Settings', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
            'class'  => 'block-pnx-title block-pnx-show',
        ),
        'log_enabled'             => array(
            'title'       => __( 'Debug Log', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable logging.', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => '<a id="view_logs" target="_blank" href="'.esc_url(home_url().'/wp-admin/admin.php?page=wc-status&tab=logs').'">'.__( 'View Logs', 'advanced-paypal-payments-for-woocommerce' ).'</a>',
            'default'     => 'no',
    			'class'  => 'block-pnx-show',
        ),
        'invoice_prefix'          => array(
            'title'       => __( 'Invoice Prefix', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'text',
            'default'     => 'Inv-',
            'description' => __( 'Enter a prefix that will be attached to the invoice number. Useful if you have connected the same PayPal account on more shops.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
    		'class'  => 'block-pnx-show',
        ),
        'payment_action'          => array(
            'title'       => __( 'Payment Action', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'select',
            'class'       => 'payment_action block-pnx-show',
            'options'     => array(
                'AUTHORIZE' => __( 'Authorize', 'advanced-paypal-payments-for-woocommerce' ),
                'CAPTURE'          => __( 'Capture', 'advanced-paypal-payments-for-woocommerce' ),
            ),
            'default'     => 'CAPTURE',
            'description' => __( 'Choose whether to capture funds immediately or authorize the payment only.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
        ),
        'instant_payments'        => array(
            'title'       => __( 'Instant Payments', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Require Instant Payment.', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( 'Instant Payments option does not allow paying with echecks.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
            'default'     => 'yes',
    			'class'  => 'block-pnx-show',
        ),
        'card'        => array(
            'title'       => __( 'Show Funding Source(s)', 'advanced-paypal-payments-for-woocommerce' ),
            'type'        => 'checkbox',
            'label'       => __( 'Debit or Credit Card', 'advanced-paypal-payments-for-woocommerce' ),
            'description' => __( 'This controls the funding sources that users see during the checkout.', 'advanced-paypal-payments-for-woocommerce' ),
            'desc_tip'    => true,
            'default'     => 'yes',
    			'class'  => 'block-pnx-show',
        ),
        'venmo'        => array(
            'title'       =>  '',
            'type'        => 'checkbox',
            'label'       => __( 'Venmo', 'advanced-paypal-payments-for-woocommerce' ),
            'default'     => 'yes',
    			'class'  => 'block-pnx-show',
        ),
        'paylater'        => array(
            'title'       =>  '',
            'type'        => 'checkbox',
            'label'       => __( 'PayPal Later', 'advanced-paypal-payments-for-woocommerce' ),
            'default'     => 'yes',
    			'class'  => 'block-pnx-show',
        ),
        'new_save'           => array(
            'title' => __( 'Save', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'title',
            'class'  => 'new-pnx-save',
        ),
        'connect_status'           => array(
            'title' => __( 'Connect Status', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
			'default'  => 'no',
            'class'  => 'hidden-value',
        ),
        'shared_id'           => array(
            'title' => __( 'shared_id', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
            'class'  => 'hidden-value',
        ),
        'auth_code'           => array(
            'title' => __( 'auth_code', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
            'class'  => 'hidden-value',
        ),
        'seller_nonce'           => array(
            'title' => __( 'seller_nonce', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
            'class'  => 'hidden-value',
        ),
        'is_third_party_mode'           => array(
            'title' => __( 'is_third_party_mode', 'advanced-paypal-payments-for-woocommerce' ),
            'type'  => 'text',
            'class'  => 'hidden-value',
        ),
    )
);