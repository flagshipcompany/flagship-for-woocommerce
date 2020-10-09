<?php

$package = <<<PACKAGE
{
    "contents": {
        "8f7d807e1f53eff5f9efbe5cb81090fb": {
            "key": "8f7d807e1f53eff5f9efbe5cb81090fb",
            "product_id": 839,
            "variation_id": 0,
            "variation": [],
            "quantity": 1,
            "data_hash": "b5c1d5ca8bae6d4896cf1807cdf763f0",
            "line_tax_data": {
                "subtotal": [],
                "total": []
            },
            "line_subtotal": 100,
            "line_subtotal_tax": 0,
            "line_total": 100,
            "line_tax": 0,
            "data": {}
        }
    },
    "contents_cost": 100,
    "applied_coupons": [],
    "user": {
        "ID": 1
    },
    "destination": {
        "country": "CN",
        "state": "CN18",
        "postcode": "430063",
        "city": "wuhan",
        "address": "12 xxxdcdf",
        "address_1": "12 xxxdcdf",
        "address_2": "ggghhhh"
    },
    "cart_subtotal": 100,
    "rates": []
}
PACKAGE
;

return json_decode($package, true);
