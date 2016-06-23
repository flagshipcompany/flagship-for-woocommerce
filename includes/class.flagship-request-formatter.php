<?php

class Flagship_Request_Formatter
{
    public static function get_flagship_shipping_service($order)
    {
        $shipping_methods = $order->get_shipping_methods();

        list($provider, $courier_name, $courier_code, $courier_desc, $date) = explode('|', $shipping_methods[key($shipping_methods)]['method_id']);

        return array(
            'provider' => $provider,
            'courier_name' => strtolower($courier_name),
            'courier_code' => $courier_code,
            'courier_desc' => $courier_desc,
            'date' => $date,
        );
    }

    public static function get_multiple_pickup_schedule_request($courier_shippings)
    {
        $flagship = Flagship_Application::get_instance();

        $pickup_requests = array();

        if ($courier_shippings['purolator']) {
            $request = array(
                'address' => $flagship['address']->get_from(),
                'courier' => 'purolator',
                'boxes' => 0,
                'weight' => 0,
                'date' => '',
                'from' => $flagship['options']->get('default_pickup_time_from', '09:00'),
                'until' => $flagship['options']->get('default_pickup_time_to', '17:00'),
                'units' => 'imperial',
                'location' => 'Reception',
                'to_country' => 'CA',
                'is_ground' => false,
                'order_ids' => array(),
            );

            foreach ($courier_shippings['purolator'] as $shipping) {
                $request['order_ids'][] = $shipping['order']->id;
                $request['boxes'] += count($shipping['shipment']['packages']);

                $weight = 0;
                foreach ($shipping['shipment']['packages'] as $package) {
                    $weight += intval($package['weight']);
                }

                $request['weight'] += $weight;

                // pickup date set as the furthermost date in all shipments
                $request['date'] = ($request['date'] && strtotime($request['date']) > strtotime($shipping['date'])) ?  $request['date'] : $shipping['date'];
            }

            $pickup_requests[] = $request;
        }

        if ($courier_shippings['ups']) {
            $requests = array(
                'domestic' => array(
                    'address' => $flagship['address']->get_from(),
                    'courier' => 'ups',
                    'boxes' => 0,
                    'weight' => 0,
                    'date' => '',
                    'from' => $flagship['options']->get('default_pickup_time_from', '09:00'),
                    'until' => $flagship['options']->get('default_pickup_time_to', '17:00'),
                    'units' => 'imperial',
                    'location' => 'Reception',
                    'to_country' => 'CA',
                    'is_ground' => false,
                    'order_ids' => array(),
                ),
                'international' => array(
                    'address' => $flagship['address']->get_from(),
                    'courier' => 'ups',
                    'boxes' => 0,
                    'weight' => 0,
                    'date' => '',
                    'from' => $flagship['options']->get('default_pickup_time_from', '09:00'),
                    'until' => $flagship['options']->get('default_pickup_time_to', '17:00'),
                    'units' => 'imperial',
                    'location' => 'Reception',
                    'to_country' => '',
                    'is_ground' => false,
                    'order_ids' => array(),
                ),
            );

            foreach ($courier_shippings['ups'] as $shipping) {
                $is_domestic = ($shipping['order']->shipping_country == $requests['domestic']['to_country']);

                $type = ($is_domestic ? 'domestic' : 'international');

                $requests[$type]['order_ids'][] = $shipping['order']->id;

                $requests[$type]['to_country'] = $shipping['order']->shipping_country;
                $requests[$type]['boxes'] += count($shipping['shipment']['packages']);

                $weight = 0;
                foreach ($shipping['shipment']['packages'] as $package) {
                    $weight += intval($package['weight']);
                }

                $requests[$type]['weight'] += $weight;

                // pickup date set as the furthermost date in all shipments
                $requests[$type]['date'] = ($requests[$type]['date'] && strtotime($requests[$type]['date']) > strtotime($shipping['date'])) ?  $requests[$type]['date'] : $shipping['date'];
            }

            foreach ($requests as $type => $request) {
                if ($request['boxes'] && $request['weight']) {
                    $pickup_requests[] = $request;
                }
            }
        }

        if ($courier_shippings['fedex']) {
            $requests = array(
                'domestic' => array(
                    'address' => $flagship['address']->get_from(),
                    'courier' => 'fedex',
                    'boxes' => 0,
                    'weight' => 0,
                    'date' => '',
                    'from' => $flagship['options']->get('default_pickup_time_from', '09:00'),
                    'until' => $flagship['options']->get('default_pickup_time_to', '17:00'),
                    'units' => 'imperial',
                    'location' => 'Reception',
                    'to_country' => 'CA',
                    'is_ground' => false,
                    'order_ids' => array(),
                ),
                'domestic_ground' => array(
                    'address' => $flagship['address']->get_from(),
                    'courier' => 'fedex',
                    'boxes' => 0,
                    'weight' => 0,
                    'date' => '',
                    'from' => $flagship['options']->get('default_pickup_time_from', '09:00'),
                    'until' => $flagship['options']->get('default_pickup_time_to', '17:00'),
                    'units' => 'imperial',
                    'location' => 'Reception',
                    'to_country' => 'CA',
                    'is_ground' => true,
                    'order_ids' => array(),
                ),
                'international' => array(
                    'address' => $flagship['address']->get_from(),
                    'courier' => 'fedex',
                    'boxes' => 0,
                    'weight' => 0,
                    'date' => '',
                    'from' => $flagship['options']->get('default_pickup_time_from', '09:00'),
                    'until' => $flagship['options']->get('default_pickup_time_to', '17:00'),
                    'units' => 'imperial',
                    'location' => 'Reception',
                    'to_country' => '',
                    'is_ground' => false,
                    'order_ids' => array(),
                ),
                'international_ground' => array(
                    'address' => $flagship['address']->get_from(),
                    'courier' => 'fedex',
                    'boxes' => 0,
                    'weight' => 0,
                    'date' => '',
                    'from' => $flagship['options']->get('default_pickup_time_from', '09:00'),
                    'until' => $flagship['options']->get('default_pickup_time_to', '17:00'),
                    'units' => 'imperial',
                    'location' => 'Reception',
                    'to_country' => '',
                    'is_ground' => true,
                    'order_ids' => array(),
                ),
            );

            foreach ($courier_shippings['fedex'] as $shipping) {
                $is_domestic = ($shipping['order']->shipping_country == $requests['domestic']['to_country']);
                $is_ground = (strpos($shipping['shipment']['service']['courier_code'], 'FedexGround') !== false);

                $type = ($is_domestic ? 'domestic' : 'international').($is_ground ? '_ground' : '');

                $requests[$type]['order_ids'][] = $shipping['order']->id;

                $requests[$type]['to_country'] = $shipping['order']->shipping_country;
                $requests[$type]['boxes'] += count($shipping['shipment']['packages']);

                $weight = 0;
                foreach ($shipping['shipment']['packages'] as $package) {
                    $weight += intval($package['weight']);
                }

                $requests[$type]['weight'] += $weight;

                if ($is_ground) {
                    $shipping['date'] = date('Y-m-d', strtotime($shipping['date'].' -1 day'));
                }

                // pickup date set as the furthermost date in all shipments
                $requests[$type]['date'] = ($requests[$type]['date'] && strtotime($requests[$type]['date']) > strtotime($shipping['date'])) ?  $requests[$type]['date'] : $shipping['date'];
            }

            foreach ($requests as $type => $request) {
                if ($request['boxes'] && $request['weight']) {
                    $pickup_requests[] = $request;
                }
            }
        }

        return $pickup_requests;
    }
}
