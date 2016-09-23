<?php

namespace FS\Components\Shipping\RequestBuilder\ShoppingOrder;

class ReceiverAddressBuilder extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    public function build($order = null)
    {
        $address = array(
            'name' => $order->shipping_company,
            'attn' => $order->shipping_first_name.' '.$order->shipping_last_name,
            'address' => trim($order->shipping_address_1.' '.$order->shipping_address_2),
            'city' => $order->shipping_city,
            'state' => $order->shipping_state,
            'country' => $order->shipping_country,
            'postal_code' => $order->shipping_postcode,
            'phone' => $order->billing_phone, // no such a field in the shipping!?
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
