<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Pickup extends Flagship_Component
{
    public function schedule($shipping)
    {
        $request = $this->get_single_pickup_schedule_request($shipping);
        $response = $this->flagship['client']->post(
            '/pickups',
            $request
        );

        if (!$response->is_success()) {
            $this->flagship['notification']->add('warning', 'Unable to schedule pick-up with FlagShip ID ('.$shipping['shipment_id'].')'.Flagship_Html::array2list($response->get_content()['errors']));

            return false;
        }

        return $response->get_content()['content'];
    }

    public function cancel($shipping)
    {
        $response = $this->flagship['client']->delete('/pickups/'.$shipping['shipment']['pickup']['id']);

        if ($response->get_code() != 204) {
            $this->flagship['notification']->add('warning', 'Unable to void pick-up with FlagShip ID ('.$shipping['shipment_id'].')'.Flagship_Html::array2list($response->get_content()['errors']));

            return false;
        }

        return true;
    }

    public function get_single_pickup_schedule_request($shipping)
    {
        $request = array(
            'address' => $this->flagship['address']->get_from(),
            'courier' => strtolower($shipping['shipment']['service']['courier_name']),
            'boxes' => count($shipping['shipment']['packages']),
            'weight' => 0,
            'date' => $shipping['date'],
            'from' => $this->flagship['options']->get('default_pickup_time_from', '09:00'),
            'until' => $this->flagship['options']->get('default_pickup_time_to', '17:00'),
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
}
