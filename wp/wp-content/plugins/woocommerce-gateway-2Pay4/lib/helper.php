<?php

class helper_2Pay4
{

    const _VERSION = '1.0.0';

    const _NO = 'no';
    const _YES = 'yes';

    /**
     * @param $locale
     * @return string
     */
    public function get_language_code($locale) {

        $langCode = array(
            'nb_NO' =>  '1',
            'nn_NO' =>  '1',
            'en_AU' =>  '2',
            'en_BZ' =>  '2',
            'en_CA' =>  '2',
            'en_CB' =>  '2',
            'en_GB' =>  '2',
            'en_IE' =>  '2',
            'en_JM' =>  '2',
            'en_NZ' =>  '2',
            'en_PH' =>  '2',
            'en_TT' =>  '2',
            'en_US' =>  '2',
            'en_ZA' =>  '2',
            'en_ZW' =>  '2',
            'se_FI' =>  '3',
            'se_NO' =>  '3',
            'se_SE' =>  '3',
        );

        if(array_key_exists($locale, $langCode)){
            return $langCode[$locale];
        }

        return "0";
    }

    /**
     * @param $code
     * @param bool $isKey
     * @return mixed
     */
    public function get_iso_code($code, $isKey = true){
        $code = strtoupper($code);

        $isoCodeArray = array(
            'ADP' => '020', 'AED' => '784', 'AFA' => '004', 'ALL' => '008', 'AMD' => '051', 'ANG' => '532',
            'AOA' => '973', 'ARS' => '032', 'AUD' => '036', 'AWG' => '533', 'AZM' => '031', 'BAM' => '052',
            'BBD' => '004', 'BDT' => '050', 'BGL' => '100', 'BGN' => '975', 'BHD' => '048', 'BIF' => '108',
            'BMD' => '060', 'BND' => '096', 'BOB' => '068', 'BOV' => '984', 'BRL' => '986', 'BSD' => '044',
            'BTN' => '064', 'BWP' => '072', 'BYR' => '974', 'BZD' => '084', 'CAD' => '124', 'CDF' => '976',
            'CHF' => '756', 'CLF' => '990', 'CLP' => '152', 'CNY' => '156', 'COP' => '170', 'CRC' => '188',
            'CUP' => '192', 'CVE' => '132', 'CYP' => '196', 'CZK' => '203', 'DJF' => '262', 'DKK' => '208',
            'DOP' => '214', 'DZD' => '012', 'ECS' => '218', 'ECV' => '983', 'EEK' => '233', 'EGP' => '818',
            'ERN' => '232', 'ETB' => '230', 'EUR' => '978', 'FJD' => '242', 'FKP' => '238', 'GBP' => '826',
            'GEL' => '981', 'GHC' => '288', 'GIP' => '292', 'GMD' => '270', 'GNF' => '324', 'GTQ' => '320',
            'GWP' => '624', 'GYD' => '328', 'HKD' => '344', 'HNL' => '340', 'HRK' => '191', 'HTG' => '332',
            'HUF' => '348', 'IDR' => '360', 'ILS' => '376', 'INR' => '356', 'IQD' => '368', 'IRR' => '364',
            'ISK' => '352', 'JMD' => '388', 'JOD' => '400', 'JPY' => '392', 'KES' => '404', 'KGS' => '417',
            'KHR' => '116', 'KMF' => '174', 'KPW' => '408', 'KRW' => '410', 'KWD' => '414', 'KYD' => '136',
            'KZT' => '398', 'LAK' => '418', 'LBP' => '422', 'LKR' => '144', 'LRD' => '430', 'LSL' => '426',
            'LTL' => '440', 'LVL' => '428', 'LYD' => '434', 'MAD' => '504', 'MDL' => '498', 'MGF' => '450',
            'MKD' => '807', 'MMK' => '104', 'MNT' => '496', 'MOP' => '446', 'MRO' => '478', 'MTL' => '470',
            'MUR' => '480', 'MVR' => '462', 'MWK' => '454', 'MXN' => '484', 'MXV' => '979', 'MYR' => '458',
            'MZM' => '508', 'NAD' => '516', 'NGN' => '566', 'NIO' => '558', 'NOK' => '578', 'NPR' => '524',
            'NZD' => '554', 'OMR' => '512', 'PAB' => '590', 'PEN' => '604', 'PGK' => '598', 'PHP' => '608',
            'PKR' => '586', 'PLN' => '985', 'PYG' => '600', 'QAR' => '634', 'ROL' => '642', 'RUB' => '643',
            'RUR' => '810', 'RWF' => '646', 'SAR' => '682', 'SBD' => '090', 'SCR' => '690', 'SDD' => '736',
            'SEK' => '752', 'SGD' => '702', 'SHP' => '654', 'SIT' => '705', 'SKK' => '703', 'SLL' => '694',
            'SOS' => '706', 'SRG' => '740', 'STD' => '678', 'SVC' => '222', 'SYP' => '760', 'SZL' => '748',
            'THB' => '764', 'TJS' => '972', 'TMM' => '795', 'TND' => '788', 'TOP' => '776', 'TPE' => '626',
            'TRL' => '792', 'TRY' => '949', 'TTD' => '780', 'TWD' => '901', 'TZS' => '834', 'UAH' => '980',
            'UGX' => '800', 'USD' => '840', 'UYU' => '858', 'UZS' => '860', 'VEB' => '862', 'VND' => '704',
            'VUV' => '548', 'XAF' => '950', 'XCD' => '951', 'XOF' => '952', 'XPF' => '953', 'YER' => '886',
            'YUM' => '891', 'ZAR' => '710', 'ZMK' => '894', 'ZWD' => '716');

        if($isKey){
            if(array_key_exists($code, $isoCodeArray)){
                return $isoCodeArray[$code];
            }
            return null;
        }

        return array_search($code, $isoCodeArray);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function jsonValueRemoveSpecialCharacters($value){
        return preg_replace('/[^\p{Latin}\d ]/u', '', $value);
    }

    /**
     * yes return true, no return false
     *
     * @param $str
     * @return bool
     */
    public function checkYesOrNo($str){
        return $str === self::_YES;
    }

    /**
     * Fix Url
     *
     * @param $url
     * @return mixed
     */
    public function fixUrl($url){
        $url = str_replace('&#038;', '&amp;', $url);
        $url = str_replace('&amp;', '&', $url);

        return $url;
    }

    /**
     * @param $url string
     * @param $fields array
     * @return array
     */
    public function successfulRequest($url, $fields){

        $result = array();

        //open connection
        $Session = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($Session, CURLOPT_URL, $url);
        curl_setopt($Session, CURLOPT_POST, count($fields));
        curl_setopt($Session, CURLOPT_POSTFIELDS, json_encode($fields));

        curl_setopt($Session, CURLOPT_HEADER, false);
        curl_setopt($Session, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($Session, CURLOPT_RETURNTRANSFER, TRUE);

        // ssl false
        curl_setopt($Session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($Session, CURLOPT_SSL_VERIFYHOST, false);

        $result['result'] = curl_exec($Session);
        $result['info'] = curl_getinfo($Session);
        $result['error'] = curl_error($Session);

        //close connection
        curl_close($Session);

        return $result;
    }
}
