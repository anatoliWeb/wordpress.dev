<?php

require_once dirname(__FILE__).'/../../lib/WC_Gateway_2Pay4.php';
require_once dirname(__FILE__).'/../../lib/WC_Gateway_2Pay4_Request.php';

class Tests_Plugin_WoocomerceGateway2Pay4_lib_WC_Gateway_2Pay4_Request extends WP_UnitTestCase
{
    protected $_request;
    protected $_gateway;
    protected $_helper;

    public function setUp(){
        parent::setUp();

        $this->_gateway = new WC_Gateway_2Pay4();
        $this->_request = new WC_Gateway_2Pay4_Request($this->_gateway);
        $this->_helper  = new helper_2Pay4();
    }

    /**
     * Test method getUrl
     */
    public function test_getUrl(){

        $rightExempl = array(
            'https://compareking.dev/2Pay4/api/?test_ipn=1&'    =>  true,
            'https://compareking.dev/2Pay4/api/?'               =>  false
        );

        // has
        foreach($rightExempl as $right => $example){
            $this->assertEquals($right, $this->_request->getUrl($example));
        }
    }

    /**
     * Test method get_request_url
     */
    public function test_get_request_url(){

        $checkout = new WC_Checkout();
        $order_id = $checkout->create_order();
        $order   = new WC_Order($order_id);
        $example = $this->_request->getUrl(false).http_build_query( $this->_getOrderArgs($order) , '', '&' );

        $this->assertEquals($example, $this->_request->get_request_url($order));
    }

    /**
     * Test method getPayArgs
     */
    public function test_getPayArgs(){

        $checkout = new WC_Checkout();
        $order_id = $checkout->create_order();
        $order   = new WC_Order($order_id);

        $this->assertEquals($this->_getOrderArgs($order), $this->_request->get_pay_args($order));
    }

    /**
     * Test method createInvoice
     */
    public function test_createInvoice(){
        $checkout = new WC_Checkout();
        $order_id = $checkout->create_order();
        $order   = new WC_Order($order_id);
        $example = '{"customer":{"emailaddress":"","firstname":"","lastname":"","address":"","zip":"","city":"","country":""},"shippingaddress":{"firstname":"","lastname":"","address":"","zip":"","city":"","country":""},"lines":[]}';
        $this->assertEquals($example, $this->_request->createInvoice($order, true));
    }

    /**
     * @param $order
     * @return array
     */
    protected function _getOrderArgs($order){
        $example = array(
            'encoding'      =>  'UTF-8',
            'cmd'           =>  '_cart',
            'cms'           =>  'WooCommerce/2.6.8 Module/1.0.0',
            'business'      =>  $this->_gateway->get_option( 'email' ),
            'language'      =>  $this->_helper->get_language_code(get_locale()),
            'windowstate'   =>  $this->_gateway->get_option( 'windowstate' ),
            'typedateinput' =>  $this->_gateway->get_option( 'typedateinput' ),
            'checkkey'      =>  $this->_gateway->get_option( 'key' ),
            'no_note'       =>  '1',
            'currency_code' =>  get_woocommerce_currency(),
            'charset'       =>  'utf-8',
            'rm'            =>  is_ssl() ? 2 : 1,
            'upload'        =>  '1',
            'return'        =>  esc_url_raw( add_query_arg( 'utm_nooverride', '1', $this->_gateway->get_return_url( $order ) ) ),
            'cancel_return' =>  esc_url_raw( $order->get_cancel_order_url_raw() ),
            'page_style'    =>  $this->_gateway->get_option( 'page_style' ),
            'paymentaction' =>  $this->_gateway->get_option( 'paymentaction' ),
            'bn'            =>  'WooThemes_Cart',
            'orderid'       =>  $order->get_order_number(),
            'custom'        =>  json_encode( array( 'order_id' => $order->id, 'order_key' => $order->order_key ) ),
            'notify_url'    =>  WC()->api_request_url( 'WC_Gateway_2Pay4' ),
            'first_name'    =>  $order->billing_first_name,
            'last_name'     =>  $order->billing_last_name,
            'company'       =>  $order->billing_company,
            'address1'      =>  $order->billing_address_1,
            'address2'      =>  $order->billing_address_2,
            'city'          =>  $order->billing_city,
            'state'         =>  '',
            'zip'           => $order->billing_postcode,
            'country'       => $order->billing_country,
            'email'         => $order->billing_email,
            'create_invoice'=>  '',
            'subscription'  =>  '0',
            'amount'        =>  '0',
            'night_phone_b' =>  '',
            'day_phone_b'   =>  '',
            'no_shipping'   =>  '1',
            'tax_cart'      =>  '0.00',

        );

        return $example;
    }

}