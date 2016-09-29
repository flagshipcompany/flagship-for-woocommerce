<?php

namespace FS\Components\Validation;

class AddressValidator extends \FS\Components\AbstractComponent implements ValidatorInterface
{
    public function validate($target, \FS\Components\Notifier $notifier)
    {
        $client = $this->getApplicationContext()->getComponent('\\FS\\Components\\Http\\Client');

        $response = $client->get(
            '/addresses/integrity',
            $target
        );

        $body = $response->getBody();

        if ($response->isSuccessful() && is_array($body) && $body['is_valid']) {
            return $target;
        }

        // the address is not valid but the api provide a correction
        if ($response->isSuccessful() && !$body['is_valid']) {
            $notifier->warning(__('Address corrected to match with shipper\'s postal code.', FLAGSHIP_SHIPPING_TEXT_DOMAIN));

            $target['postal_code'] = $body['postal_code'];
            $target['state'] = $body['state'];
            $target['city'] = $body['city'];

            return $target;
        }

        foreach ($response->getError() as $error) {
            $notifier->warning($error);
        }

        return $target;
    }
}
