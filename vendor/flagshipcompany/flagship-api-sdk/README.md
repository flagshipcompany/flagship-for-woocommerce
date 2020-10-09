# flagship-api-sdk

Library to use FlagShip API

# License: MIT
Please go through the documentation at https://docs.smartship.io/ for all information about the API.

# Requirements

Composer

PHP 7.1

# Installation

```
composer require flagshipcompany/flagship-api-sdk : ^1.1
composer update
```

# Code Sample

```
<?php

use Flagship\Shipping\Flagship;
use Flagship\Shipping\Exceptions\QuoteException;

require_once './vendor/autoload.php';

/*
 * @params 
 * MY_ACCESS_TOKEN : use your Flagship token
 * For test environment use https://test-api.smartship.io and https://api.smartship.io for a live one
 * MY_WEBSITE : name of your website
 * API_VERSION : this is same as the tag number from github. Instead of master branch, download the latest tag. It is something like v1.1.x
 */

$flagship = new Flagship('MY_ACCESS_TOKEN', 'https://api.smartship.io','MY_WEBSITE','API_VERSION');

$payload = [
    'from' =>[
        "name"=> "FlagShip Courier Solutions",
        "attn"=> "FCS",
        "address"=> "Brunswick Blvd",
        "suite"=> "148",
        "city"=> "Pointe-Claire",
        "country"=> "CA",
        "state"=> "QC",
        "postal_code"=> "H9R5P9",
        "phone"=> "18663208383",
        "ext"=> "",
        "department"=> "Reception",
        "is_commercial"=> true
    ],
    "to" => [
        "name"=> "FlagShip Courier Solutions",
        "attn"=> "FCS",
        "address"=> "Brunswick Blvd",
        "suite"=> "148",
        "city"=> "Pointe-Claire",
        "country"=> "CA",
        "state"=> "QC",
        "postal_code"=> "H9R5P9",
        "phone"=> "18663208383",
        "ext"=> "",
        "department"=> "Reception",
        "is_commercial"=> true
    ],
    "packages"=> [
        "items"=> [
            [
                "width"=> 22,
                "height"=> 22,
                "length"=> 22,
                "weight"=> 22,
                "description"=> "Item description"
            ],

        ],
        "units"=> "imperial",
        "type"=> "package",
        "content"=> "goods"
    ],
    "payment"=> [
        "payer"=> "F"
    ],
    "options"=> [
        "insurance"=> [
            "value"=> 123.45,
            "description"=> "Children books"
        ],
        "signature_required"=> false,
        "saturday_delivery"=> false,
        "reference"=> "123 test",
        "driver_instructions"=> "Doorbell broken, knock on door",
        "address_correction"=> true,
        "return_documents_as"=> "url",
        "shipment_tracking_emails"=> "jbeans@company.com;shipping1@company.com"
    ]
];

try{
    $rates = $flagship->createQuoteRequest($payload)->execute();
    //$rates = $flagship->createQuoteRequest($payload)->setStoreName("My Awesome Store")->setOrderId(1234)->execute();
}
catch(QuoteException $e){
    echo $e->getMessage();
}

```

# Usage
```
<?php

require_once './vendor/autoload.php';

use Flagship\Shipping\Flagship;
use Flagship\Shipping\Exceptions\PrepareShipmentException;
use Flagship\Shipping\Exceptions\QuoteException;
use Flagship\Shipping\Exceptions\ConfirmShipmentException;
use Flagship\Shipping\Exceptions\GetDhlEcommRatesException;

/*
 * MY_WEBSITE and API_VERSION are optional parameters
 */
$flagship = new Flagship('MY_FLAGSHIP_ACCESS_TOKEN', 'MY_DOMAIN','MY_WEBSITE','API_VERSION');

try{
    //example prepare shipment request

    $request = $flagship->prepareShipmentRequest([

        'from'=>[ ... ],
        'to' => [ ... ],
        'packages' => [ ... ],
        ...  
  ]);

  $response = $request->execute();
}
catch(PrepareShipmentException $e){
    echo $e->getMessage()."\n";
}

try{
    //example get quotes request

    $request = $flagship->createQuoteRequest([

      'from'=>[ ... ],
      'to' => [ ... ],
      'packages' => [ ... ],
      ...  
  ]);

  $rates = $request->execute(); //returns a collection of rates
  $rates->getCheapest();
  $rates->getFastest();
  $rates->getByCourier('UPS');
  $rates->sortByPrice();
  $rates->sortByTime();
}
catch(QuoteException $e){
    echo $e->getMessage()."\n";
}

try{
    //example confirm shipment request

    $request = $flagship->confirmShipmentRequest([

      'from'=>[ ... ],
      'to' => [ ... ],
      'packages' => [ ... ],
       ...  
    ]);

  $confirmedShipment = $request->execute(); //returns a collection of rates
  $confirmedShipment->getLabel(); //returns regular label
  $confirmedShipment->getThermalLabel(); //returns thermal label
  $confirmedShipment->getTotal();
  ...
}
catch(ConfirmShipmentException $e){
    echo $e->getMessage()."\n";
}

try{

    //get DHL Ecommerce rates

    $payload = [
        "from"=> [
            "name"=> "Flagship Courier Solutions",
            "attn"=> "Reception",
            "address"=> "148 Brunswick",
            "suite"=> null,
            "department"=> " ",
            "country"=> "CA",
            "postal_code"=> "H9R 5P9",
            "city"=> "Pointe-Claire",
            "state"=> "QC",
            "phone"=> "5147390202",
            "ext"=> null,
            "is_commercial"=> true
        ],
        "to"=> [
            "name"=> "INRA",
            "attn"=> "Marie Martin",
            "address"=> "14 rue Girardet",
            "suite"=> null,
            "department"=> " ",
            "country"=> "FR",
            "postal_code"=> "54042",
            "city"=> "Nancy",
            "state"=> null,
            "phone"=> "383396892",
            "ext"=> null,
            "is_commercial"=> true
        ],
        "options"=> [
            "signature_required"=> false,
            "reference"=> null,
            "driver_instructions"=> null,
            "return_documents_as"=> "url",
            "address_correction"=> false,
            "shipment_tracking_emails"=> null,
            "insurance"=> [
                "value"=> 1200,
                "description"=> "Battle-ready saber"
            ]
        ],
        "payment"=> [
            "payer"=> "F"
        ],
        "accounts"=> [],
        "packages"=> [
            "units"=> "metric",
            "type"=> "package",
            "content"=> "goods",
            "items"=> [
                [
                    "width"=> "18",
                    "height"=> "18",
                    "length"=> "18",
                    "weight"=> "360",
                    "description"=> "Very nicely packed thing"
                ]
            ]
        ]
    ];

    $dhlEcommRates = $flagship->getDhlEcommRatesRequest($payload)->execute();
} catch(GetDhlEcommRatesException $e){
    echo $e->getMessage();
}

//Prepare Complete DHL Ecommerce Shipment

    $depotPayload = $depotPayload = [
                    "from"=> [
                        "name"=> "Flagship Courier Solutions",
                        "attn"=> "Reception",
                        "address"=> "Brunswick Boulevard",
                        "suite"=> "148",
                        "city"=> "Pointe-Claire",
                        "country"=> "CA",
                        "state"=> "QC",
                        "postal_code"=> "H9R5P9",
                        "phone"=> "514-739-0202"
                    ],
                    "to"=> [
                        "is_commercial"=> true,
                        "name"=> "DHL eCommerce",
                        "attn"=> "DHL eCommerce",
                        "address"=> "4-355 Admiral Blvd.",
                        "city"=> "Mississauga",
                        "country"=> "CA",
                        "state"=> "ON",
                        "postal_code"=> "L5T 2N1",
                        "phone"=> "647-588-7155"
                    ],
                    "packages"=> [
                        "units"=> "metric",
                        "type"=> "package",
                        "items"=> [
                            [
                                "width"=> "31",
                                "height"=> "31",
                                "length"=> "31",
                                "weight"=> "1",
                                "description"=> "Very nicely packed thing"
                            ]
                        ]
                    ],
                    "service"=> [
                        "courier_name"=> "ups",
                        "courier_code"=> 65
                    ]
                ];


    $confirmShipmentIds = [];

    $confirmShipmentIds[] = $flagship->confirmShipmentRequest($confirmShipmentPayload1)->execute()->getId();
    $confirmShipmentIds[] = $flagship->confirmShipmentRequest($confirmShipmentPayload2)->execute()->getId();

    $completeDhlEcommShipment = $flagship->createCompleteDhlEcommShipment($confirmShipmenIds,"MyNewManifest",$depotPayload);


```
