<?php

class Flagship_Validation
{
    protected $client;
    protected $errors;

    public function __construct(Flagship_Client $client = null)
    {
        $this->set_client($client);
        $this->errors = array();
    }

    public function set_client(Flagship_Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    // return errors
    public function address($postal_code, $state, $city = '', $country = 'CA')
    {
        $address = array(
            'city' => $city,
            'country' => $country,
            'state' => $state,
            'postal_code' => $postal_code,
        );

        $response = $this->client->get('/addresses/integrity', $address);

        if ($response->is_success() && $response->content['content']['is_valid']) {
            return $this->errors;
        }

        // the address is not valid but the api provide a correction
        if ($response->is_success()) {
            return $response->content;
        }

        if ($response->code == 403) {
            $this->errors[] = $response->content[0];

            return $this->errors;
        }

        foreach ($response->content['errors'] as $error) {
            $this->errors = array_merge($this->errors, $error);
        }

        return $this->errors;
    }

    public function phone($phone)
    {
        preg_match('/^\+?[1]?[-. ]?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', $phone, $matches);

        if (!$matches) {
            $this->errors[] = $phone.__(' is not a valid phone number.', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        }

        return $this->errors;
    }

    public function settings($settings)
    {
        $request = array(
            'from' => array(
                'country' => 'CA',
                'state' => $settings['freight_shipper_state'],
                'city' => $settings['freight_shipper_city'],
                'postal_code' => $settings['origin'],
                'address' => $settings['freight_shipper_street'],
                'name' => $settings['shipper_company_name'],
                'attn' => $settings['shipper_person_name'],
                'phone' => $settings['shipper_phone_number'],
                'ext' => $settings['shipper_phone_ext'],
            ),
            'to' => array(
                'name' => 'FLS Integrity',
                'attn' => 'FLS Guard',
                'address' => '148 Brunswick',
                'city' => 'POINTE-CLAIRE',
                'state' => 'QC',
                'country' => 'CA',
                'postal_code' => 'H9R5P9',
                'phone' => '1 866 320 8383', // no such a field in the shipping!?
            ),
            'packages' => array(
                'items' => array(
                    array(
                        'width' => 5,
                        'height' => 4,
                        'length' => 3,
                        'weight' => 2,
                        'description' => 'For FLS Settings Integrity',
                    ),
                ),
                'units' => 'imperial',
                'type' => 'package',
            ),
            'payment' => array(
                'payer' => 'F',
            ),
        );

        $response = $this->client->post('/ship/rates', $request);

        if (!$response->is_success()) {
            $this->errors = $response->get_content()['errors'];
        }

        return $this->errors;
    }
}
