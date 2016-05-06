<?php

class Flagship_Validation
{
    protected $client;
    protected $errors;

    public function __construct(Flagship_Client $client)
    {
        $this->client = $client;
        $this->errors = array();
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
            $this->errors[] = __($response->content[0], 'flagship-shipping');

            return $this->errors;
        }

        foreach ($response->content['errors'] as $error) {
            $this->errors = array_merge($this->errors, $error);
        }

        return $this->errors;
    }
}
