<?php

require_once dirname(__FILE__).'/../../lib/WC_Gateway_2Pay4.php';
require_once dirname(__FILE__).'/../../lib/WC_Gateway_2Pay4_Request.php';

class Tests_Plugin_WoocomerceGateway2Pay4_lib_WC_Gateway_2Pay4 extends WP_UnitTestCase
{
    protected $_request;
    protected $_gateway;
    protected $_helper;

    public function setUp(){
        parent::setUp();

        $this->_gateway = new WC_Gateway_2Pay4();
        $this->_request = new WC_Gateway_2Pay4_Request($this->_gateway);
        $this->_helper = new helper_2Pay4();
    }

    /**
     * Test method process_payment
     */
    public function test_processPayment(){
        $checkout = new WC_Checkout();
        $order_id = $checkout->create_order();
        $order   = new WC_Order($order_id);

        $example = array(
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url( true )
        );

        $this->_gateway->typeDateInput = 1;
        $this->assertEquals($example, $this->_gateway->process_payment($order_id));

        $example['redirect'] = $this->_request->get_request_url($order, $this->_gateway->testmode);

        $this->_gateway->typeDateInput = 2;
        $this->assertEquals($example, $this->_gateway->process_payment($order_id));
    }


}