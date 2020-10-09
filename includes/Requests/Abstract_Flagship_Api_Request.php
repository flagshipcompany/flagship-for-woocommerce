<?php
namespace FlagshipWoocommerce\Requests;

use FlagshipWoocommerce\FlagshipWoocommerceShipping;

abstract class Abstract_Flagship_Api_Request {

    private $token;

    private $apiUrl;

    // address field in request => field in woocommerce address
    private $addressFieldMap = array(
        'postal_code' => 'postcode',
        'address' => 'address_1',
        'suite' => 'address_2',
    );

    protected $requiredAddressFields = array(
        'country',
        'state',
        'postal_code',
        'city',
    );

    protected function getApiUrl($testEnv = 0)
    {
        return $testEnv ? 'https://test-api.smartship.io' : 'https://api.smartship.io';
    }

    protected function getWebUrl($testEnv = 0)
    {
        return $testEnv ? 'https://test-smartshipng.flagshipcompany.com' : 'https://smartship-ng.flagshipcompany.com';
    }

    protected function addHeaders($prepareRequest, $storeName, $orderId)
    {
        return $prepareRequest
            ->setStoreName($storeName)
            ->setOrderId($orderId)
            ->setOrderLink(get_edit_post_link($orderId, null));
    }

    public function getStoreAddress($fullAddress = false, $getEmail = false, $options = array())
    {
        $storeAddress = array();

        if(!empty($options['dropshipping_address_city']))
        {
            $dropShipAddress['postal_code'] = trim($options['dropshipping_address_postal_code']);
            $dropShipAddress['country'] = 'CA';
            $dropShipAddress['state'] = $options['dropshipping_address_state'];
            $dropShipAddress['city'] = trim($options['dropshipping_address_city']);
            if($fullAddress){
                $address = trim($options['dropshipping_address_street_address']);
                $dropShipAddress['address'] = substr($address,0,30);
                $dropShipAddress['suite'] = substr(trim($options['dropshipping_address_suite']),0,18);
                $company = $options['dropshipping_address_company'];
                $dropShipAddress['name'] = substr((empty($company) ? trim($options['dropshipping_address_name']) : $company),0,30);
                $dropShipAddress['attn'] = substr(trim($options['dropshipping_address_name']),0,21);
                $dropShipAddress['phone'] = trim($options['dropshipping_address_phone']);
            }
            return $dropShipAddress;
        }

        $storeAddress['postal_code'] = trim(get_option('woocommerce_store_postcode', ''));
        $countryState = $this->getCountryState();
        $storeAddress['country'] = $countryState['country'];
        $storeAddress['state'] = $countryState['state'];
        $storeAddress['city'] = trim(get_option('woocommerce_store_city', ''));

        if ($fullAddress) {
            $storeAddress['address'] = substr(trim(get_option('woocommerce_store_address', '')),0,30);
            $storeAddress['suite'] = substr(trim(get_option('woocommerce_store_address_2', '')),0,18);
            $storeAddress['name'] = substr(trim(get_option('woocommerce_store_name', '')),0,30);
            $storeAddress['attn'] = substr(trim(get_option('woocommerce_store_attn', '')),0,21);
            $storeAddress['phone'] = trim(get_option('woocommerce_store_phone', ''));
        }

        if ($getEmail) {
            $storeAddress['email'] = trim(WC()->mailer()->get_emails()['WC_Email_New_Order']->recipient);
        }

        return $storeAddress;
    }

    protected function getCountryState()
    {
        $countryAndState = array(
            'country' => null,
            'state' => null,
        );
        $countryState = get_option('woocommerce_default_country', '');

        if (empty($countryState)) {
            return $countryAndState;
        }

        $splitValues = explode(':', $countryState);
        $country = isset($splitValues[0]) ? $splitValues[0] : null;
        $state = isset($splitValues[1]) ? $splitValues[1] : null;

        return array(
            'country' => $country,
            'state' => $state,
        );
    }

    protected function fillAddressField($destination, $fieldName)
    {
        if (isset($destination[$fieldName])) {
            return trim($destination[$fieldName]);
        }

        $alternativeName = $this->addressFieldMap[$fieldName];

        return isset($destination[$alternativeName]) ? trim($destination[$alternativeName]) : '';
    }

    protected function getDestinationAddress($destination, $addressFields, $options = array())
    {
        $destinationAddress = array();

        foreach ($addressFields as $key => $fieldName) {
            $destinationAddress[$fieldName] = $this->fillAddressField($destination, $fieldName);
        }

        if (isset($options['residential_receiver_address'])) {
            $destinationAddress['is_commercial'] = false;
        }

        return $destinationAddress;
    }

    protected function makeShippingOptions($options)
    {
        $shippingOptions = array();

        if (get_array_value($options, 'rsignature_required', false)) {
            $shippingOptions['signature_required'] = true;
        }

        return $shippingOptions;
    }

    protected function getOrderShippingAddressForRates($order)
    {
        $shippingAddress = [];

        $shippingAddress['postal_code'] = $order->get_shipping_postcode();
        $shippingAddress['country'] = $order->get_shipping_country();
        $shippingAddress['state'] = $order->get_shipping_state();
        $shippingAddress['city'] = $order->get_shipping_city();

        return $shippingAddress;
    }
}
