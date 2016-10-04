<?php

namespace FS\Configurations\WordPress\RequestBuilder\Order;

class ReceiverAddressBuilder extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    public function build($payload = null)
    {
        $wcOrder = $payload['order']->getWcOrder();

        $address = array(
            'name' => $wcOrder->shipping_company,
            'attn' => $wcOrder->shipping_first_name.' '.$wcOrder->shipping_last_name,
            'address' => trim($wcOrder->shipping_address_1.' '.$wcOrder->shipping_address_2),
            'city' => $wcOrder->shipping_city,
            'state' => $wcOrder->shipping_state,
            'country' => $wcOrder->shipping_country,
            'postal_code' => $wcOrder->shipping_postcode,
            'phone' => $wcOrder->billing_phone, // no such a field in the shipping!?
        );

        if (empty($address['name'])) {
            $address['name'] = $address['attn'] ? $address['attn'] : 'Receiver';
        }

        $isNorthAmericanCountry = in_array($address['country'], array('CA', 'US'));

        // a friendly fix for quote, when customer does not provide state
        // provide a possibly wrong state to let address correction correct it
        if ($isNorthAmericanCountry && empty($address['state'])) {
            $address['state'] = $address['country'] == 'CA' ? 'QC' : 'NY';
        }

        return $address;
    }
}
