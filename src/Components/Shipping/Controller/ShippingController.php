<?php

namespace FS\Components\Shipping\Controller;

use FS\Components\AbstractComponent;
use FS\Components\Web\RequestParam as Req;
use FS\Context\ApplicationContext as Context;

class ShippingController extends AbstractComponent
{
    public function calculate(Req $request, Context $context, $package, $method)
    {
        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingCartRate');
        $notifier = $context
            ->alert();

        $rateProcessorFactory = $context
            ->_('\\FS\\Components\\Shipping\\RateProcessor\\Factory\\RateProcessorFactory');

        // no shipping address, alert customer
        if (empty($package['destination']['postcode'])) {
            $context->alert()->notice('Add shipping address to get shipping rates! (click "Calculate Shipping")');

            return;
        }

        if (!$context->option('token')) {
            $context->alert()->error(MetaboxController::$tokenMissingMessage);

            return;
        }

        $options = $context->option();
        $edhlResponse = null;

        $response = $context->command()->quote(
            $context->api(),
            $factory->setPayload([
                'package' => $package,
                'options' => $options,
                'notifier' => $notifier,
            ])->getRequest()
        );

        if ($this->isEdhlRatesApplicable($package['destination'], $options)) {
            $edhlRatefactory = $context->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingCartEdhlRate');
            $options->setTempValue('default_package_box_split', 'yes'); //Everything in one box
            $edhlResponse = $context->command()->edhlQuote(
                $context->api(),
                $edhlRatefactory->setPayload([
                    'package' => $package,
                    'options' => $options,
                    'notifier' => $notifier,
                ])->getRequest()
            );                
        }

        if (!$response->isSuccessful() && (is_null($edhlResponse) || !$edhlResponse->isSuccessful())) {
            $errorMsg = $response->getStatusCode() === 403 ? MetaboxController::$tokenInvalidMessage : MetaboxController::$noRatesMessage;
            $context->alert()->error($errorMsg);

            return;
        }

        $rates = $response->getContent();

        if (!is_null($edhlResponse) && $edhlResponse->isSuccessful()) {
            $rates = array_merge($rates, $edhlResponse->getContent());
        }

        $rates = $rateProcessorFactory
            ->resolve('ProcessRate')
            ->getProcessedRates($rates, [
                'factory' => $rateProcessorFactory,
                'options' => $context->option(),
                'instanceId' => property_exists($method, 'instance_id') ? $method->instance_id : false,
                'methodId' => $context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID'),
                'extra_info' => $factory->getExtraReqInfo(),
            ]);

        foreach ($rates as $rate) {
            //Set the method id to rate id so that the selected rate will show up in backend
            $method->id = $rate['id'];
            $method->add_rate($rate);
        }
    }

    protected function isEdhlRatesApplicable($destination, $options)
    {;
        return $options->get('allow_dhl_ecommerce_rates', 'no') == 'yes' && isset($destination['country']) && $destination['country'] != 'CA';
    }
}
