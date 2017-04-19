<?php

namespace FS\Components\Validation;

use FS\Context\ApplicationContext as Context;

class IntegrityValidator extends AbstractValidator
{
    public function validate($target, Context $context)
    {
        $request = array(
            'from' => array(
                'country' => 'CA',
                'state' => $target['freight_shipper_state'],
                'city' => $target['freight_shipper_city'],
                'postal_code' => $target['origin'],
                'address' => $target['freight_shipper_street'],
                'name' => $target['shipper_company_name'],
                'attn' => $target['shipper_person_name'],
                'phone' => $target['shipper_phone_number'],
                'ext' => $target['shipper_phone_ext'],
            ),
            'to' => array(
                'name' => 'FLS Integrity Test',
                'attn' => 'FLS Guard',
                'address' => '148 Brunswick',
                'city' => 'POINTE-CLAIRE',
                'state' => 'QC',
                'country' => 'CA',
                'postal_code' => 'H9R5P9',
                'phone' => '1 866 320 8383', // no such a field in the shipping!?
            ),
            'packages' => array(
                'items' => array(
                    array(
                        'width' => 5,
                        'height' => 4,
                        'length' => 3,
                        'weight' => 2,
                        'description' => 'For WooCommerce Plugin Settings Integrity',
                    ),
                ),
                'units' => 'imperial',
                'type' => 'package',
            ),
            'payment' => array(
                'payer' => 'F',
            ),
        );

        $response = $context->api()->post('/ship/rates', $request);

        if (!$response->isSuccessful()) {
            $context->alert()->error('<strong>Shipping Integrity Failure:</strong> <br/>');

            // show 'FlagShip API Error: ' first
            $context->alert()->getScenario()->reverseOrdering('error');
        }

        return $target;
    }
}
