<?php
namespace FlagshipWoocommerce\Requests;

use Flagship\Shipping\Flagship;
use FlagshipWoocommerce\FlagshipWoocommerceShipping;
use FlagshipWoocommerce\Helpers\Package_Helper;
use FlagshipWoocommerce\Requests\Confirm_Shipment_Request;

class Export_Order_Request extends Abstract_Flagship_Api_Request {

    private $fullAddressFields = array();

    private $editShipmentAddressFields = array(
        'postal_code',
        'country',
        'state',
        'city',
        'address',
        'name',
        'attn',
        'phone',
    );

    public function __construct($token, $testEnv=0)
    {
        $this->token = $token;
        $this->apiUrl = $this->getApiUrl($testEnv);
        $this->webUrl = $this->getWebUrl($testEnv);
        $this->fullAddressFields = array_merge($this->requiredAddressFields, array('address', 'suite', 'first_name', 'last_name'));
    }

    public function exportOrder($order, $options)
    {
        $storeAddress = $this->getStoreAddress(true, false, $options);
        $prepareRequest = $this->makePrepareRequest($order, $options);
        $apiClient = new Flagship($this->token, $this->apiUrl, 'woocommerce', FlagshipWoocommerceShipping::$version);

        try
        {
            $prepareRequestObj = $apiClient->prepareShipmentRequest($prepareRequest);
            $prepareRequestObj = $this->addHeaders($prepareRequestObj, $storeAddress['name'], $order->get_id());
            $exportedShipment = $prepareRequestObj->execute();

            $editShipmentData = $this->makeExtraFieldsForEdit($order, $exportedShipment, $prepareRequest, $options);

            if($editShipmentData)
            {
                $exportedShipment = $this->editShipment($order,$exportedShipment,$prepareRequest,$editShipmentData, $options);
            }
            return $exportedShipment;
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    public function editShipment($order, $flagshipShipment, $preparePayload, $editShipmentData, $options)
    {
        $storeAddress = $this->getStoreAddress(true, false, $options);
        $apiClient = new Flagship($this->token, $this->apiUrl, 'woocommerce', FlagshipWoocommerceShipping::$version);
        $editRequest = array_merge($preparePayload, $editShipmentData);
        $editRequestObj = $apiClient->editShipmentRequest($editRequest, $flagshipShipment->getId());
        $editRequestObj = $this->addHeaders($editRequestObj, $storeAddress['name'], $order->get_id());
        try{
            $exportedShipment = $editRequestObj->execute();
            return $exportedShipment;
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function makeExtraFieldsForEdit($order, $exportedShipment, $prepareRequest, $options)
    {
        $extraFields = array();
        $storeAddress = $this->getStoreAddress(true,false,$options);
        $selectedService = $this->findShippingServiceInOrder($order);
        $nbrOfMissingFields = count($this->findMissingAddressFieldsForEdit($storeAddress));
        $shipmentId = $exportedShipment->getId();
        $isIntl = $this->isIntShipment($prepareRequest);
        $commercialInvFields = array();

        if ($isIntl) {
            $commercialInvFields = (new Commercial_Inv_Request_Helper())->makeIntShpFields($prepareRequest, $order);
        }

        if (!$shipmentId || !$selectedService || $nbrOfMissingFields || ($isIntl && !$commercialInvFields)) {
            return array();
        }

        $extraFields['service'] = $selectedService;

        if ($commercialInvFields) {
            $extraFields = array_merge($extraFields, $commercialInvFields);
        }

        return $extraFields;
    }

    public function isOrderShippingAddressValid($order)
    {
        $address = $this->getDestinationAddress($order->get_address('shipping'), $this->requiredAddressFields);

        return count(array_filter($address)) == count($address);
    }

    public function makePrepareRequest($order, $options)
    {
        $storeAddress = $this->getStoreAddress(true, false, $options);
        $orderOptions = $this->getOrderOptions($order);

        $destinationAddress = $this->getFullDestinationAddress($order);
        $packageHelper = new Package_Helper(false,$this->apiUrl);
        $orderItems = $order->get_items();
        $packages = $packageHelper->make_packages($this->extractOrderItems($orderItems), $options);
        $trackingEmails = $this->makeTrackingEmails($destinationAddress, $options, $orderOptions);
        unset($destinationAddress['email']);

        $shippingOptions = array();

        if ($trackingEmails) {
            $shippingOptions['shipment_tracking_emails'] = $trackingEmails;
        }

        $request = array(
            'from' => $storeAddress,
            'to' => $destinationAddress,
            'packages' => $packages,
            'options' => $shippingOptions,
        );

        if (get_array_value($orderOptions, 'residential_receiver_address', false)) {
            $request['to']['is_commercial'] = false;
        }

        if (get_array_value($orderOptions, 'signature_required', false)) {
            $request['options']['signature_required'] = true;
        }

        return $request;
    }

    public function getFlagshipUrl()
    {
        return $this->webUrl;
    }

    public function confirmShipment($shipmentId)
    {
        $confirmShipmentRequest = new Confirm_Shipment_Request($this->token,$this->apiUrl);
        $confirmedShipment = $confirmShipmentRequest->confirmShipmentById($shipmentId);
        return $confirmedShipment;
    }

    protected function isIntShipment($prepareRequest)
    {
        return ($prepareRequest['from']['country'] == 'CA' && $prepareRequest['to']['country'] != 'CA') || ($prepareRequest['from']['country'] != 'CA' && $prepareRequest['to']['country'] == 'CA');
    }

    public function extractOrderItems($items)
    {
        $orderItems = array();

        foreach ( $items as $items_key => $item_data ) {
            $item = array();
            $item['product'] = $item_data->get_product();
            $item['quantity'] = $item_data->get_quantity();
            $orderItems[] = $item;
        }

        return $orderItems;
    }

    protected function getFullDestinationAddress($order)
    {
        $shippingAddress = $order->get_address('shipping');
        $billingAddress = $order->get_address('billing');

        $fullAddress = $this->getDestinationAddress($shippingAddress, $this->fullAddressFields);
        $fullAddress['attn'] = substr(trim($fullAddress['first_name'].' '.$fullAddress['last_name']),0,21);
        unset($fullAddress['first_name']);
        unset($fullAddress['last_name']);
        $fullAddress['name'] = substr($fullAddress['attn'],0,30);
        $fullAddress['phone'] = trim($billingAddress['phone']);
        $fullAddress['email'] = trim($billingAddress['email']);

        $fullAddress['address'] = substr($fullAddress['address'],0,30);

        if ($this->getOrderShippingMeta($order, 'residential_receiver_address') == 'yes') {
            $fullAddress['is_commercial'] = false;
        }

        return $fullAddress;
    }

    protected function getOrderOptions($order)
    {
        $optionKeys = array(
            'send_tracking_emails',
            'residential_receiver_address',
            'signature_required',
        );
        $options = array();

        foreach ($optionKeys as $key => $value) {
            if ($this->getOrderShippingMeta($order, $value) === 'yes') {
                $options[$value] = true;
            }
        }

        return $options;
    }

    protected function findShippingServiceInOrder($order)
    {
        $selectedService = $this->getOrderShippingMeta($order, 'selected_shipping');
        $courierAndService = array_map('trim', explode('-', $selectedService));
        $fields = array('courier_name', 'courier_code');

        return array_combine($fields, $courierAndService);
    }

    protected function getOrderShippingMeta($order, $key)
    {
        $shipping = $order->get_items('shipping');

        if (!$shipping) {
            return;
        }

        return reset($shipping)->get_meta($key);
    }

    protected function makeTrackingEmails($destinationAddress, $options, $orderOptions)
    {
        $adminEmail = get_array_value($options, 'tracking_emails');
        $customerEmail = isset($destinationAddress['email']) && (get_array_value($orderOptions, 'send_tracking_emails', false) || get_array_value($options, 'send_tracking_emails', 'no') === 'yes') ? $destinationAddress['email'] : null;
        $trackingEmails = array_filter(array($adminEmail, $customerEmail));

        return implode(';', $trackingEmails);
    }

    protected function findMissingAddressFieldsForEdit($storeAddress)
    {
        $missingFields = array_filter($this->editShipmentAddressFields, function($val) use ($storeAddress) {
            return !isset($storeAddress[$val]) || empty(trim($storeAddress[$val]));
        });

        return $missingFields;
    }
}
