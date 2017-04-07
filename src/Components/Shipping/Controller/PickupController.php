<?php

namespace FS\Components\Shipping\Controller;

use FS\Components\AbstractComponent;
use FS\Components\Web\RequestParam as Req;
use FS\Context\ApplicationContext as App;

class PickupController extends AbstractComponent
{
    public function schedulePickup(Req $request, App $context, $orderIds, $pickupPostIds = array())
    {
        $requestFactory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\MultipleOrdersPickup');
        $orderShippingsFactory = $context
            ->_('\\FS\\Components\\Order\\Factory\\FlattenOrderShippingsFactory');

        $orders = $context->_('\\FS\\Components\\Shop\\Factory\\ShopFactory')->resolve(
            \FS\Components\Shop\Factory\ShopFactory::RESOURCE_ORDER_COLLECTION,
            ['ids' => $orderIds]
        );

        // group shipping orders by courier and service type
        $flattenOrderShippings = $orderShippingsFactory->getFlattenOrderShippings($orders);

        foreach ($flattenOrderShippings as $orderShippings) {
            $response = $context->command()->pickup(
                $context->api(),
                $requestFactory->setPayload(array(
                    'orders' => $orderShippings['orders'],
                    'courier' => $orderShippings['courier'],
                    'type' => $orderShippings['type'],
                    'options' => $context->option(),
                    'date' => date('Y-m-d'),
                ))->getRequest()
            );

            if ($response->isSuccessful()) {
                $pickup = $response->getContent();

                $pickup['order_ids'] = $orderShippings['ids'];
                $pickup['pickup_date'] = $pickup['date'];

                // replace existing pickup post if and only if there is one cancelled pickup to reschdule
                // when user select many pickups, we can possibly combine them into fewer or equals pickups
                $this->savePickup($pickup, (count($pickupPostIds) == 1 ? $pickupPostIds[0] : null));
            }
        }

        $sendback = add_query_arg(array(
            'post_type' => 'flagship_pickup',
        ), '');

        \wp_redirect(esc_url_raw($sendback));
        exit();
    }

    public function voidPickup(Req $request, App $context, $pickupPostIds)
    {
        foreach ($pickupPostIds as $pickupPostId) {
            $pickupId = get_post_meta($pickupPostId, 'id', true);

            if (!$pickupId) {
                continue;
            }

            $response = $context->api()->delete('/pickups/'.$pickupId);

            if (!$response->isSuccessful() && $response->getStatusCode() != 409) {
                continue;
            }

            update_post_meta($pickupPostId, 'cancelled', true);
        }

        $sendback = add_query_arg(array('post_type' => 'flagship_pickup'), '');
        wp_redirect(esc_url_raw($sendback));
        exit();
    }

    public function reschedulePickup(Req $request, App $context, $pickupPostIds)
    {
        $requestFactory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\MultipleOrdersPickup');
        $orderShippingsFactory = $context
            ->_('\\FS\\Components\\Order\\Factory\\FlattenOrderShippingsFactory');

        foreach ($pickupPostIds as $pickupPostId) {
            $pickupId = get_post_meta($pickupPostId, 'id', true);
            $cancelled = get_post_meta($pickupPostId, 'cancelled', true);

            if (!$pickupId || !$cancelled) {
                continue;
            }

            $orderIds = get_post_meta($pickupPostId, 'order_ids', true);

            $this->schedulePickup($request, $context, $orderIds, $pickupPostIds);
        }

        $sendback = add_query_arg(array('post_type' => 'flagship_pickup', 'ids' => implode(',', $pickupPostIds)), '');
        \wp_redirect(esc_url_raw($sendback));
        exit();
    }

    public function savePickup($pickup = array(), $id = null)
    {
        if (!$id) {
            $pickup_id = wp_insert_post(array(
                'post_title' => 'pickup title',
                'post_content' => '',
                'post_type' => 'flagship_pickup',
                'post_status' => 'publish',
                'meta_input' => $pickup,
            ));

            return $pickup_id;
        }

        foreach ($pickup as $key => $value) {
            update_post_meta($id, $key, $value);
        }

        return $id;
    }
}
