<?php

namespace FS\Components\Shipping\Request\Builder\Cart;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\Request\Builder\BuilderInterface;

class ReceiverAddressBuilder extends AbstractComponent implements BuilderInterface
{
    public function build($payload = null)
    {
        $package = $payload['package'];

        $address = array(
            'country' => $package['destination']['country'],
            'state' => $package['destination']['state'],
            'city' => $package['destination']['city'],
            'postal_code' => $package['destination']['postcode'],
            'address' => $package['destination']['address'].' '.$package['destination']['address_2'],
        );

        $isNorthAmericanCountry = in_array($address['country'], array('CA', 'US'));

        // a friendly fix for quote, when customer does not provide state
        // provide a possibly wrong state to let address correction correct it
        if ($isNorthAmericanCountry && empty($address['state'])) {
            $address['state'] = $address['country'] == 'CA' ? 'QC' : 'NY';
        }

        return $address;
    }
}