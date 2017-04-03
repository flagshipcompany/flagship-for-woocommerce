<?php

namespace FS\Components\Shipping\Controller;

use FS\Components\AbstractComponent;

class PickupController extends AbstractComponent
{
    public function schedulePickup($orders, $pickupPostIds = array())
    {
        $options = $this->getApplicationContext()
            ->_('\\FS\\Components\\Options');
        $requestFactory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Factory\\MultipleOrdersPickupRequestFactory');
        $orderShippingsFactory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Order\\Factory\\FlattenOrderShippingsFactory');
        $client = $this->getApplicationContext()
            ->_('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));
        $command = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Command');

        // group shipping orders by courier and service type
        $flattenOrderShippings = $orderShippingsFactory->getFlattenOrderShippings($orders);

        foreach ($flattenOrderShippings as $orderShippings) {
            $response = $command->pickup(
                $client,
                $requestFactory->setPayload(array(
                    'orders' => $orderShippings['orders'],
                    'courier' => $orderShippings['courier'],
                    'type' => $orderShippings['type'],
                    'options' => $options,
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

    public function voidPickup($pickupPostIds)
    {
        $options = $this->getApplicationContext()
            ->_('\\FS\\Components\\Options');

        $client = $this->getApplicationContext()
            ->_('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));

        foreach ($pickupPostIds as $pickupPostId) {
            $pickupId = get_post_meta($pickupPostId, 'id', true);

            if (!$pickupId) {
                continue;
            }

            $response = $client->delete('/pickups/'.$pickupId);

            if (!$response->isSuccessful() && $response->getCode() != 409) {
                continue;
            }

            update_post_meta($pickupPostId, 'cancelled', true);
        }

        $sendback = add_query_arg(array('post_type' => 'flagship_pickup'), '');
        wp_redirect(esc_url_raw($sendback));
        exit();
    }

    public function reschedulePickup($pickupPostIds)
    {
        $options = $this->getApplicationContext()
            ->_('\\FS\\Components\\Options');
        $requestFactory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Factory\\MultipleOrdersPickupRequestFactory');
        $shopFactory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shop\\Factory\\ShopFactory');
        $orderShippingsFactory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Order\\Factory\\FlattenOrderShippingsFactory');
        $client = $this->getApplicationContext()
            ->_('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));
        $command = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Command');

        foreach ($pickupPostIds as $pickupPostId) {
            $pickupId = get_post_meta($pickupPostId, 'id', true);
            $cancelled = get_post_meta($pickupPostId, 'cancelled', true);

            if (!$pickupId || !$cancelled) {
                continue;
            }

            $orderIds = get_post_meta($pickupPostId, 'order_ids', true);

            $this->schedulePickup($shopFactory->getModel(
                \FS\Components\Shop\Factory\FactoryInterface::RESOURCE_ORDER_COLLECTION,
                array('ids' => $orderIds)
            ), $pickupPostIds);
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
