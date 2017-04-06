<?php

namespace FS\Components\Validation;

use FS\Context\ApplicationContext as Context;

class SettingsValidator extends AbstractValidator
{
    public function validate($target, Context $context)
    {
        $factory = $context->factory('\\FS\\Components\\Validation\\Factory\\ValidatorFactory');

        // if user set/update token, we need to use the latest entered one
        if (isset($target['token'])) {
            $context->_('\\FS\\Components\\Http\\Client')->setToken($target['token']);
        }

        // enabled?
        if ($target['enabled'] != 'yes') {
            $context->alert(__('FlagShip Shipping is disabled.', FLAGSHIP_SHIPPING_TEXT_DOMAIN), 'warning');
        }

        // phone
        $phoneValidator = $factory->resolve('Phone');
        $phoneValidator->validate($target['shipper_phone_number'], $context);

        // address
        $addressValidator = $factory->resolve('AddressEssential');
        $address = $addressValidator->validate([
            'postal_code' => $target['origin'],
            'state' => $target['freight_shipper_state'],
            'city' => $target['freight_shipper_city'],
            'country' => 'CA',
        ], $context);

        $target['origin'] = $address['postal_code'];
        $target['freight_shipper_state'] = $address['state'];
        $target['freight_shipper_city'] = $address['city'];

        // credentials
        if (!$target['shipper_person_name']) {
            $context->alert(__('Shipper person name is missing.', FLAGSHIP_SHIPPING_TEXT_DOMAIN), 'warning');
        }

        if (!$target['shipper_company_name']) {
            $context->alert(__('Shipper company name is missing.', FLAGSHIP_SHIPPING_TEXT_DOMAIN), 'warning');
        }

        if (!$target['shipper_phone_number']) {
            $context->alert(__('Shipper phone number is missing.', FLAGSHIP_SHIPPING_TEXT_DOMAIN), 'warning');
        }

        if (!$target['freight_shipper_street']) {
            $context->alert(__('Shipper address\'s streetline is missing.', FLAGSHIP_SHIPPING_TEXT_DOMAIN), 'warning');
        }

        // overall integrity, send mock quote request
        $integrityValidator = $factory->resolve('Integrity');
        $integrityValidator->validate($target, $context);

        return $target;
    }
}
