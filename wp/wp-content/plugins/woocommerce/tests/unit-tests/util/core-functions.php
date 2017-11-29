<?php

/**
 * Class Core_Functions.
 * @package WooCommerce\Tests\Util
 * @since 2.2
 */
class WC_Tests_Core_Functions extends WC_Unit_Test_Case {

	/**
	 * Test get_woocommerce_currency().
	 *
	 * @since 2.2
	 */
	public function test_get_woocommerce_currency() {

		$this->assertEquals( 'GBP', get_woocommerce_currency() );
	}

	/**
	 * Test get_woocommerce_currencies().
	 *
	 * @since 2.2
	 */
	public function test_get_woocommerce_currencies() {

		$expected_currencies = array(
			'AED' => 'United Arab Emirates dirham',
			'AFN' => 'Afghan afghani',
			'ALL' => 'Albanian lek',
			'AMD' => 'Armenian dram',
			'ANG' => 'Netherlands Antillean guilder',
			'AOA' => 'Angolan kwanza',
			'ARS' => 'Argentine peso',
			'AUD' => 'Australian dollar',
			'AWG' => 'Aruban florin',
			'AZN' => 'Azerbaijani manat',
			'BAM' => 'Bosnia and Herzegovina convertible mark',
			'BBD' => 'Barbadian dollar',
			'BDT' => 'Bangladeshi taka',
			'BGN' => 'Bulgarian lev',
			'BHD' => 'Bahraini dinar',
			'BIF' => 'Burundian franc',
			'BMD' => 'Bermudian dollar',
			'BND' => 'Brunei dollar',
			'BOB' => 'Bolivian boliviano',
			'BRL' => 'Brazilian real',
			'BSD' => 'Bahamian dollar',
			'BTC' => 'Bitcoin',
			'BTN' => 'Bhutanese ngultrum',
			'BWP' => 'Botswana pula',
			'BYR' => 'Belarusian ruble',
			'BZD' => 'Belize dollar',
			'CAD' => 'Canadian dollar',
			'CDF' => 'Congolese franc',
			'CHF' => 'Swiss franc',
			'CLP' => 'Chilean peso',
			'CNY' => 'Chinese yuan',
			'COP' => 'Colombian peso',
			'CRC' => 'Costa Rican col&oacute;n',
			'CUC' => 'Cuban convertible peso',
			'CUP' => 'Cuban peso',
			'CVE' => 'Cape Verdean escudo',
			'CZK' => 'Czech koruna',
			'DJF' => 'Djiboutian franc',
			'DKK' => 'Danish krone',
			'DOP' => 'Dominican peso',
			'DZD' => 'Algerian dinar',
			'EGP' => 'Egyptian pound',
			'ERN' => 'Eritrean nakfa',
			'ETB' => 'Ethiopian birr',
			'EUR' => 'Euro',
			'FJD' => 'Fijian dollar',
			'FKP' => 'Falkland Islands pound',
			'GBP' => 'Pound sterling',
			'GEL' => 'Georgian lari',
			'GGP' => 'Guernsey pound',
			'GHS' => 'Ghana cedi',
			'GIP' => 'Gibraltar pound',
			'GMD' => 'Gambian dalasi',
			'GNF' => 'Guinean franc',
			'GTQ' => 'Guatemalan quetzal',
			'GYD' => 'Guyanese dollar',
			'HKD' => 'Hong Kong dollar',
			'HNL' => 'Honduran lempira',
			'HRK' => 'Croatian kuna',
			'HTG' => 'Haitian gourde',
			'HUF' => 'Hungarian forint',
			'IDR' => 'Indonesian rupiah',
			'ILS' => 'Israeli new shekel',
			'IMP' => 'Manx pound',
			'INR' => 'Indian rupee',
			'IQD' => 'Iraqi dinar',
			'IRR' => 'Iranian rial',
			'ISK' => 'Icelandic kr&oacute;na',
			'JEP' => 'Jersey pound',
			'JMD' => 'Jamaican dollar',
			'JOD' => 'Jordanian dinar',
			'JPY' => 'Japanese yen',
			'KES' => 'Kenyan shilling',
			'KGS' => 'Kyrgyzstani som',
			'KHR' => 'Cambodian riel',
			'KMF' => 'Comorian franc',
			'KPW' => 'North Korean won',
			'KRW' => 'South Korean won',
			'KWD' => 'Kuwaiti dinar',
			'KYD' => 'Cayman Islands dollar',
			'KZT' => 'Kazakhstani tenge',
			'LAK' => 'Lao kip',
			'LBP' => 'Lebanese pound',
			'LKR' => 'Sri Lankan rupee',
			'LRD' => 'Liberian dollar',
			'LSL' => 'Lesotho loti',
			'LYD' => 'Libyan dinar',
			'MAD' => 'Moroccan dirham',
			'MDL' => 'Moldovan leu',
			'MGA' => 'Malagasy ariary',
			'MKD' => 'Macedonian denar',
			'MMK' => 'Burmese kyat',
			'MNT' => 'Mongolian t&ouml;gr&ouml;g',
			'MOP' => 'Macanese pataca',
			'MRO' => 'Mauritanian ouguiya',
			'MUR' => 'Mauritian rupee',
			'MVR' => 'Maldivian rufiyaa',
			'MWK' => 'Malawian kwacha',
			'MXN' => 'Mexican peso',
			'MYR' => 'Malaysian ringgit',
			'MZN' => 'Mozambican metical',
			'NAD' => 'Namibian dollar',
			'NGN' => 'Nigerian naira',
			'NIO' => 'Nicaraguan c&oacute;rdoba',
			'NOK' => 'Norwegian krone',
			'NPR' => 'Nepalese rupee',
			'NZD' => 'New Zealand dollar',
			'OMR' => 'Omani rial',
			'PAB' => 'Panamanian balboa',
			'PEN' => 'Peruvian nuevo sol',
			'PGK' => 'Papua New Guinean kina',
			'PHP' => 'Philippine peso',
			'PKR' => 'Pakistani rupee',
			'PLN' => 'Polish z&#x142;oty',
			'PRB' => 'Transnistrian ruble',
			'PYG' => 'Paraguayan guaran&iacute;',
			'QAR' => 'Qatari riyal',
			'RON' => 'Romanian leu',
			'RSD' => 'Serbian dinar',
			'RUB' => 'Russian ruble',
			'RWF' => 'Rwandan franc',
			'SAR' => 'Saudi riyal',
			'SBD' => 'Solomon Islands dollar',
			'SCR' => 'Seychellois rupee',
			'SDG' => 'Sudanese pound',
			'SEK' => 'Swedish krona',
			'SGD' => 'Singapore dollar',
			'SHP' => 'Saint Helena pound',
			'SLL' => 'Sierra Leonean leone',
			'SOS' => 'Somali shilling',
			'SRD' => 'Surinamese dollar',
			'SSP' => 'South Sudanese pound',
			'STD' => 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra',
			'SYP' => 'Syrian pound',
			'SZL' => 'Swazi lilangeni',
			'THB' => 'Thai baht',
			'TJS' => 'Tajikistani somoni',
			'TMT' => 'Turkmenistan manat',
			'TND' => 'Tunisian dinar',
			'TOP' => 'Tongan pa&#x2bb;anga',
			'TRY' => 'Turkish lira',
			'TTD' => 'Trinidad and Tobago dollar',
			'TWD' => 'New Taiwan dollar',
			'TZS' => 'Tanzanian shilling',
			'UAH' => 'Ukrainian hryvnia',
			'UGX' => 'Ugandan shilling',
			'USD' => 'United States dollar',
			'UYU' => 'Uruguayan peso',
			'UZS' => 'Uzbekistani som',
			'VEF' => 'Venezuelan bol&iacute;var',
			'VND' => 'Vietnamese &#x111;&#x1ed3;ng',
			'VUV' => 'Vanuatu vatu',
			'WST' => 'Samoan t&#x101;l&#x101;',
			'XAF' => 'Central African CFA franc',
			'XCD' => 'East Caribbean dollar',
			'XOF' => 'West African CFA franc',
			'XPF' => 'CFP franc',
			'YER' => 'Yemeni rial',
			'ZAR' => 'South African rand',
			'ZMW' => 'Zambian kwacha',
		);

		$this->assertEquals( $expected_currencies, get_woocommerce_currencies() );
	}

	/**
	 * Test get_woocommerce_currency_symbol().
	 *
	 * @since 2.2
	 */
	public function test_get_woocommerce_currency_symbol() {

		// default currency
		$this->assertEquals( '&pound;', get_woocommerce_currency_symbol() );

		// given specific currency
		$this->assertEquals( '&#36;', get_woocommerce_currency_symbol( 'USD' ) );

		// each case
		foreach ( array_keys( get_woocommerce_currencies() ) as $currency_code ) {
			$this->assertInternalType( 'string', get_woocommerce_currency_symbol( $currency_code ) );
		}
	}

	/**
	 * Test get_woocommerce_api_url().
	 *
	 * @since 2.2
	 */
	public function test_get_woocommerce_api_url() {

		$base_uri = get_home_url();

		// base uri
		$this->assertEquals( "$base_uri/wc-api/v3/", get_woocommerce_api_url( null ) );

		// path
		$this->assertEquals( "$base_uri/wc-api/v3/orders", get_woocommerce_api_url( 'orders' ) );
	}

	/**
	 * Test wc_get_log_file_path().
	 *
	 * @since 2.4
	 */
	public function test_wc_get_log_file_path() {
		$log_dir   = trailingslashit( WC_LOG_DIR );
		$hash_name = sanitize_file_name( wp_hash( 'unit-tests' ) );

		$this->assertEquals( $log_dir . 'unit-tests-' . $hash_name . '.log', wc_get_log_file_path( 'unit-tests' ) );
	}

	/**
	 * Test wc_get_core_supported_themes().
	 *
	 * @since 2.2
	 */
	public function test_wc_get_core_supported_themes() {

		$expected_themes = array( 'twentyseventeen', 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );

		$this->assertEquals( $expected_themes, wc_get_core_supported_themes() );
	}

	/**
	 * Test wc_get_base_location().
	 *
	 * @since 2.3.0
	 */
	public function test_wc_get_base_location() {
		$default = wc_get_base_location();

		$this->assertEquals( 'GB', $default['country'] );
		$this->assertEquals( '', $default['state'] );
	}

	/**
	 * Test wc_format_country_state_string().
	 *
	 * @since 2.6.0
	 */
	public function test_wc_format_country_state_string() {
		// Test with correct values.
		$this->assertEquals( array( 'country' => 'US', 'state' => 'CA' ), wc_format_country_state_string( 'US:CA' ) );
		// Test what happens when we pass an incorrect value.
		$this->assertEquals( array( 'country' => 'US-CA', 'state' => '' ), wc_format_country_state_string( 'US-CA' ) );
	}

	/**
	 * Test wc_get_shipping_method_count()
	 *
	 * @since 2.6.0
	 */
	public function test_wc_get_shipping_method_count() {
		// Without legacy methods.
		$this->assertEquals( 0, wc_get_shipping_method_count( false ) );

		// With legacy methods.
		$this->assertEquals( 0, wc_get_shipping_method_count( true ) );
	}
}
