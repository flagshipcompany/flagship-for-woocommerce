<?php

namespace FS\Components\Shipping\Request\Builder\Order;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\Request\Builder\BuilderInterface;

class ShippingOptionsBuilder extends AbstractComponent implements BuilderInterface
{
    public function build($payload = null)
    {
        $shippingOptions = [];

        if (in_array($payload['to']['country'], ['CA', 'US'])) {
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

        if ($request->has('flagship_shipping_tracking_emails')
            && $request->get('flagship_shipping_tracking_emails')) {
            $shippingOptions['shipment_tracking_emails'] = $request->get('flagship_shipping_tracking_emails');
        }

        if ($request->has('flagship_shipping_date')
            && strtotime($request->get('flagship_shipping_date')) >= strtotime(date('Y-m-d'))
        ) {
            $shippingOptions['shipping_date'] = $request->get('flagship_shipping_date');
        }

        return $shippingOptions;
    }
}
