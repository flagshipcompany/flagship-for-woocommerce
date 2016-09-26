<?php

namespace FS\Components\Validation;

class SettingsValidator extends \FS\Components\AbstractComponent implements ValidatorInterface
{
    public function validate($target, \FS\Components\Notifier $notifier)
    {
        // if user set/update token, we need to use the latest entered one
        if (isset($target['token'])) {
            $this->getApplicationContext()->getComponent('\\FS\\Components\\Http\\Client')->setToken($target['token']);
        }

        // enabled?
        if ($target['enabled'] != 'yes') {
            $notifier->warning(__('FlagShip Shipping is disabled.', FLAGSHIP_SHIPPING_TEXT_DOMAIN));
        }

        // phone
        $phoneValidator = new PhoneValidator();
        $phoneValidator->validate($target['shipper_phone_number'], $notifier);

        // address
        $addressValidator = $this->getApplicationContext()->getComponent('\\FS\\Components\\Validation\\AddressValidator');
        $address = $addressValidator->validate(array(
            'postal_code' => $target['origin'],
            'state' => $target['freight_shipper_state'],
            'city' => $target['freight_shipper_city'],
            'country' => 'CA',
        ), $notifier);

        $target['origin'] = $address['postal_code'];
        $target['freight_shipper_state'] = $address['state'];
        $target['freight_shipper_city'] = $address['city'];

        // credentials
        if (!$target['shipper_person_name']) {
            $notifier->warning(__('Shipper person name is missing.', FLAGSHIP_SHIPPING_TEXT_DOMAIN));
        }

        if (!$target['shipper_company_name']) {
            $notifier->warning(__('Shipper company name is missing.', FLAGSHIP_SHIPPING_TEXT_DOMAIN));
        }

        if (!$target['shipper_phone_number']) {
            $notifier->warning(__('Shipper phone number is missing.', FLAGSHIP_SHIPPING_TEXT_DOMAIN));
        }

        if (!$target['freight_shipper_street']) {
            $notifier->warning(__('Shipper address\'s streetline is missing.', FLAGSHIP_SHIPPING_TEXT_DOMAIN));
        }

        // overall integrity, send mock quote request
        $integrityValidator = $this->getApplicationContext()->getComponent('\\FS\\Components\\Validation\\SettingsIntegrityValidator');
        $integrityValidator->validate($target, $notifier);

        return $target;
    }
}
