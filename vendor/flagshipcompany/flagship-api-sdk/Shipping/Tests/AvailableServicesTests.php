<?php

namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Requests\AvailableServicesRequest;
use Flagship\Shipping\Collections\AvailableServicesCollection;
use Flagship\Shipping\Exceptions\AvailableServicesException;

class AvailableServicesTests extends TestCase{

    public function testGetServicesByCourier(){
        $this->assertNotEmpty($this->availableServices->getServicesByCourier('canpar'));
        $this->assertNotNull($this->availableServices->getServicesByCourier('fedex'));
        $this->assertInstanceOf(AvailableServicesCollection::class, $this->availableServices->getServicesByCourier('purolator'));
    }
    
    public function testGetStandardServices(){
        $this->assertNotEmpty($this->availableServices->getStandardServices());
        $this->assertNotNull($this->availableServices->getStandardServices());
        $this->assertInstanceOf(AvailableServicesCollection::class,$this->availableServices->getStandardServices());
    }

    public function testGetOvernightServices(){
        $this->assertNotEmpty($this->availableServices->getOvernightServices());
        $this->assertNotNull($this->availableServices->getOvernightServices());
        $this->assertInstanceOf(AvailableServicesCollection::class,$this->availableServices->getOvernightServices());
    }

    public function testGetExpressServices(){
        $this->assertNotEmpty($this->availableServices->getExpressServices());
        $this->assertNotNull($this->availableServices->getExpressServices());
        $this->assertInstanceOf(AvailableServicesCollection::class,$this->availableServices->getExpressServices());
    }

    public function testGetStandardServicesByCourier(){
        $this->assertNotEmpty($this->availableServices->getStandardServicesByCourier('ups'));
        $this->assertNotNull($this->availableServices->getStandardServicesByCourier('purolator'));
        $this->assertInstanceOf(AvailableServicesCollection::class,$this->availableServices->getStandardServicesByCourier('fedex'));
    }

    public function testGetOvernightServicesByCourier(){
        $this->expectException(AvailableServicesException::class);
        $this->assertNotEmpty($this->availableServices->getOvernightServicesByCourier('ups'));
    }

    public function testGetExpressServicesByCourier(){
        $this->assertNotEmpty($this->availableServices->getExpressServicesByCourier('purolator'));
        $this->assertNotNull($this->availableServices->getExpressServicesByCourier('fedex'));
        $this->assertInstanceOf(AvailableServicesCollection::class,$this->availableServices->getExpressServicesByCourier('ups'));
    }

    protected function setUp(){

        $response = '[
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "EXPRESS 9:00",
                "courier_description": "DHL Express 9:00 AM"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "EXPRESS 10:30",
                "courier_description": "DHL Express 10:30 AM"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "EXPRESS 12:00",
                "courier_description": "DHL Express 12:00"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "EXPRESS WORLDWIDE",
                "courier_description": "DHL Express Worldwide"
            },
            {
                "flagship_code": "intlStandard",
                "courier_code": "ECONOMY SELECT",
                "courier_description": "DHL Economy Select"
            },
            {
                "flagship_code": "intlJumboBox",
                "courier_code": "JUMBO BOX",
                "courier_description": "DHL Jumbo Box"
            },
            {
                "flagship_code": "intlFreight",
                "courier_code": "FREIGHT WORLDWIDE",
                "courier_description": "DHL Freight Worldwide"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "EXPRESS ENVELOPE",
                "courier_description": "DHL Express Envelope"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "EURO PACK",
                "courier_description": "DHL Euro Pack"
            },
            {
                "flagship_code": "standard",
                "courier_code": 1,
                "courier_description": "Canpar Ground"
            },
            {
                "flagship_code": "standard",
                "courier_code": "PurolatorGround",
                "courier_description": "Purolator Ground"
            },
            {
                "flagship_code": "express",
                "courier_code": "PurolatorExpress",
                "courier_description": "Purolator Express"
            },
            {
                "flagship_code": "expressAm",
                "courier_code": "PurolatorExpress10:30AM",
                "courier_description": "Purolator Express 10:30 AM"
            },
            {
                "flagship_code": "expressEarlyAm",
                "courier_code": "PurolatorExpress9AM",
                "courier_description": "Purolator Express 9AM"
            },
            {
                "flagship_code": "express",
                "courier_code": "PurolatorExpressBox",
                "courier_description": "Purolator Express Box"
            },
            {
                "flagship_code": "expressAm",
                "courier_code": "PurolatorExpressBox10:30AM",
                "courier_description": "Purolator Express Box 10:30 AM"
            },
            {
                "flagship_code": "expressEarlyAm",
                "courier_code": "PurolatorExpressBox9AM",
                "courier_description": "Purolator Express Box 9AM"
            },
            {
                "flagship_code": "express",
                "courier_code": "PurolatorExpressEnvelope",
                "courier_description": "Purolator Express Envelope"
            },
            {
                "flagship_code": "expressAm",
                "courier_code": "PurolatorExpressEnvelope10:30AM",
                "courier_description": "Purolator Express Envelope 10:30 AM"
            },
            {
                "flagship_code": "expressEarlyAm",
                "courier_code": "PurolatorExpressEnvelope9AM",
                "courier_description": "Purolator Express Envelope 9AM"
            },
            {
                "flagship_code": "express",
                "courier_code": "PurolatorExpressPack",
                "courier_description": "Purolator Express Pack"
            },
            {
                "flagship_code": "expressAm",
                "courier_code": "PurolatorExpressPack10:30AM",
                "courier_description": "Purolator Express Pack 10:30 AM"
            },
            {
                "flagship_code": "expressEarlyAm",
                "courier_code": "PurolatorExpressPack9AM",
                "courier_description": "Purolator Express Pack 9AM"
            },
            {
                "flagship_code": "intlStandard",
                "courier_code": "PurolatorGroundU.S.",
                "courier_description": "Purolator Ground U.S."
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "PurolatorExpressU.S.",
                "courier_description": "Purolator Express U.S."
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "PurolatorExpressU.S.10:30AM",
                "courier_description": "Purolator Express U.S. 10:30 AM"
            },
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "PurolatorExpressU.S.9AM",
                "courier_description": "Purolator Express U.S. 9AM"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "PurolatorExpressBoxU.S.",
                "courier_description": "Purolator Express Box U.S."
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "PurolatorExpressU.S.Box10:30AM",
                "courier_description": "Purolator Express U.S. Box 10:30 AM"
            },
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "PurolatorExpressU.S.Box9AM",
                "courier_description": "Purolator Express U.S. Box 9AM"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "PurolatorExpressEnvelopeU.S.",
                "courier_description": "Purolator Express Envelope U.S."
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "PurolatorExpressU.S.Envelope10:30AM",
                "courier_description": "Purolator Express U.S. Envelope 10:30 AM"
            },
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "PurolatorExpressU.S.Envelope9AM",
                "courier_description": "Purolator Express U.S. Envelope 9AM"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "PurolatorExpressPackU.S.",
                "courier_description": "Purolator Express Pack U.S."
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "PurolatorExpressU.S.Pack10:30AM",
                "courier_description": "Purolator Express U.S. Pack 10:30 AM"
            },
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "PurolatorExpressU.S.Pack9AM",
                "courier_description": "Purolator Express U.S. Pack 9AM"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "PurolatorExpressInternational",
                "courier_description": "Purolator Express International"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "PurolatorExpressInternational10:30AM",
                "courier_description": "Purolator Express International 10:30 AM"
            },
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "PurolatorExpressInternational9AM",
                "courier_description": "Purolator Express International 9AM"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "PurolatorExpressBoxInternational",
                "courier_description": "Purolator Express Box International"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "PurolatorExpressInternationalBox10:30AM",
                "courier_description": "Purolator Express International Box 10:30 AM"
            },
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "PurolatorExpressInternationalBox9AM",
                "courier_description": "Purolator Express International Box 9AM"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "PurolatorExpressEnvelopeInternational",
                "courier_description": "Purolator Express Envelope International"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "PurolatorExpressInternationalEnvelope10:30AM",
                "courier_description": "Purolator Express International Envelope 10:30 AM"
            },
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "PurolatorExpressInternationalEnvelope9AM",
                "courier_description": "Purolator Express International Envelope 9AM"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "PurolatorExpressPackInternational",
                "courier_description": "Purolator Express Pack International"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "PurolatorExpressInternationalPack10:30AM",
                "courier_description": "Purolator Express International Pack 10:30 AM"
            },
            {
                "flagship_code": "intlExpressEarlyAm",
                "courier_code": "PurolatorExpressInternationalPack9AM",
                "courier_description": "Purolator Express International Pack 9AM"
            },
            {
                "flagship_code": "expressAm",
                "courier_code": "PRIORITY_OVERNIGHT",
                "courier_description": "FedEx Priority Overnight"
            },
            {
                "flagship_code": "secondDay",
                "courier_code": "FEDEX_2_DAY",
                "courier_description": "FedEx 2 Days"
            },
            {
                "flagship_code": "expressAm",
                "courier_code": "STANDARD_OVERNIGHT",
                "courier_description": "FedEx Standard Overnight"
            },
            {
                "flagship_code": "expressEarlyAm",
                "courier_code": "FIRST_OVERNIGHT",
                "courier_description": "FedEx First Overnight"
            },
            {
                "flagship_code": "express",
                "courier_code": "FEDEX_EXPRESS_SAVER",
                "courier_description": "FedEx Economy"
            },
            {
                "flagship_code": "freightExpress",
                "courier_code": "FEDEX_FIRST_FREIGHT",
                "courier_description": "FedEx First Freight"
            },
            {
                "flagship_code": "freightExpress",
                "courier_code": "FEDEX_1_DAY_FREIGHT",
                "courier_description": "FedEx 1 Day Freight"
            },
            {
                "flagship_code": "standard",
                "courier_code": "FEDEX_GROUND",
                "courier_description": "FedEx Ground"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "INTERNATIONAL_ECONOMY",
                "courier_description": "FedEx International Economy"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "INTERNATIONAL_ECONOMY_DISTRIBUTION",
                "courier_description": "FedEx International Economy Distribution"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "INTERNATIONAL_ECONOMY_FREIGHT",
                "courier_description": "FedEx International Economy Freight"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "INTERNATIONAL_PRIORITY",
                "courier_description": "FedEx International Priority"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "INTERNATIONAL_PRIORITY_DISTRIBUTION",
                "courier_description": "FedEx International Priority Distribution"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "INTERNATIONAL_PRIORITY_FREIGHT",
                "courier_description": "FedEx International Priority Freight"
            },
            {
                "flagship_code": "intlStandard",
                "courier_code": "FEDEX_GROUND",
                "courier_description": "FedEx Ground"
            },
            {
                "flagship_code": "expressEarlyAm",
                "courier_code": 14,
                "courier_description": "UPS Express Early A.M. SM"
            },
            {
                "flagship_code": "express",
                "courier_code": "01",
                "courier_description": "UPS Express"
            },
            {
                "flagship_code": "thirdDay",
                "courier_code": 12,
                "courier_description": "UPS Three-Day Select"
            },
            {
                "flagship_code": "expressAm",
                "courier_code": 13,
                "courier_description": "UPS Express Saver"
            },
            {
                "flagship_code": "standard",
                "courier_code": 11,
                "courier_description": "UPS Standard"
            },
            {
                "flagship_code": "intlExpressSaver",
                "courier_code": 65,
                "courier_description": "UPS Express Saver"
            },
            {
                "flagship_code": "intlExpress",
                "courier_code": "08",
                "courier_description": "UPS Worldwide Expedited SM"
            },
            {
                "flagship_code": "intlExpressAm",
                "courier_code": "07",
                "courier_description": "UPS Worldwide Express SM"
            },
            {
                "flagship_code": "intlStandard",
                "courier_code": 11,
                "courier_description": "UPS Standard"
            }
        ]';


        $this->getAvailableServicesRequest = $this->getMockBuilder(AvailableServicesRequest::class)
            ->setConstructorArgs(['testToken','localhost','test','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
        $this->getAvailableServices = $this->getAvailableServicesRequest->execute();

        $this->availableServices = new AvailableServicesCollection();
        $this->availableServices->importServices(json_decode($response));


    }
}