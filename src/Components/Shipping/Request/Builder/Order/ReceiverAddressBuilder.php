<?php

namespace FS\Components\Shipping\Request\Builder\Order;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\Request\Builder\BuilderInterface;

class ReceiverAddressBuilder extends AbstractComponent implements BuilderInterface
{
    public function build($payload = null)
    {
        $address = $payload['shipping']->getShipment()->getToAddress();

        $receiverIsCommercial = false;
        if(array_key_exists('options', $payload)){
            $receiverIsCommercial = array_key_exists('receiver_residential',$payload['options']->all()) ? ($payload['options']->all()['receiver_residential'] == 'no' ? true : false) : false;
        }

        if (empty($address['name'])) {
            $address['name'] = $address['attn'] ? $address['attn'] : 'Receiver';
        }
        $address['address'] = substr($address['address'],0,30);
        $isNorthAmericanCountry = in_array($address['country'], ['CA', 'US']);

        // a friendly fix for quote, when customer does not provide state
        // provide a possibly wrong state to let address correction correct it
        if ($isNorthAmericanCountry && empty($address['state'])) {
            $address['state'] = $address['country'] == 'CA' ? 'QC' : 'NY';
        }

        $address['is_commercial'] = $receiverIsCommercial;

        return $address;
    }
}
