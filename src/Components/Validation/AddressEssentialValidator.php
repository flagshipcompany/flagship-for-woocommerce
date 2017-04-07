<?php

namespace FS\Components\Validation;

use FS\Context\ApplicationContext as Context;

class AddressEssentialValidator extends AbstractValidator
{
    public function validate($target, Context $context)
    {
        $response = $context->api()->get(
            '/addresses/integrity',
            $target
        );

        $body = $response->getContent();

        if ($response->isSuccessful() && is_array($body) && $body['is_valid']) {
            return $target;
        }

        // the address is not valid but the api provide a correction
        if ($response->isSuccessful() && !$body['is_valid']) {
            $context->alert(__('Address corrected to match with shipper\'s postal code.', FLAGSHIP_SHIPPING_TEXT_DOMAIN), 'warning');

            $target['postal_code'] = $body['postal_code'];
            $target['state'] = $body['state'];
            $target['city'] = $body['city'];

            return $target;
        }

        foreach ($response->getErrors() as $error) {
            $context->alert($error, 'warning');
        }

        return $target;
    }
}
