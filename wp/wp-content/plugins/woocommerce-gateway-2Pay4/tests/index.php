<?php

/**
 * Class Tests_Plugin_WoocomerceGateway2Pay4_index
 */
class Tests_Plugin_WoocomerceGateway2Pay4_index extends WP_UnitTestCase
{

    protected $_path;

    public function setUp(){

        $this->_path = ABSPATH . 'wp-content/plugins/woocommerce-gateway-2Pay4/woocommerce-gateway-2Pay4.php';

        include_once $this->_path;

        parent::setUp();
    }

    /**
     * check data plugin
     */
    public function test_get_plugin_data(){

        $data = get_plugin_data( $this->_path );

        // check Does data is available
        $this->assertTrue( is_array($data));

        // data default plugin
        $defaultHeaders = array(
            'Name'          =>  'WooCommerce 2Pay4 Payment Solutions Gateway',
            'Title'         =>  '<a href="https://www.compareking.no/plugin/woocommerce">WooCommerce 2Pay4 Payment Solutions Gateway</a>',
            'PluginURI'     =>  'https://www.compareking.no/plugin/woocommerce',
            'Version'       =>  '1.0.0',
            'Description'   =>  'A payment gateway for 2Pay4 payment solutions standard <cite>By <a href="https://www.compareking.no/">2Pay4</a>.</cite>',
            'Author'        =>  '<a href="https://www.compareking.no/">2Pay4</a>',
            'AuthorURI'     =>  'https://www.compareking.no/',
            'AuthorName'    =>  '2Pay4',
            'TextDomain'    =>  '2pay4',
            'DomainPath'    =>  '',
            'Network'       =>  false,
        );

        foreach ($defaultHeaders as $key => $value) {
            $this->assertTrue(array_key_exists($key, $data));
            $this->assertEquals($value, $data[$key]);
        }

    }
}