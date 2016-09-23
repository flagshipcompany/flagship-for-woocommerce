<?php

namespace FS\Components\Shipping\RequestBuilder;

class ShipperAddressBuilder extends \FS\Components\AbstractComponent implements RequestBuilderInterface
{
    public function build($options = null)
    {
        return array(
            'country' => 'CA',
            'state' => $options->get('freight_shipper_state'),
            'city' => $options->get('freight_shipper_city'),
            'postal_code' => $options->get('origin'),
            'address' => $options->get('freight_shipper_street'),
            'name' => $options->get('shipper_company_name'),
            'attn' => $options->get('shipper_person_name'),
            'phone' => $options->get('shipper_phone_number'),
            'ext' => $options->get('shipper_phone_ext'),
        );
    }
}
