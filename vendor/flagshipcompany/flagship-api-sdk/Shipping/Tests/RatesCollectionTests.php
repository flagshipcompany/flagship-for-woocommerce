<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Collections\RatesCollection;
use Flagship\Shipping\Objects\Rate;
use Flagship\Shipping\Exceptions\QuoteException;

class RatesCollectionTest extends TestCase{

    public function testGetCheapest(){ 
        $this->assertNotEmpty($this->ratesCollection->getCheapest());
        $this->assertNotNull($this->ratesCollection->getCheapest());
        $this->assertInstanceOf(Rate::class, $this->ratesCollection->getCheapest());
        $this->assertSame(59.93, $this->ratesCollection->getCheapest()->rate->price->total);
    }

    public function testGetFastest(){
        $this->assertNotEmpty($this->ratesCollection->getFastest());
        $this->assertNotNull($this->ratesCollection->getFastest());
        $this->assertInstanceOf(Rate::class, $this->ratesCollection->getFastest());
        $this->assertSame('2018-12-12 09:00', $this->ratesCollection->getFastest()->rate->service->estimated_delivery_date);
    }

    public function testGetByCourier(){
        $this->assertNotNull($this->ratesCollection->getByCourier('ups'));
        $this->assertInstanceOf(RatesCollection::class, $this->ratesCollection->getByCourier('ups'));
        $this->assertSame(59.93, $this->ratesCollection->getByCourier('ups')->getCheapest()->rate->price->total);
        $this->assertSame('2018-12-12 09:00', $this->ratesCollection->getByCourier('ups')->getFastest()->rate->service->estimated_delivery_date);
    }

    public function testSortByPrice(){
        $this->assertNotNull($this->ratesCollection->sortByPrice());
        $this->assertInstanceOf(RatesCollection::class, $this->ratesCollection->sortByPrice());
        $this->assertSame(59.93, $this->ratesCollection->sortByPrice()->first()->rate->price->total);
        $this->assertSame(131.51, $this->ratesCollection->sortByPrice()->last()->rate->price->total);
    }

    public function testSortByTime(){
        $this->assertNotNull($this->ratesCollection->sortByTime());
        $this->assertInstanceOf(RatesCollection::class, $this->ratesCollection->sortByTime());
        $this->assertSame(131.51, $this->ratesCollection->sortByTime()->first()->rate->price->total);
        $this->assertSame(59.93, $this->ratesCollection->sortByTime()->last()->rate->price->total);
    }

    public function testGetByCourierForException(){

        $this->expectException(QuoteException::class);
        $this->ratesCollection->getByCourier('fedex');
    }

    protected function setUp(){
        $response = '[
               {
                  "price":{
                     "charges":{
                        "freight":47.17,
                        "insurance":4.95
                     },
                     "adjustments":null,
                     "debits":null,
                     "brokerage":null,
                     "subtotal":52.12,
                     "total":59.93,
                     "taxes":{
                        "gst":2.61,
                        "qst":5.2
                     }
                  },
                  "service":{
                     "flagship_code":"expressAm",
                     "courier_code":"65",
                     "courier_desc":"UPS Express Saver",
                     "courier_name":"UPS",
                     "transit_time":1,
                     "estimated_delivery_date":"2018-12-12 15:00"
                  }
               },
               {
                  "price":{
                     "charges":{
                        "freight":109.43,
                        "insurance":4.95
                     },
                     "adjustments":null,
                     "debits":null,
                     "brokerage":null,
                     "subtotal":114.38,
                     "total":131.51,
                     "taxes":{
                        "gst":5.72,
                        "qst":11.41
                     }
                  },
                  "service":{
                     "flagship_code":"expressEarlyAm",
                     "courier_code":"14",
                     "courier_desc":"UPS Express Early A.M. SM",
                     "courier_name":"UPS",
                     "transit_time":1,
                     "estimated_delivery_date":"2018-12-12 09:00"
                  }
               },
               {
                  "price":{
                     "charges":{
                        "freight":58.2,
                        "insurance":4.95
                     },
                     "adjustments":null,
                     "debits":null,
                     "brokerage":null,
                     "subtotal":63.15,
                     "total":72.61,
                     "taxes":{
                        "gst":3.16,
                        "qst":6.3
                     }
                  },
                  "service":{
                     "flagship_code":"express",
                     "courier_code":"01",
                     "courier_desc":"UPS Express",
                     "courier_name":"UPS",
                     "transit_time":1,
                     "estimated_delivery_date":"2018-12-12 10:30"
                  }
               }
            ]';

        $this->ratesCollection = new RatesCollection();
        $this->ratesCollection->importRates(json_decode($response));
    }
}
