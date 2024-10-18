<?php

namespace FS\Components\Validation;

use FS\Context\ApplicationContext as Context;

class AddressEssentialValidator extends AbstractValidator
{
    public function validate($target, Context $context)
    {
        preg_match('/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/',$target['postal_code'],$matches);

        if(!$matches) {
            $context->alert()->error('Invalid postal code');
            return $target;
        }

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
            $context->alert()->warning('Address corrected to match with shipper\'s postal code.');

            $target['postal_code'] = $body['postal_code'];
            $target['state'] = $body['state'];
            $target['city'] = $body['city'];

            return $target;
        }

        if (!is_array($response->getErrors())) {
            return $target;
        }

        foreach ($response->getErrors() as $error) {
            $context->alert()->warning($error);
        }

        return $target;
    }
}
