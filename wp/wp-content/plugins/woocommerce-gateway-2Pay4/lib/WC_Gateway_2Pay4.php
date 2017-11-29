<?php

/**
 * Class WC_Gateway_2Pay4
 */
class WC_Gateway_2Pay4 extends WC_Payment_Gateway
{

    const _VERSION = '1.0.0';

    const _NO = 'no';
    const _YES = 'yes';

    // form fields type
    const _FORM_FIELDS_TYPE_TEXT = 'text';
    const _FORM_FIELDS_TYPE_PASSWORD = 'password';
    const _FORM_FIELDS_TYPE_CHECKBOX = 'checkbox';
    const _FORM_FIELDS_TYPE_TEXTAREA = 'textarea';
    const _FORM_FIELDS_TYPE_SELECT = 'select';

    /** @var bool Whether or not logging is enabled */
    public static $log_enabled = false;

    /** @var WC_Logger Logger instance */
    public static $log = false;

    public static $_instance;

    protected $_domain;

    protected static $_domainLang;
    /**
     * @var helper_2Pay4
     */
    protected $_helper;

    public $debug;
    public $renamebuttonpayment = false;
    public $remoteinterface = false;

    /**
     * getInstance
     *
     * Returns a new instance of self, if it does not already exist.
     *
     * @return \WC_Gateway_2Pay4
     */
    public static function getInstance() {
        if (!isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->_domain = '2Pay4';
        self::$_domainLang = 'woocommerce-gateway-2Pay4';

        include_once "helper.php";
        $this->_helper = new helper_2Pay4();

        $this->id = '2pay4';
        $this->method_title = '2Pay4';
        $this->method_description = '';

        $this->icon = WP_PLUGIN_URL . "/" . plugin_basename(__DIR__ . "/../") . '/2Pay4-logo.png';
        $this->has_fields = false;

        $this->supports = array(
            'subscriptions',
            'products',
            'subscription_cancellation',
            'subscription_reactivation',
            'subscription_suspension',
            'subscription_amount_changes',
            'subscription_date_changes'
        );

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Define user set variables
        $this->_defineSetVariable();

        self::$log_enabled = $this->debug;

        // rename default button
        if($this->renamebuttonpayment){
            $this->order_button_text = WC_Gateway_2Pay4::__('Proceed to 2Pay4');
        }

        // Payment Gateway description for the checkout page
//        $this->set_description_for_checkout($this->merchant);

        if ($this->remoteinterface) {
            $this->supports = array_merge($this->supports, array('refunds'));
        }
    }

    /**
     * Check if this gateway is enabled and available in the user's country.
     * @return bool
     */
    public function is_valid_for_use() {

        $supported_currencies = array('NOK');

        //if($this->testmode){
        if(true){
            $supported_currencies = array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB' );
        }

        return in_array(
            get_woocommerce_currency(),
            apply_filters( 'woocommerce_2pay4_supported_currencies', $supported_currencies)
        );
    }

    /**
     * Admin Panel Options.
     * - Options for bits like 'title' and availability on a country-by-country basis.
     *
     * @since 1.0.0
     */
    public function admin_options() {
        if ( $this->is_valid_for_use() ) {
            parent::admin_options();
        } else {
            ?>
            <div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woocommerce' ); ?></strong>: <?php _e( '2Pay4 does not support your store currency.', 'woocommerce' ); ?></p></div>
        <?php
        }
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
        $this->form_fields = include __DIR__ . "/../include/admin-setting.php";
    }

    /**
     * Define user set variables
     */
    protected function _defineSetVariable() {

        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->showicon = $this->_helper->checkYesOrNo($this->get_option('showicon', self::_YES));
        $this->showhelppage = $this->_helper->checkYesOrNo($this->get_option('showhelppage', self::_YES));
        $this->renamebuttonpayment = $this->_helper->checkYesOrNo($this->get_option('renamebuttonpayment', self::_YES));
        $this->testmode = $this->_helper->checkYesOrNo($this->get_option('testmode', self::_YES));
        $this->debug = $this->_helper->checkYesOrNo($this->get_option('debug', self::_NO));
        $this->checkkey = $this->get_option('key', '12345');
        $this->publickey = $this->get_option('publickey', 'test');
        $this->privatekey = $this->get_option('privatekey', 'test');
        $this->typeDateInput = $this->get_option('typedateinput', '1');
        $this->windowstate = $this->get_option('windowstate', '2');
        $this->enableinvoice = $this->_helper->checkYesOrNo($this->get_option('enableinvoice', self::_NO));

        $this->remoteinterface = $this->_helper->checkYesOrNo($this->get_option('remoteinterface', self::_NO));
    }

    /**
     * Set the WC Payment Gateway description for the checkout page
     */
    public function set_description_for_checkout($merchantnumber) {
        global $woocommerce;
        $cart = $woocommerce->cart;
        if (!$cart || !$merchantnumber) {
            return;
        }
        // link to description integration 2Pay4
        $this->description .= '<span id="_2pay4_card_logos"></span><script type="text/javascript" src="https://google.com"></script>';
    }

    public function initHooks(){

        // action successful payment
        add_action('valid-2pay4-callback', array($this, 'successful_request'));

        if (is_admin()) {

            if ($this->_helper->checkYesOrNo($this->remoteinterface)) {
                add_action('add_meta_boxes', array($this, '2pay4_meta_boxes'));
            }

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('wp_before_admin_bar_render', array($this, 'pay_action'));
        }

        add_action('woocommerce_api_' . strtolower(get_class()), array($this, 'check_callback'));
        add_action('woocommerce_scheduled_subscription_payment_' . $this->id, array($this, 'scheduled_subscription_payment'));

        // eceipt_page
        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

        // status
        add_action('woocommerce_order_status_changed', array($this, 'sendChangeStatus'));

    }

    /**
     * Successful Payment
     *
     * @param $post
     */
    public function successful_request($post){

        WC_Gateway_2Pay4::log('succsefful request: ' . print_r($post, true) );

        if(array_key_exists('typedateinput', $post) && $post['typedateinput'] == 3){

            $formRows =  json_decode(file_get_contents('php://input'),true);

            include_once 'WC_Gateway_2Pay4_Request.php';
            $pay_request = new WC_Gateway_2Pay4_Request($this);

            $order = wc_get_order($formRows['orderid']);

            $rows = array(
                'form' => $formRows,
                'args' => $pay_request->get_pay_args($order, $this->testmode)
            );

            $url = $pay_request->getUrl($this->get_option('testmode', true)) .http_build_query( array('data'=>json_encode($rows),'typedateinput'=>'3',  'check_form'=>'true'), '', '&' );

            $curl = $this->_helper->successfulRequest($url, $rows);

            if($curl['info']['http_code'] == 200){
                echo $curl['result'];
                status_header(200);
                exit ;
            }

            echo $curl['result'];
            status_header(500);
            exit;

        }else{
            $posted = array_keys($post);
            $id = $posted['0'];
            $order_id = $posted['1'];
            $key = $posted['2'];

            reset($_POST);
            $postData = json_decode(key($_POST));

            try{

                // check key
                if($postData->key != md5($key) && $postData->key != $this->get_option('key', '12345')){
                    throw new Exception('MD5 check failed for 2Pay4 callback with order_id:'.$id);
                }

                //check privateKey
                if($postData->privatekey != md5($this->get_option('keyprivate', '12345'))){
                    throw new Exception('key check failed for 2Pay4 callback with order_id:'.$id);
                }

                $order = new WC_Order((int)$id);

                if($postData->status) {
                    // Payment completed
                    $order->add_order_note(WC_Gateway_2Pay4::__('Callback completed'));

                    $order->payment_complete();

                    echo "OK - Order Created";

                }else{
                    // Payment  not completed
                    $order->add_order_note(WC_Gateway_2Pay4::__('Callback not completed'));
                }


            }catch(Exception $e){
                $this->log($e->getMessage());
                echo $e->getMessage();
                status_header(500);
                return;
            }

            $psbReference = get_post_meta((int)$posted["wooorderid"],'Transaction ID',true);
            status_header(200);
            exit;
        }

    }

    /**
     * action when save option to admin
     *
     * @return bool|void
     */
//    public function process_admin_options(){
//        // ...
//
//        parent::process_admin_options();
//    }

    /**
     * pay action
     */
    public function pay_action(){
        if (array_key_exists("2pay4_action", $_GET)) {
//            dd(func_get_args(), __METHOD__, __LINE__);
        }
    }

    /**
     * action check callback
     */
    public function check_callback(){

        do_action("valid-2pay4-callback", array_map('stripslashes_deep', $_GET));
    }

//    public function scheduled_subscription_payment(){
//        dd(func_get_args(), __METHOD__);
//    }

    /**
     * receipt_page
     **/
    function receipt_page($order_id){
        $html = '<p>' . WC_Gateway_2Pay4::__("Thank you for your order, please click the button below to pay with 2Pay4.") . '</p>';
        $html .= $this->_generate_pay_form($order_id);
        echo $html;
    }

    /**
     * Logging method.
     * @param string $message
     */
    public static function log($message){
        if (self::$log_enabled) {
            if (empty(self::$log)) {
                self::$log = new WC_Logger();
            }
            self::$log->add('2pay4', $message);
        }
    }

    /**
     * -- site --
     *  info
     * Get gateway icon.
     * @return string
     */
    public function get_icon() {
        $icon_html = '';
        if($this->showicon){
            $icon = array($this->icon);

            foreach ($icon as $i) {
                $icon_html .= '<img src="' . esc_attr($i) . '" alt="' . WC_Gateway_2Pay4::__('2Pay4 Acceptance Mark') . '" />';
            }
        }
        if($this->showhelppage){
            $icon_html .= sprintf('<a href="%1$s" class="about_paypal" onclick="javascript:window.open(\'%1$s\',\'WI2Pay4\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700\'); return false;" title="' . WC_Gateway_2Pay4::__( 'What is 2Pay4?') . '">' . WC_Gateway_2Pay4::__( 'What is 2Pay4?') . '</a>', esc_url(''));
        }

        return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
    }

    /**
     * Get the transaction URL.
     * @param  WC_Order $order
     * @return string
     */
//    public function get_transaction_url($order) {
//        return parent::get_transaction_url($order);
//    }

    /**
     * site when click button $this->order_button_text
     * Process the payment and return the result.
     * @param  int $order_id
     * @return array
     */
    public function process_payment($order_id) {

        $order = wc_get_order($order_id);
        $redirect = $order->get_checkout_payment_url( true );

        WC_Gateway_2Pay4::log('Process payment order- ' . $order->get_order_number() . '. Type date input: ' . $this->typeDateInput );

        // if option request
        if($this->typeDateInput == '2'){
            include_once 'WC_Gateway_2Pay4_Request.php';
            $pay_request = new WC_Gateway_2Pay4_Request($this);
            $redirect = $pay_request->get_request_url($order, $this->testmode);
        }

        return array(
            'result' => 'success',
            'redirect' => $redirect
        );
    }

    /**
     * Process a refund if supported.
     * @param  int $order_id
     * @param  float $amount
     * @param  string $reason
     * @return bool True or false based on success, or a WP_Error object
     */
//    public function process_refund($order_id, $amount = null, $reason = '') {
//        dd($order_id, __METHOD__);
//    }

    /**
     * TRANSLATE
     *
     * @param $str
     * @return string|void
     */
    public static function __($str){
        return __( $str, self::$_domainLang);
    }

    protected function _generate_pay_form($order_id){
        $order = new WC_Order($order_id);

        WC_Gateway_2Pay4::log('Build payment form order- ' . $order->get_order_number() . '. Type date input: ' . $this->typeDateInput );

        include_once 'WC_Gateway_2Pay4_Request.php';
        $pay_request = new WC_Gateway_2Pay4_Request($this);
        $args = $pay_request->get_pay_args($order, $this->enableinvoice);
        $args_array = array();
        foreach($args as $key=>$value){
            $args_array[] = "'" . esc_attr($key) . "':'" . $value ."'";
        }

        // load ifram
        switch($this->typeDateInput){
            case '1':
                $html = array(
                    '<script type="text/javascript">',
                        'function PaymentWindowReady() {',
                            'paymentwindow = new PaymentWindow({',
                                implode(',', $args_array),
                            '});',
                            'paymentwindow.open();',
                        '}',
                    '</script>',
                    '<script type="text/javascript" src="https://compareking.dev/js/integration/paymentwindow.uncompress.js" charset="UTF-8"></script>',
                    '<a class="button" onclick="javascript: paymentwindow.open();" id="submit_2pay4_payment_form" />' . WC_Gateway_2Pay4::__('Pay via 2Pay4') . '</a>',
                    '<a class="button cancel" href="' . esc_url($order->get_cancel_order_url()) . '">' . WC_Gateway_2Pay4::__('Cancel order &amp; restore cart') . '</a>',
                );
                break;

            case '3':

                $html = array(
                    '<div id="2pay4_payment_form">',
                    '</div>',
                    '<a class="button" id="submit_2pay4_payment_form">' . WC_Gateway_2Pay4::__('Pay via 2Pay4') . '</a>',
                    '<a class="button cancel" href="' . esc_url($order->get_cancel_order_url()) . '">' .WC_Gateway_2Pay4::__('Cancel order &amp; restore cart') . '</a>',
                    '<script type="text/javascript">',
                    'function PaymentFormReady() {',
                        'paymentform = new PaymentForm({',
                            implode(',', $args_array),
                        '});',
                        'paymentform.buildForm("2pay4_payment_form");',
                    '}',
                    '</script>',
                    '<script type="text/javascript" src="https://compareking.dev/js/integration/paymentform.uncompress.js" charset="UTF-8"></script>',
                );

                break;

            default:
                $html = array();
        }

        return implode('', $html);
    }

    public function sendChangeStatus($order_id = 0){

        try{
            $order = wc_get_order($order_id);
            if($order->payment_method === '2pay4'){
                $fields = array(
                    'order_id' => $order->id,
                    'order_key' => $order->order_key,
                    'title' => $order->payment_method_title,
                    'status_id' => $order->post->post_status,
                    'status_label' => $order->get_status(),
                );

                WC_Gateway_2Pay4::log('sendChangeStatus fields: ' . print_r($fields, true) );

                include_once 'WC_Gateway_2Pay4_Request.php';
                $pay_request = new WC_Gateway_2Pay4_Request($this);

                $url = $pay_request->getUrl($this->get_option('testmode', true)).http_build_query(array('status_order'=>true));

                $request = $this->_helper->successfulRequest($url, $fields);

                WC_Gateway_2Pay4::log('sendChangeStatus request: ' . print_r($request, true) );
            }

        }catch (Exception $e){
            WC_Gateway_2Pay4::log('sendChangeStatus error: ' . print_r($e->getMessage(), true) );
        }
    }
}