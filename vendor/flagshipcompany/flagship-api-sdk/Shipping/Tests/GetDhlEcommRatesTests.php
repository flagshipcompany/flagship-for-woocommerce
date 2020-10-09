<?php

namespace Flagship\Shipping\Tests;
use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Requests\GetDhlEcommRatesRequest;
use Flagship\Shipping\Exceptions\GetDhlEcommRatesException;
use Flagship\Shipping\Exceptions\QuoteException;
use Flagship\Shipping\Collections\RatesCollection;
use Flagship\Shipping\Objects\Rate;

class GetDhlEcommRatesTest extends TestCase{

    public function testGetCheapest(){
        $this->assertNotNull($this->rates->getCheapest());
        $this->assertSame(31.18,$this->rates->getCheapest()->getTotal());
    }

    public function testGetFastest(){
        $this->assertNotNull($this->rates->getFastest());
        $this->assertInstanceOf(Rate::class,$this->rates->getFastest());
        $this->assertSame(29.23,$this->rates->getFastest()->getSubtotal());
    }

    public function testGetByCourier(){
        $this->assertNotNull($this->rates->getByCourier('dhlec'));
        $this->assertSame(2,$this->rates->getByCourier('dhlec')->count());
        $this->expectException(QuoteException::class);
        $this->assertNull($this->rates->getByCourier('dhl'));
    }

    public function testSortByPrice(){
        $this->assertNotNull($this->rates->sortByPrice());
        $this->assertInstanceOf(RatesCollection::class, $this->rates->sortByPrice());
    }

    public function testSortByTime(){
        $this->assertNotNull($this->rates->sortByTime());
        $this->assertInstanceOf(RatesCollection::class, $this->rates->sortByTime());
    }

    protected function setUp(){
        $response = '[
        {
            "price": {
                "charges": {
                    "freight": 14.23,
                    "insurance": 15
                },
                "adjustments": null,
                "debits": null,
                "brokerage": null,
                "subtotal": 29.23,
                "total": 31.18,
                "taxes": {
                    "hst": 1.95
                }
            },
            "service": {
                "flagship_code": "intlExpress",
                "courier_code": "PKY",
                "courier_desc": "DHL GlobalMail Packet Priority",
                "courier_name": "dhlec",
                "transit_time": null,
                "estimated_delivery_date": null
            }
        },
        {
            "price": {
                "charges": {
                    "freight": 25.27,
                    "insurance": 15
                },
                "adjustments": null,
                "debits": null,
                "brokerage": null,
                "subtotal": 40.27,
                "total": 42.22,
                "taxes": {
                    "hst": 1.95
                }
            },
            "service": {
                "flagship_code": "intlExpressSaver",
                "courier_code": "PKT",
                "courier_desc": "DHL GlobalMail Packet Plus",
                "courier_name": "dhlec",
                "transit_time": null,
                "estimated_delivery_date": null
            }
        }
    ]';

        $this->getDhlEcommRatesRequest = $this->getMockBuilder(GetDhlEcommRatesRequest::class)
            ->setConstructorArgs(['testToken','localhost',['payload'],'testing','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
        $this->ratesRequest = $this->getDhlEcommRatesRequest->execute();
        $this->rates = new RatesCollection();
        $this->rates->importRates(json_decode($response));
    }
}
