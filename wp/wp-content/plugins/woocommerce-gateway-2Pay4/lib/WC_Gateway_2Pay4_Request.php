<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generates requests to send to 2Pay4.
 */
class WC_Gateway_2Pay4_Request {

    /**
     * Stores line items to send to 2Pay4.
     *
     * @var array
     */
    protected $_line_items = array();

    /**
     * Endpoint for requests from 2Pay4.
     *
     * @var string
     */
    protected $_notify_url;

    /**
     * Pointer to gateway making the request.
     *
     * @var WC_Gateway_2Pay4
     */
    protected $_gateway;

    /**
     * @var helper_2Pay4
     */
    protected $_helper;

    /**
     * Constructor.
     *
     * @param WC_Gateway_2Pay4 $gateway
     */
    public function __construct($gateway){
        $this->_gateway    = $gateway;
        $this->_notify_url = WC()->api_request_url( 'WC_Gateway_2Pay4' );

        // add helper
        include_once "helper.php";
        $this->_helper = new helper_2Pay4();
    }

    /**
     * return api url
     *
     * @param $sandbox
     * @return string
     */
    public function getUrl($sandbox){

        if ( $sandbox ) {
            return 'http://compareking.dev/2Pay4/api/?test_ipn=1&';
        }else{
            return 'http://compareking.dev/2Pay4/api/?';
        }
    }

    /**
     * Get the 2Pay4 request URL for an order.
     *
     * @param $order
     * @param bool $sandbox
     * @return string
     */
    public function get_request_url( $order, $sandbox = false ) {
        $pay_args = http_build_query( $this->get_pay_args( $order ), '', '&' );

        WC_Gateway_2Pay4::log( 'Pay Request Args for order ' . $order->get_order_number() . ': ' . print_r( $pay_args, true ) );

        $url = $this->getUrl($sandbox);

        return $url. $pay_args;
    }

    /**
     * Get 2Pay4 Args for passing to PP.
     *
     * @param $order
     * @return mixed|void
     */
    public function get_pay_args( $order, $flagInvoice = false ) {
        WC_Gateway_2Pay4::log('Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->_notify_url );
        $listProduct = array();

        foreach($order->get_items() as $key=>$item){
            $listProduct[] = array(
                'item_id' => $key,
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'count' => $item['qty'],
                'total' => $item['line_total'],
                'price' => $item['line_subtotal']/$item['qty'],
            );
        }

        $args = array(
            'encoding'  =>  "UTF-8",
            'cmd'           => '_cart',
            'cms'           =>  $this->_getModuleHeaderInfo(),
            'login'         =>  $this->_gateway->get_option( 'login' ),
            'password'      =>  md5($this->_gateway->get_option( 'password' )),
            'merchantnumber'=>  $this->_gateway->get_option( 'merchantnumber' ),
            'checkkey'      => $this->_gateway->get_option( 'key' ),
            'business'      => $this->_gateway->get_option( 'email' ),
            'language'      => $this->_helper->get_language_code(get_locale()),
            'windowstate'   => $this->_gateway->get_option( 'windowstate' ),
            'typedateinput' => $this->_gateway->get_option( 'typedateinput' ),
            'no_note'       => 1,
            'currency_code' => get_woocommerce_currency(),
            'charset'       => 'utf-8',
            'rm'            => is_ssl() ? 2 : 1,
            'upload'        => 1,
            'return'        => esc_url_raw( add_query_arg( 'utm_nooverride', '1', $this->_gateway->get_return_url( $order ) ) ),
            'cancel_return' => esc_url_raw( $order->get_cancel_order_url_raw() ),
            'page_style'    => $this->_gateway->get_option( 'page_style' ),
            'paymentaction' => $this->_gateway->get_option( 'paymentaction' ),
            'bn'            => 'WooThemes_Cart',
            'orderid'       => $order->get_order_number(),
            'custom'        => json_encode( array( 'order_id' => $order->id, 'order_key' => $order->order_key ) ),
            'notify_url'    => $this->_notify_url,
            'first_name'    => $order->billing_first_name,
            'last_name'     => $order->billing_last_name,
            'company'       => $order->billing_company,
            'address1'      => $order->billing_address_1,
            'address2'      => $order->billing_address_2,
            'city'          => $order->billing_city,
            'state'         => $this->_get_pay_state( $order->billing_country, $order->billing_state ),
            'zip'           => $order->billing_postcode,
            'country'       => $order->billing_country,
            'email'         => $order->billing_email,
            'create_invoice'=> $this->createInvoice($order, $flagInvoice),
            'order_detail'  =>  json_encode(array('products'=>$listProduct)),

        );

        // WooCommerce Subscriptions v2+
        if( is_a($order, 'WC_Subscription') && function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($order) ) {
            $args['subscription'] = 1;
            $args['amount'] = $order->get_total_initial_payment() * 100;
        }
        // deprecated way since Subscriptions v2+
        else if( class_exists('WC_Subscriptions_Order') && WC_Subscriptions_Order::order_contains_subscription($order) ) {
            $args['subscription'] = 1;
            $args['amount'] = WC_Subscriptions_Order::get_total_initial_payment($order) * 100;
        }
        // not a subscription
        else {
            $args['subscription'] = 0;
            $args['amount'] = $order->get_total() * 100;
        }

        return apply_filters( 'woocommerce_2pay4_args', array_merge(
            $args,
            $this->_get_phone_number_args( $order ),
            $this->_get_shipping_args( $order ),
            $this->_get_line_item_args( $order )
        ), $order );

    }

    /**
     * Get the state to send to 2pay4.
     * @param  string $cc
     * @param  string $state
     * @return string
     */
    protected function _get_pay_state( $cc, $state ) {
        if ( 'US' === $cc ) {
            return $state;
        }

        $states = WC()->countries->get_states( $cc );

        if ( isset( $states[ $state ] ) ) {
            return $states[ $state ];
        }

        return $state;
    }

    /**
     * Get phone number args for 2pay4 request.
     * @param  WC_Order $order
     * @return array
     */
    protected function _get_phone_number_args( WC_Order $order ) {
        if ( in_array( $order->billing_country, array( 'US','CA' ) ) ) {
            $phone_number = str_replace( array( '(', '-', ' ', ')', '.' ), '', $order->billing_phone );
            $phone_number = ltrim( $phone_number, '+1' );
            $phone_args   = array(
                'night_phone_a' => substr( $phone_number, 0, 3 ),
                'night_phone_b' => substr( $phone_number, 3, 3 ),
                'night_phone_c' => substr( $phone_number, 6, 4 ),
                'day_phone_a' 	=> substr( $phone_number, 0, 3 ),
                'day_phone_b' 	=> substr( $phone_number, 3, 3 ),
                'day_phone_c' 	=> substr( $phone_number, 6, 4 )
            );
        } else {
            $phone_args = array(
                'night_phone_b' => $order->billing_phone,
                'day_phone_b' 	=> $order->billing_phone
            );
        }
        return $phone_args;
    }

    /**
     * Get shipping args for 2pay4 request.
     * @param  WC_Order $order
     * @return array
     */
    protected function _get_shipping_args( WC_Order $order ) {
        $shipping_args = array();

        if ( 'yes' == $this->_gateway->get_option( 'send_shipping' ) ) {
            $shipping_args['address_override'] = $this->_gateway->get_option( 'address_override' ) === 'yes' ? 1 : 0;
            $shipping_args['no_shipping']      = 0;

            // If we are sending shipping, send shipping address instead of billing
            $shipping_args['first_name']       = $order->shipping_first_name;
            $shipping_args['last_name']        = $order->shipping_last_name;
            $shipping_args['company']          = $order->shipping_company;
            $shipping_args['address1']         = $order->shipping_address_1;
            $shipping_args['address2']         = $order->shipping_address_2;
            $shipping_args['city']             = $order->shipping_city;
            $shipping_args['state']            = $this->_get_pay_state( $order->shipping_country, $order->shipping_state );
            $shipping_args['country']          = $order->shipping_country;
            $shipping_args['zip']              = $order->shipping_postcode;
        } else {
            $shipping_args['no_shipping']      = 1;
        }

        return $shipping_args;
    }

    /**
     * Get line item args for 2pay4 request.
     * @param  WC_Order $order
     * @return array
     */
    protected function _get_line_item_args( WC_Order $order ) {

        /**
         * Try passing a line item per product if supported.
         */
        if ( ( ! wc_tax_enabled() || ! wc_prices_include_tax() ) && $this->_prepare_line_items( $order ) ) {

            $line_item_args             = array();
            $line_item_args['tax_cart'] = $this->_number_format( $order->get_total_tax(), $order );

            if ( $order->get_total_discount() > 0 ) {
                $line_item_args['discount_amount_cart'] = $this->_number_format( $this->_round( $order->get_total_discount(), $order ), $order );
            }

            // Add shipping costs. Pay ignores anything over 5 digits (999.99 is the max).
            // We also check that shipping is not the **only** cost as Pay won't allow payment
            // if the items have no cost.
            if ( $order->get_total_shipping() > 0 && $order->get_total_shipping() < 999.99 && $this->_number_format( $order->get_total_shipping() + $order->get_shipping_tax(), $order ) !== $this->_number_format( $order->get_total(), $order ) ) {
                $line_item_args['shipping_1'] = $this->_number_format( $order->get_total_shipping(), $order );
            } elseif ( $order->get_total_shipping() > 0 ) {
                $this->_add_line_item( sprintf( __( 'Shipping via %s', 'woocommerce' ), $order->get_shipping_method() ), 1, $this->_number_format( $order->get_total_shipping(), $order ) );
            }

            $line_item_args = array_merge( $line_item_args, $this->_get_line_items() );

            /**
             * Send order as a single item.
             *
             * For shipping, we longer use shipping_1 because pay ignores it if *any* shipping rules are within pay, and pay ignores anything over 5 digits (999.99 is the max).
             */
        } else {

            $this->_delete_line_items();

            $line_item_args = array();
            $all_items_name = $this->_get_order_item_names( $order );
            $this->_add_line_item( $all_items_name ? $all_items_name : __( 'Order', 'woocommerce' ), 1, $this->_number_format( $order->get_total() - $this->_round( $order->get_total_shipping() + $order->get_shipping_tax(), $order ), $order ), $order->get_order_number() );

            // Add shipping costs. Pay ignores anything over 5 digits (999.99 is the max).
            // We also check that shipping is not the **only** cost as Pay won't allow payment
            // if the items have no cost.
            if ( $order->get_total_shipping() > 0 && $order->get_total_shipping() < 999.99 && $this->_number_format( $order->get_total_shipping() + $order->get_shipping_tax(), $order ) !== $this->_number_format( $order->get_total(), $order ) ) {
                $line_item_args['shipping_1'] = $this->_number_format( $order->get_total_shipping() + $order->get_shipping_tax(), $order );
            } elseif ( $order->get_total_shipping() > 0 ) {
                $this->_add_line_item( sprintf( __( 'Shipping via %s', 'woocommerce' ), $order->get_shipping_method() ), 1, $this->_number_format( $order->get_total_shipping() + $order->get_shipping_tax(), $order ) );
            }

            $line_item_args = array_merge( $line_item_args, $this->_get_line_items() );
        }

        return $line_item_args;
    }

    /**
     * Get line items to send to 2pay4.
     * @param  WC_Order $order
     * @return bool
     */
    protected function _prepare_line_items( $order ) {
        $this->_delete_line_items();
        $calculated_total = 0;

        // Products
        foreach ( $order->get_items( array( 'line_item', 'fee' ) ) as $item ) {
            if ( 'fee' === $item['type'] ) {
                $item_line_total  = $this->_number_format( $item['line_total'], $order );
                $line_item        = $this->_add_line_item( $item['name'], 1, $item_line_total );
                $calculated_total += $item_line_total;
            } else {
                $product          = $order->get_product_from_item( $item );
                $sku              = $product ? $product->get_sku() : '';
                $item_line_total  = $this->_number_format( $order->get_item_subtotal( $item, false ), $order );
                $line_item        = $this->_add_line_item( $this->_get_order_item_name( $item ), $item['qty'], $item_line_total, $sku );
                $calculated_total += $item_line_total * $item['qty'];
            }

            if ( ! $line_item ) {
                return false;
            }
        }

        // Check for mismatched totals.
        if ( $this->_number_format( $calculated_total + $order->get_total_tax() + $this->_round( $order->get_total_shipping(), $order ) - $this->_round( $order->get_total_discount(), $order ), $order ) != $this->_number_format( $order->get_total(), $order ) ) {
            return false;
        }

        return true;
    }

    /**
     * Remove all line items.
     */
    protected function _delete_line_items() {
        $this->_line_items = array();
    }

    /**
     * Format prices.
     * @param  float|int $price
     * @param  WC_Order $order
     * @return string
     */
    protected function _number_format( $price, WC_Order $order ) {
        $decimals = 2;

        if ( ! $this->_currency_has_decimals( $order->get_order_currency() ) ) {
            $decimals = 0;
        }

        return number_format( $price, $decimals, '.', '' );
    }

    /**
     * Check if currency has decimals.
     * @param  string $currency
     * @return bool
     */
    protected function _currency_has_decimals( $currency ) {
        if ( in_array( $currency, array( 'HUF', 'JPY', 'TWD' ) ) ) {
            return false;
        }

        return true;
    }

    /**
     * Add 2Pay4 Line Item.
     *
     * @param $item_name
     * @param int $quantity
     * @param int $amount
     * @param string $item_number
     * @return bool  successfully added or not
     */
    protected function _add_line_item( $item_name, $quantity = 1, $amount = 0, $item_number = '' ) {
        $index = ( sizeof( $this->_line_items ) / 4 ) + 1;

        if ( $amount < 0 || $index > 9 ) {
            return false;
        }

        $this->_line_items[ 'item_name_' . $index ]   = html_entity_decode( wc_trim_string( $item_name ? $item_name : __( 'Item', 'woocommerce' ), 127 ), ENT_NOQUOTES, 'UTF-8' );
        $this->_line_items[ 'quantity_' . $index ]    = (int) $quantity;
        $this->_line_items[ 'amount_' . $index ]      = (float) $amount;
        $this->_line_items[ 'item_number_' . $index ] = $item_number;

        return true;
    }

    /**
     * Get order item names as a string.
     * @param  array $item
     * @return string
     */
    protected function _get_order_item_name( $item ) {
        $item_name = $item['name'];
        $item_meta = new WC_Order_Item_Meta( $item );

        if ( $meta = $item_meta->display( true, true ) ) {
            $item_name .= ' ( ' . $meta . ' )';
        }

        return $item_name;
    }

    /**
     * Round prices.
     * @param  double $price
     * @param  WC_Order $order
     * @return double
     */
    protected function _round( $price, WC_Order $order ) {
        $precision = 2;

        if ( ! $this->_currency_has_decimals( $order->get_order_currency() ) ) {
            $precision = 0;
        }

        return round( $price, $precision );
    }

    /**
     * Return all line items.
     */
    protected function _get_line_items() {
        return $this->_line_items;
    }

    /**
     * Get order item names as a string.
     * @param  WC_Order $order
     * @return string
     */
    protected function _get_order_item_names( $order ) {
        $item_names = array();

        foreach ( $order->get_items() as $item ) {
            $item_names[] = $item['name'] . ' x ' . $item['qty'];
        }

        return implode( ', ', $item_names );
    }

    /**
     * Returns the module header
     *
     * @return string
     */
    protected  function _getModuleHeaderInfo() {
        global $woocommerce;

        return implode(' ', array(
            'WooCommerce/'.$woocommerce->version,
            'Module/'. WC_Gateway_2Pay4::_VERSION
        ));
    }

    public function createInvoice($order,  $flagInvoice = false ){
        if($flagInvoice){
            $invoice = array(
                'customer'  =>  array(
                    'emailaddress'  =>  $order->billing_email,
                    'firstname'     =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->billing_first_name),
                    'lastname'      =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->billing_last_name),
                    'address'       =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->billing_address_1),
                    'zip'           =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->billing_postcode),
                    'city'          =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->billing_city),
                    'country'       =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->billing_country)
                ),
                'shippingaddress' => array(
                    'firstname'     =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->shipping_first_name),
                    'lastname'      =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->shipping_last_name),
                    'address'       =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->shipping_address_1),
                    'zip'           =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->shipping_postcode),
                    'city'          =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->shipping_city),
                    'country'       =>  $this->_helper->jsonValueRemoveSpecialCharacters($order->shipping_country),
                ),
                'lines'     =>  array()
            );

            // product info
            $items = $order->get_items();
            foreach($items as $item){
                $invoice["lines"][] = array(
                    'id'            => $item['product_id'],
                    'description'   => $this->_helper->jsonValueRemoveSpecialCharacters($item['name']),
                    'price'         => round($item['line_subtotal'] / $item['qty'] * 100),
                    'vat'           => round($item['line_subtotal_tax'] / $item['line_subtotal'] * 100)
                );
            }

            // discount
            $discount = $order->get_total_discount();
            if($discount != 0){
                $invoice['lines'][] = array(
                    'id'            => 'discount',
                    'description'   => 'discount',
                    'quantity'      => 1,
                    'price'         => -round($discount * 100),
                    'vat'           => round($order->get_total_tax() / ($order->get_total() - $order->get_total_tax())  * 100)
                );
            }

            // shipping
            $shipping = $order->get_total_shipping();
            if($shipping != 0){
                $invoice['lines'][] = array(
                    'id'            => 'shipping',
                    'description'   => 'shipping',
                    'quantity'      => 1,
                    'price'         => round($shipping * 100),
                    'vat'           => round($order->get_shipping_tax() / $shipping * 100)
                );
            }

            return json_encode($invoice,JSON_UNESCAPED_UNICODE);
        }
        return '';
    }


}
