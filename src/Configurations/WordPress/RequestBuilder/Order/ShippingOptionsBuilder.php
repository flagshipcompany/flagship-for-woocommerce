<?php

namespace FS\Configurations\WordPress\RequestBuilder\Order;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\RequestBuilder\RequestBuilderInterface;

class ShippingOptionsBuilder extends AbstractComponent implements RequestBuilderInterface
{
    public function build($payload = null)
    {
        $shippingOptions = array();

        if (in_array($payload['to']['country'], array('CA', 'US'))) {
            $shippingOptions['address_correction'] = true;
        }

        $request = $payload['request']->request;

        if ($request->has('flagship_shipping_enable_insurance')
            && $request->get('flagship_shipping_enable_insurance') == 'yes'
            && $request->get('flagship_shipping_insurance_value') > 0
            && $request->get('flagship_shipping_insurance_description')
        ) {
            $shippingOptions['insurance'] = array(
                'value' => $request->get('flagship_shipping_insurance_value'),
                'description' => $request->get('flagship_shipping_insurance_description'),
            );
        }

        if ($request->has('flagship_shipping_enable_cod')
            && $request->get('flagship_shipping_enable_cod') == 'yes'
            && $request->get('flagship_shipping_cod_method')
            && $request->get('flagship_shipping_cod_payable_to')
            && $request->get('flagship_shipping_cod_receiver_phone')
            && $request->get('flagship_shipping_cod_amount')
            && $request->get('flagship_shipping_cod_currency')
        ) {
            $shippingOptions['cod'] = array(
                'method' => $request->get('flagship_shipping_cod_method'),
                'payable_to' => $request->get('flagship_shipping_cod_payable_to'),
                'receiver_phone' => $request->get('flagship_shipping_cod_receiver_phone'),
                'amount' => $request->get('flagship_shipping_cod_amount'),
                'currency' => $request->get('flagship_shipping_cod_currency'),
            );
        }

        if ($request->has('flagship_shipping_signature_required')) {
            $shippingOptions['signature_required'] = $request->get('flagship_shipping_signature_required') == 'yes';
        }

        if ($request->has('flagship_shipping_reference')
            && $request->get('flagship_shipping_reference')) {
            $shippingOptions['reference'] = $request->get('flagship_shipping_reference');
        }

        if ($request->has('flagship_shipping_driver_instructions')
            && $request->get('flagship_shipping_driver_instructions')) {
            $shippingOptions['driver_instructions'] = $request->get('flagship_shipping_driver_instructions');
        }

        if ($request->has('flagship_shipping_date')
            && strtotime($request->get('flagship_shipping_date')) >= strtotime(date('Y-m-d'))
        ) {
            $shippingOptions['shipping_date'] = $request->get('flagship_shipping_date');
        }

        return $shippingOptions;
    }
}
