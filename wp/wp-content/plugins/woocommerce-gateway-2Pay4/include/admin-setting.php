<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * use rows
 *  title
 *  type
 *  description
 *  default
 *  desc_tip - show question mark
 *  placeholder
 *  label
 *
 */
/**
 * Settings for 2Pay4 Gateway.
 */
return array(

    // enabled/disabled plugin
    'enabled' => array(
        'title'     => __( 'Enable/Disable', 'woocommerce'),
        'type'      => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_CHECKBOX,
        'label'     => __( 'Enable 2Pay4', 'woocommerce'),
        'default'   => WC_Gateway_2Pay4::_YES
    ),

    // title which the user sees during checkout
    'title' => array(
        'title'         => __( 'Title', 'woocommerce'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_TEXT,
        'description'   => __( 'This controls the title which the user sees during checkout.', 'woocommerce'),
        'default'       => WC_Gateway_2Pay4::__('2Pay4 Payment Solutions')
    ),

    // description which the user sees during checkout
    'description' => array(
        'title'         => __('Description', 'woocommerce'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_TEXTAREA,
        'description'   => __( 'This controls the description which the user sees during checkout.', 'woocommerce'),
        'default'       => WC_Gateway_2Pay4::__('Pay using 2Pay4 Payment Solutions')
    ),

    // Enabled/disabled show icon which the user sees during checkout
    'showicon'  => array(
        'title'     => WC_Gateway_2Pay4::__( 'Show icon'),
        'type'      => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_CHECKBOX,
        'label'     => WC_Gateway_2Pay4::__( 'Show icon'),
        'default'   => WC_Gateway_2Pay4::_YES
    ),

    // Enabled/disabled help page which the user sees during checkout
    'showhelppage'  => array(
        'title'         => WC_Gateway_2Pay4::__( 'Show help page'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_CHECKBOX,
        'label'         => WC_Gateway_2Pay4::__( 'Show help page'),
        'description'   => WC_Gateway_2Pay4::__( 'Description Show help page'),
        'default'       => WC_Gateway_2Pay4::_YES,
        'desc_tip'      =>  true,
    ),

    // rename button payment which the user sees during checkout
    'renamebuttonpayment'   =>  array(
        'title'         => WC_Gateway_2Pay4::__( 'Rename button payment'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_CHECKBOX,
        'label'         => WC_Gateway_2Pay4::__( 'rename button payment which the user sees during checkout'),
        'description'   => WC_Gateway_2Pay4::__( 'Description rename button payment which the user sees during checkout'),
        'default'       => WC_Gateway_2Pay4::_YES,
        'desc_tip'      =>  true,
    ),

    // Enabled/disabled test server
    'testmode' => array(
        'title'       => WC_Gateway_2Pay4::__( '2Pay4 Sandbox'),
        'type'        => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_CHECKBOX,
        'label'       => WC_Gateway_2Pay4::__( 'Enable 2Pay4 sandbox'),
        'default'     => WC_Gateway_2Pay4::_NO,
        'description' => sprintf( WC_Gateway_2Pay4::__( '2Pay4 sandbox can be used to test payments. Sign up for a developer account <a href="%s">here</a>.'), 'http://developer.com/' ),
        'desc_tip'    =>  true,
    ),

    // Enabled/disabled Logs payment
    'debug' => array(
        'title'       => __( 'Debug Log', 'woocommerce' ),
        'type'        => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_CHECKBOX,
        'label'       => __( 'Enable logging', 'woocommerce' ),
        'default'     => WC_Gateway_2Pay4::_NO,
        'description' => sprintf( WC_Gateway_2Pay4::__( 'Log 2Pay4 events, such as IPN requests, inside <code>%s</code>'), wc_get_log_file_path( '2pay4' ) )
    ),

    // Check this key when
    'key'   =>  array(
        'title'         => WC_Gateway_2Pay4::__( 'key'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_TEXT,
        'label'         => WC_Gateway_2Pay4::__( 'check key 2Pay4 Payment'),
        'description'   => WC_Gateway_2Pay4::__( 'check key 2Pay4 Payment'),
        'desc_tip'      =>  true,
        'default'       => md5(date('YmdHis'))
    ),

    // checked this key when successful request Payment
    'keyprivate' =>  array(
        'title'         => WC_Gateway_2Pay4::__( 'private key'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_TEXT,
        'label'         => WC_Gateway_2Pay4::__( 'private key 2Pay4 Payment'),
        'description'   => WC_Gateway_2Pay4::__( 'private key 2Pay4 Payment'),
        'desc_tip'      =>  true,
        'default'       => ''
    ),
    'login' =>  array(
        'title'         => WC_Gateway_2Pay4::__( 'login'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_TEXT,
        'label'         => WC_Gateway_2Pay4::__( 'login 2Pay4 Payment'),
        'description'   => WC_Gateway_2Pay4::__( 'login 2Pay4 Payment'),
        'desc_tip'      =>  true,
        'default'       => ''
    ),
    'password' =>  array(
        'title'         => WC_Gateway_2Pay4::__( 'password'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_PASSWORD,
        'label'         => WC_Gateway_2Pay4::__( 'password 2Pay4 Payment'),
        'description'   => WC_Gateway_2Pay4::__( 'password 2Pay4 Payment'),
        'desc_tip'      =>  true,
        'default'       => ''
    ),

    'merchantnumber' => array(
        'title' => WC_Gateway_2Pay4::__('Merchant number'),
        'type' => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_TEXT,
        'default' => ''
    ),

    // Processing payment Iframe/Redirect/Direct call which the user sees during checkout
    'typedateinput' =>  array(
        'title'         => WC_Gateway_2Pay4::__( 'Processing of type'),
        'type'          => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_SELECT,
        'options'       => array(
            1 => WC_Gateway_2Pay4::__( 'Iframe'),
            2 => WC_Gateway_2Pay4::__( 'Redirect'),
            3 => WC_Gateway_2Pay4::__( 'Direct calls'),
        ),
        'label'         => WC_Gateway_2Pay4::__( 'Processing of type 2Pay4 Payment'),
        'description'   => WC_Gateway_2Pay4::__( 'Processing of type 2Pay4 Payment'),
        'desc_tip'      =>  true,
        'default'       => 1
    ),

    // How to open the 2Pay4 Payment Window
    'windowstate' => array(
        'title'     => WC_Gateway_2Pay4::__( 'Window state( Iframe )'),
        'type'      => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_SELECT,
        'options'   => array(
            1 => WC_Gateway_2Pay4::__( 'Overlay'),
//            2 => WC_Gateway_2Pay4::__( 'Iframe'),
//            3 => WC_Gateway_2Pay4::__( 'Full screen')
        ),
        'label'     => WC_Gateway_2Pay4::__( 'How to open the 2Pay4 Payment Window'),
        'default'   => 1
    ),

    // add data invoice to 2Pay4 Payment
    'enableinvoice' => array(
        'title'     => WC_Gateway_2Pay4::__( 'Invoice data'),
        'type'      => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_CHECKBOX,
        'label'     => WC_Gateway_2Pay4::__( 'Enable invoice data'),
        'default'   => WC_Gateway_2Pay4::_NO
    ),

    'windowid' => array(
        'title' => WC_Gateway_2Pay4::__( 'Window ID'),
        'type' => WC_Gateway_2Pay4::_FORM_FIELDS_TYPE_TEXT,
        'default' => '1'
    ),
);
