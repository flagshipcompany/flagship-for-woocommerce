<?php

namespace FS\Components\Validation;

use FS\Context\ApplicationContext as Context;

class IntegrityValidator extends AbstractValidator
{
    public function validate($target, Context $context)
    {
        $response = $context->api()->get('/v2/validate-token', []);

        if (!$response->isSuccessful()) {
            $statusCode = $response->getStatusCode();
            
            if ($statusCode > 400) {
                $errorMsg = 'Invalid FlagShip API token';
            }
            
            $context->alert()->error(sprintf('<strong>%s</strong><br/>', $errorMsg));

            // show 'FlagShip API Error: ' first
            $context->alert()->getScenario()->reverseOrdering('error');
        }

        return $target;
    }
}
