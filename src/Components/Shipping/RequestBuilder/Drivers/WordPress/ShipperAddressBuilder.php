<?php

namespace FS\Components\Shipping\RequestBuilder\Drivers\WordPress;

class ShipperAddressBuilder extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    public function build($payload = null)
    {
        $options = $payload['options'];

        return array(
            'country' => 'CA',
            'state' => $options->get('freight_shipper_state'),
            'city' => $options->get('freight_shipper_city'),
            'postal_code' => $options->get('origin'),
            'address' => $options->get('freight_shipper_street'),
            'name' => $options->get('shipper_company_name', 'Shipper Company'),
            'attn' => $options->get('shipper_person_name', 'Shipper Attention'),
            'phone' => $options->get('shipper_phone_number'),
            'ext' => $options->get('shipper_phone_ext'),
        );
    }
}
