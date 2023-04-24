<?php

namespace FS\Components\Shipping\Request\Factory;

use FS\Components\Shipping\Request\Builder\Factory\RequestBuilderFactory;
use FS\Components\Shipping\Request\FormattedRequestInterface;

class ShoppingCartRate extends AbstractRequestFactory
{
    public function makeRequest(FormattedRequestInterface $request, RequestBuilderFactory $factory)
    {
        $request->add(
            'from',
            $this->makeRequestPart(
                $factory->resolve('ShipperAddress', [
                    'type' => 'cart',
                ]),
                $this->payload
            )
        );

        $toAddress = $this->makeRequestPart(
            $factory->resolve('ReceiverAddress', array(
                'type' => 'cart',
            )),
            $this->payload
        );

        $request->add(
            'to',
            $toAddress
        );

        $request->add(
            'packages',
            $this->makeRequestPart(
                $factory->resolve('PackageItems', array(
                    'type' => 'cart',
                    'usePackingApi' => $this->payload['options']->eq('default_package_box_split', 'packing'),
                )),
                $this->payload
            )
        );

        $request->add(
            'payment',
            array(
                'payer' => 'F',
            )
        );

        $options = $this->makeOptions($toAddress);

        if (!empty($options)) {
            $request->add(
                'options',
                $options
            );
        }

        return $request;
    }

    protected function makeOptions(array $toAddress)
    {
        $options = array();
        $cartSubtotal = WC()->cart->subtotal;

        if ($this->payload['options']->eq('signature_required', 'yes')) {
            $options['signature_required'] = true;
        }

        // validate north american address
        if (in_array($toAddress['country'], array('CA', 'US'))) {
            $options['address_correction'] = true;
        }

        if($this->payload['options']->eq('flagship_insurance','yes') && $cartSubtotal > 100){

            $options['insurance'] = [
                "value" => $cartSubtotal,
                "description" => $this->getInsuranceDescription()
            ];
        }

        return $options;
    }

    protected function getInsuranceDescription()
    {
        $insuranceDescription = '';
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $insuranceDescription .= $cart_item['data']->get_title().',';

        }
        return rtrim($insuranceDescription,',');
    }
}
