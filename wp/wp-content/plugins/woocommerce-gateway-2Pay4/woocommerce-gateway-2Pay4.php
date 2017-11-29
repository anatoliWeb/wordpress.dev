<?php
/*
Plugin Name: WooCommerce 2Pay4 Payment Solutions Gateway
Plugin URI: https://www.compareking.no/plugin/woocommerce
Description: A payment gateway for 2Pay4 payment solutions standard
Version: 1.0.0
Author: 2Pay4
Author URI: https://www.compareking.no/
Text Domain: 2pay4
 */

class payment2Pay4
{

    protected $_pluginUrl;

    protected $_pluginBasename;

    protected $_path;

    protected $_pathLib;

    public function __construct(){

        $this->_init();
    }

    protected function _init(){

        $this->_path = __DIR__;
        $this->_pluginBasename = plugin_basename($this->_path);
        $this->_pluginUrl = WP_PLUGIN_URL ."/".$this->_pluginBasename;
        $this->_pathLib = $this->_path . "/lib/";

        // add utils
        if(file_exists($this->_pathLib . 'Utils.php')) {
            include_once $this->_pathLib . 'Utils.php';
        }
    }

    public function addScript(){
        // add stule
        $urlPathStyle = $this->_pluginUrl."/style/";
        wp_enqueue_style('2pay4_style',  $urlPathStyle . '2Pay4.css');
    }

    public function pluginLoaded(){

        // check extends Payment Gateway and empty class WC_Gateway_2Pay4
        if ( class_exists( 'WC_Payment_Gateway' )  && !class_exists( 'WC_Gateway_2Pay4' )) {

            if(file_exists($this->_pathLib.'WC_Gateway_2Pay4.php')){
                // load lib
                include_once $this->_pathLib.'WC_Gateway_2Pay4.php';

                /**
                 * Add the Gateway to WooCommerce
                 **/
                add_filter('woocommerce_payment_gateways', function($methods){
                    $methods[] = 'WC_Gateway_2Pay4';
                    return $methods;
                });

                WC_Gateway_2Pay4::getInstance()->initHooks();

                load_plugin_textdomain('woocommerce-gateway-2Pay4', false, $this->_pluginBasename . '/languages');
            }
        }
    }
}

$payment2Pay4 = new payment2Pay4();

// register action
/*
Add Stylesheet and javascript to plugin
 */
add_action('admin_enqueue_scripts', array($payment2Pay4, 'addScript'));

// load plugin
add_action('plugins_loaded', array($payment2Pay4, 'pluginLoaded'));
