<?php
namespace FlagshipWoocommerce\Requests;

use Flagship\Shipping\Flagship;
use Flagship\Shipping\Collections\RatesCollection;
use FlagshipWoocommerce\FlagshipWoocommerceShipping;
use FlagshipWoocommerce\Helpers\Package_Helper;

class Rates_Request extends Abstract_Flagship_Api_Request {

    protected $debugMode = false;

    public function __construct($token, $debugMode = false, $testEnv = 0)
    {
        $this->token = $token;
        $this->apiUrl = $this->getApiUrl($testEnv);
        $this->debugMode = $debugMode;
    }

    public function getRates($package, $options = array(), $admin=0, $order=null)
    {
        if($admin==1)
        {
            $orderItems = $this->getOrderItems($order);
            $packages = $this->getPackages($orderItems,$options);
            $sourceAddress = $this->getStoreAddress(false,false,$options);

            $shippingAddress = $this->getOrderShippingAddressForRates($order);
            $apiRequest = $this->getRequest($sourceAddress,$shippingAddress,$packages, $options);
        }
        if($admin==0)
        {
            $apiRequest = $this->makeApiRequest($package, $options);
        }

        $apiClient = new Flagship($this->token, $this->apiUrl, 'woocommerce', FlagshipWoocommerceShipping::$version);

        try{
            $rates = $apiClient->createQuoteRequest($apiRequest)->execute();           
            return $rates;
        }
        catch(\Exception $e){
            $this->debug($e->getMessage());            
            $rates = new RatesCollection();
            return $rates;
        }
    }

    public function getOrderItems($order)
    {
        $orderItems = [];
        $items = $order->get_items();
        foreach ($items as $item_id => $value) {
            $item = [];
            $item["product"] = $value->get_product();
            $item["quantity"] = $value->get_quantity();
            $orderItems[] = $item;
        }
        return $orderItems;
    }

    protected function makeApiRequest($package, $options = array())
    {
        $storeAddress = $this->getStoreAddress(false,false,$options);
        $destinationAddress = $this->getDestinationAddress($package['destination'], $this->requiredAddressFields, $options);

        $packages = $this->getPackages($this->extractOrderItems($package),$options);

        $request = $this->getRequest($storeAddress,$destinationAddress,$packages, $options);
        return $request;
    }

    protected function getRequest($sourceAddress, $destinationAddress, $packages, $options)
    {
        $shippingOptions = $this->makeShippingOptions($options);

        $request = array(
            'from' => $sourceAddress,
            'to' => $destinationAddress,
            'packages' => $packages,
            'options' => [
                    "address_correction" => true
                ]
        );

        if ($shippingOptions) {
            $request['options'] = $shippingOptions;
        }

        return $request;

    }

    public function getPackages($orderItems,$options)
    {
        $packageHelper = new Package_Helper($this->debugMode,$this->apiUrl);
        $packages = $packageHelper->make_packages($orderItems,$options);
        return $packages;
    }

    protected function extractOrderItems($items)
    {
        $orderItems = array();

        foreach ( $items['contents'] as $item_id => $values ) {
            $item = array();
            $item['product'] = $values['data'];
            $item['quantity'] = $values['quantity'];
            $orderItems[] = $item;
        }

        return $orderItems;
    }

    protected function debug($message, $type = 'notice')
    {
        if (FlagshipWoocommerceShipping::isDebugMode() || $this->debugMode) {
            wc_add_notice($message, $type);
        }
    }
}
