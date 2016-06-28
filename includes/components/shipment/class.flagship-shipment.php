<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Shipment extends Flagship_Component
{
    protected $shipment = null;
    protected $service = null;
    protected $type = 'NOT_CREATED';

    public function confirm()
    {
        $this->ctx->load('Confirmation');

        $shipping = $this->ctx['confirmation']->confirm($this->ctx['order']->get_order());

        if (!$shipping) {
            return $this;
        }

        $this->ctx['order']
            ->remove_meta('flagship_shipping_requote_rates')
            ->set_meta('flagship_shipping_shipment_id', $shipping['content']['shipment_id'])
            ->set_meta('flagship_shipping_shipment_tracking_number', $shipping['content']['tracking_number'])
            ->set_meta('flagship_shipping_courier_name', $shipping['content']['service']['courier_name'])
            ->set_meta('flagship_shipping_courier_service_code', $shipping['content']['service']['courier_code'])
            ->set_meta('flagship_shipping_raw', $shipping['content']);

        return $this;
    }

    public function requote()
    {
        $this->ctx->load('Quoter');

        $this->ctx['notification']->scope('shop_order', array('id' => $this->ctx['order']->get_id()));

        $rates = $this->ctx['quoter']->requote($this->ctx['order']->get_order());

        if ($rates) {
            $this->ctx['order']->set_meta('flagship_shipping_requote_rates', $rates);
        }

        return $this;
    }

    public function cancel()
    {
        $this->ctx['notification']->scope('shop_order', array('id' => $this->ctx['order']->get_id()));

        $shipment = $this->ctx['order']->get_meta('flagship_shipping_raw');

        $shipment_id = $this->get_shipment_id($shipment);

        if (!$shipment_id) {
            return $this;
        }

        $response = $this->ctx['client']->delete('/ship/shipments/'.$shipment_id);

        if (!$response->is_success()) {
            $this->ctx['notification']->add('warning', 'Unable to void shipment with FlagShip ID ('.$shipment_id.')'.$this->ctx['html']->ul($response->get_content()['errors']));

            return $this;
        }

        if (empty($shipment['pickup'])) {
            $this->ctx['order']->remove_meta('flagship_shipping_raw');

            return $this;
        }

        $this->ctx->load('Pickup');

        $this->ctx['pickup']
            ->initialize($this->ctx['order']->get_order())
            ->cancel();

        $this->ctx['order']->remove_meta('flagship_shipping_raw');

        return $this;
    }

    public function get_order_id()
    {
        return $this->ctx['order']->get_id();
    }

    public function initialize($order)
    {
        $this->ctx['order']->import($order);

        $this->shipment = $this->ctx['order']->get_meta('flagship_shipping_raw');

        if ($this->shipment) {
            $this->type = 'CREATED';

            return $this;
        }

        $this->service = $this->get_service();

        if ($this->service['provider'] == FLAGSHIP_SHIPPING_PLUGIN_ID) {
            $this->type = 'NOT_CREATED';

            return $this;
        }

        return 'UNAVAILABLE';
    }

    public function get_view_data()
    {
        $data = array();

        if ($this->type == 'CREATED') {
            $data['type'] = 'created';
            $data['shipment'] = $this->shipment;

            return $data;
        }

        $requote_rates = $this->ctx['order']->get_meta('flagship_shipping_requote_rates');

        if ($requote_rates) {
            $data['requote_rates'] = $requote_rates;
        }

        if ($this->type == 'NOT_CREATED') {
            $data['type'] = 'create';
            $data['service'] = $this->service;
            $data['cod'] = array(
                'currency' => strtoupper(get_woocommerce_currency()),
            );

            return $data;
        }

        $data['type'] = 'unavailable';

        return $data;
    }

    public function get_service()
    {
        if ($this->service) {
            return $this->service;
        }

        $this->service = $this->ctx['order']->get_shipping_service();

        return $this->service;
    }

    public function get_shipment_id($shipment)
    {
        $shipment_id = sanitize_text_field($_POST['flagship_shipping_shipment_id']);

        if (empty($shipment) || empty($shipment_id) || $shipment_id != $shipment['shipment_id']) {
            $this->ctx['notification']->add('warning', 'Unable to access shipment with FlagShip ID ('.$shipment_id.')');

            return false;
        }

        return $shipment_id;
    }
}
