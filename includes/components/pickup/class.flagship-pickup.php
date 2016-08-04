<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Pickup extends Flagship_Component
{
    public function initialize($order)
    {
        $this->ctx['order']->initialize($order);
        $this->ctx['notification']->scope('shop_order', array('id' => $this->ctx['order']->get_id()));

        return $this;
    }

    public function schedule($shipping)
    {
        $shipment = $this->ctx['order']->get_meta('flagship_shipping_raw');
        $shipment_id = $this->ctx['shipment']->get_shipment_id($shipment);

        if (!$shipment_id) {
            return $this;
        }

        $shipping = array(
            'order' => $order,
            'shipment' => $shipment,
            'shipment_id' => $shipment_id,
            'date' => $date,
        );

        $request = $this->get_single_pickup_schedule_request($shipping);
        $response = $this->ctx['client']->post(
            '/pickups',
            $request
        );

        if (!$response->is_success()) {
            $this->ctx['notification']->warning(__('Unable to schedule pick-up with FlagShip ID').' ('.$shipping['shipment_id'].')');

            return $this;
        }

        $pickup = $response->get_body();

        if (!$pickup) {
            return $this;
        }

        $shipment['pickup'] = $pickup;

        $this->ctx['order']->set_meta('flagship_shipping_raw', $shipment);

        return $this;
    }

    public function cancel()
    {
        $shipment = $this->ctx['order']->get_meta('flagship_shipping_raw');

        $shipment_id = $this->ctx['shipment']->get_shipment_id($shipment);

        if (!$shipment || !$shipment_id || !isset($shipment['pickup'])) {
            return $this;
        }

        $shipping = array(
            'shipment' => $shipment,
            'shipment_id' => $shipment_id,
        );

        $response = $this->ctx['client']->delete('/pickups/'.$shipping['shipment']['pickup']['id']);

        if ($response->get_code() != 204) {
            $this->ctx['notification']->warning(__('Unable to void pick-up with FlagShip Pickup ID').' ('.$shipping['shipment']['pickup']['id'].')');

            return $this;
        }

        unset($shipment['pickup']);
        $this->ctx['order']->set_meta('flagship_shipping_raw', $shipment);

        return $this;
    }

    public function get_single_pickup_schedule_request($shipping)
    {
        $request = array(
            'address' => $this->ctx['address']->get_from(),
            'courier' => strtolower($shipping['shipment']['service']['courier_name']),
            'boxes' => count($shipping['shipment']['packages']),
            'weight' => 0,
            'date' => $shipping['date'],
            'from' => $this->ctx['options']->get('default_pickup_time_from', '09:00'),
            'until' => $this->ctx['options']->get('default_pickup_time_to', '17:00'),
            'units' => 'imperial',
            'location' => 'Reception',
            'to_country' => $order->shipping_country,
            'is_ground' => false,
        );

        $shipment = $shipping['shipment'];

        foreach ($shipment['packages'] as $package) {
            $request['weight'] += $package['weight'];
        }

        if (strtolower($shipment['service']['courier_name']) == 'fedex'
            && strpos($shipment['service']['courier_code'], 'FedexGround') !== false) {
            $request['is_ground'] = true;
        }

        return $request;
    }

    public function get_multiple_pickup_schedule_request($courier_shippings)
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
