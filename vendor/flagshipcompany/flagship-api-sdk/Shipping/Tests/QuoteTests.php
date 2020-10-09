<?php

namespace Flagship\Shipping\Tests;
use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Collections\RatesCollection;
use Flagship\Shipping\Objects\Rate;
use Flagship\Shipping\Requests\QuoteRequest;
use Flagship\Shipping\Exceptions\QuoteException;

class QuoteTests extends TestCase{

    public function testGetCheapest(){
        $this->assertNotNull($this->rates->getCheapest());
        $this->assertInstanceOf(Rate::class,$this->rates->getCheapest());
        $this->assertSame("standard",$this->rates->getCheapest()->getFlagshipCode());
    }

    public function testGetFastest(){
        $this->assertNotNull($this->rates->getFastest());
        $this->assertInstanceOf(Rate::class,$this->rates->getFastest());
        $this->assertSame("2019-12-13",$this->rates->getFastest()->getDeliveryDate());
    }

    public function testGetByCourier(){
        $this->assertInstanceOf(RatesCollection::class, $this->rates->getByCourier('fedex'));
        $this->expectException(QuoteException::class);
        $this->assertNotNull($this->rates->getByCourier('dhl'));
    }

    public function testSortByPrice(){
        $this->assertNotNull($this->rates->sortByPrice());
        $this->assertSame($this->rates->count(),$this->rates->sortByPrice()->count());
        $this->assertSame(7.63,$this->rates->sortByPrice()->last()->getTaxesTotal());
        $this->assertSame(17.68,$this->rates->sortByPrice()->first()->getTotal());
    }

    public function testSortByTime(){
        $this->assertNotNull($this->rates->sortByTime());
        $this->assertSame($this->rates->count(),$this->rates->sortByTime()->count());
        $this->assertNull($this->rates->sortByTime()->first()->getBrokerage());
        $this->assertSame(2.03,$this->rates->sortByTime()->last()->getTaxesTotal());
    }

    protected function setUp(){
        $response = '[
            {
                "price": {
                    "charges": {
                        "freight": 37.51
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 37.51,
                    "total": 42.39,
                    "taxes": {
                        "hst": 4.88
                    }
                },
                "service": {
                    "flagship_code": "standard",
                    "courier_code": "11",
                    "courier_desc": "UPS Standard",
                    "courier_name": "UPS",
                    "transit_time": 1,
                    "estimated_delivery_date": "2019-12-13 23:30"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 37.97
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 37.97,
                    "total": 42.91,
                    "taxes": {
                        "hst": 4.94
                    }
                },
                "service": {
                    "flagship_code": "expressAm",
                    "courier_code": "65",
                    "courier_desc": "UPS Express Saver",
                    "courier_name": "UPS",
                    "transit_time": 1,
                    "estimated_delivery_date": "2019-12-13 23:30"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 44.95
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 44.95,
                    "total": 50.79,
                    "taxes": {
                        "hst": 5.84
                    }
                },
                "service": {
                    "flagship_code": "express",
                    "courier_code": "01",
                    "courier_desc": "UPS Express",
                    "courier_name": "UPS",
                    "transit_time": 1,
                    "estimated_delivery_date": "2019-12-13 13:30"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 28.33,
                        "residential_surcharge": 3.44,
                        "fuel_surcharge": 4.53
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 36.3,
                    "total": 41.02,
                    "taxes": {
                        "hst": 4.72
                    }
                },
                "service": {
                    "flagship_code": "expressAm",
                    "courier_code": "PRIORITY_OVERNIGHT",
                    "courier_desc": "Priority Overnight",
                    "courier_name": "FedEx",
                    "transit_time": null,
                    "estimated_delivery_date": "2019-12-13 12:00"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 25.84,
                        "residential_surcharge": 3.44,
                        "fuel_surcharge": 4.16
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 33.44,
                    "total": 37.79,
                    "taxes": {
                        "hst": 4.35
                    }
                },
                "service": {
                    "flagship_code": "expressAm",
                    "courier_code": "STANDARD_OVERNIGHT",
                    "courier_desc": "Standard Overnight",
                    "courier_name": "FedEx",
                    "transit_time": null,
                    "estimated_delivery_date": "2019-12-13 20:00"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 25.84,
                        "residential_surcharge": 3.44,
                        "fuel_surcharge": 4.16
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 33.44,
                    "total": 37.79,
                    "taxes": {
                        "hst": 4.35
                    }
                },
                "service": {
                    "flagship_code": "secondDay",
                    "courier_code": "FEDEX_2_DAY",
                    "courier_desc": "2 Days",
                    "courier_name": "FedEx",
                    "transit_time": null,
                    "estimated_delivery_date": "2019-12-13 20:00"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 25.84,
                        "residential_surcharge": 3.44,
                        "fuel_surcharge": 4.16
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 33.44,
                    "total": 37.79,
                    "taxes": {
                        "hst": 4.35
                    }
                },
                "service": {
                    "flagship_code": "express",
                    "courier_code": "FEDEX_EXPRESS_SAVER",
                    "courier_desc": "Economy",
                    "courier_name": "FedEx",
                    "transit_time": null,
                    "estimated_delivery_date": "2019-12-13 20:00"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 12.43,
                        "residential_surcharge": 4.55,
                        "fuel_surcharge": 1.85
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 18.83,
                    "total": 21.28,
                    "taxes": {
                        "hst": 2.45
                    }
                },
                "service": {
                    "flagship_code": "standard",
                    "courier_code": "FEDEX_GROUND",
                    "courier_desc": "Ground",
                    "courier_name": "FedEx",
                    "transit_time": 2,
                    "estimated_delivery_date": "2019-12-16"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 48.88,
                        "residential_surcharge": 3.7,
                        "fuel_surcharge": 6.16
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 58.74,
                    "total": 66.37,
                    "taxes": {
                        "hst": 7.63
                    }
                },
                "service": {
                    "flagship_code": "expressEarlyAm",
                    "courier_code": "PurolatorExpress9AM",
                    "courier_desc": "Purolator Express 9AM",
                    "courier_name": "Purolator",
                    "transit_time": "1",
                    "estimated_delivery_date": "2019-12-13"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 32.53,
                        "residential_surcharge": 3.7,
                        "fuel_surcharge": 4.19
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 40.42,
                    "total": 45.67,
                    "taxes": {
                        "hst": 5.25
                    }
                },
                "service": {
                    "flagship_code": "expressAm",
                    "courier_code": "PurolatorExpress10:30AM",
                    "courier_desc": "Purolator Express 10:30 AM",
                    "courier_name": "Purolator",
                    "transit_time": "1",
                    "estimated_delivery_date": "2019-12-13"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 21.63,
                        "residential_surcharge": 3.7,
                        "fuel_surcharge": 2.89
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 28.22,
                    "total": 31.89,
                    "taxes": {
                        "hst": 3.67
                    }
                },
                "service": {
                    "flagship_code": "express",
                    "courier_code": "PurolatorExpress",
                    "courier_desc": "Purolator Express",
                    "courier_name": "Purolator",
                    "transit_time": "1",
                    "estimated_delivery_date": "2019-12-13"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 21.18,
                        "residential_surcharge": 3.7,
                        "fuel_surcharge": 2.83
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 27.71,
                    "total": 31.31,
                    "taxes": {
                        "hst": 3.6
                    }
                },
                "service": {
                    "flagship_code": "standard",
                    "courier_code": "PurolatorGround",
                    "courier_desc": "Purolator Ground",
                    "courier_name": "Purolator",
                    "transit_time": "1",
                    "estimated_delivery_date": "2019-12-13"
                }
            },
            {
                "price": {
                    "charges": {
                        "freight": 11.47,
                        "residential_surcharge": 4.18
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 15.65,
                    "total": 17.68,
                    "taxes": {
                        "hst": 2.03
                    }
                },
                "service": {
                    "flagship_code": "standard",
                    "courier_code": "1",
                    "courier_desc": "Canpar Ground",
                    "courier_name": "Canpar",
                    "transit_time": "1",
                    "estimated_delivery_date": "2019-12-16 05:00"
                }
            }
        ]';

        $this->getQuoteRequest = $this->getMockBuilder(QuoteRequest::class)
            ->setConstructorArgs(['testToken','localhost',[],'test','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
        $this->quoteRequest = $this->getQuoteRequest->execute();
        $this->rates = new RatesCollection();
        $this->rates->importRates(json_decode($response));
    }
}
