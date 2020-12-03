<?php

namespace FS\Components\Shipping\Request\Builder\Cart\PackageItems;

use FS\Components\Shipping\Request\Builder\BuilderInterface;

class ApiBuilder extends FallbackBuilder implements BuilderInterface
{
    public function makePackageItems($productItems, $payload)
    {
        $context = $this->getApplicationContext();

        $options = $context->option();
        $client = $context->api();
        $command = $context->command();
        $notifier = $context->alert();

        $availableBoxes = $this->getAvailableBoxes($options->get('package_box'), $productItems);

        if (count($availableBoxes) == 0) {
            return parent::makePackageItems($productItems, $payload);
        }

        $options->setValue('package_box', $availableBoxes);
        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderPacking');

        $response = $command->pack(
            $client,
            $factory->setPayload(array(
                'options' => $options,
                'productItems' => $productItems,
            ))->getRequest()
        );

        // when failed, we need to use fallback
        if (!$response->isSuccessful()) {
            return parent::makePackageItems($productItems, $payload);
        }

        $body = $response->getContent();
        $items = array();

        foreach ($body['packages'] as $package) {
            $items[] = array(
                'length' => $package['length'],
                'width' => $package['width'],
                'height' => $package['height'],
                'weight' => $package['weight'],
                'description' => 'product: '.implode(', ', $package['items']),
            );
        }

        $usedBoxes = $this->getBoxesUsed($body['packages'], $options->get('package_box'));
        $this->saveBoxes($usedBoxes);

        return $items;
    }

    protected function saveBoxes(array $boxes)
    {
        $this->boxes = $boxes;
    }

    protected function getBoxesUsed(array $packedBoxes, array $availableBoxes)
    {
        $packedBoxesCopy = $packedBoxes;

        foreach ($packedBoxes as $packedBoxKey => $packedBox) {
            $packedBoxDimensions = [$packedBox['length'], $packedBox['width'], $packedBox['height']];
            sort($packedBoxDimensions);

            foreach ($availableBoxes as $key => $box) {
                $boxDimensions = [$box['length'], $box['width'], $box['height']];
                sort($boxDimensions);

                if ($packedBox['box_model'] == $box['model_name'] && $packedBoxDimensions == $boxDimensions) {
                    $packedBoxesCopy[$packedBoxKey]['markup'] = isset($box['markup']) ? $box['markup'] : null;
                    break;
                }
            }
        }

        return $packedBoxesCopy;
    }

    //A package box with specified shipping classes should only be used when every item in the order has shipping class and the shipping classes of this box cover all the shipping classes of the items.
    protected function getAvailableBoxes(array $packageBoxes, array $items)
    {
        $boxesWithoutShippingClass = array_filter($packageBoxes, function($box) {
            return empty(trim($box['shipping_classes']));
        });
        $itemsWithoutClass = array_filter($items, function($item) {
            return empty($item['shipping_class']);
        });

        if (count($itemsWithoutClass) > 0) {
            return $boxesWithoutShippingClass;
        }

        $shippingClassBoxes = array_filter($packageBoxes, function($box) use ($items) {
            if (empty(trim($box['shipping_classes']))) {
                return false;
            }

            $classes = explode(';', trim($box['shipping_classes']));
            $classes = array_map(function($class) {
                return trim($class);
            }, $classes);
            $itemsInClasses = array_filter($items, function($item) use ($classes) {
                return in_array($item['shipping_class'], $classes);
            });

            return count($itemsInClasses) == count($items);
        });

        return $shippingClassBoxes;
    }
}
