<?php
namespace Flagship\Shipping\Tests;

use Flagship\Shipping\Requests\CreateManifestRequest;
use Flagship\Shipping\Exceptions\CreateManifestException;
use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Manifest;

class CreateManifestTests extends TestCase{

    public function testGetName(){
        $this->assertNotNull($this->manifest->getName());
        $this->assertInternalType('string', $this->manifest->getName());
        $this->assertSame('myManifest',$this->manifest->getName());
    }

    public function testGetStatus(){
        $this->assertNotNull($this->manifest->getStatus());
        $this->assertInternalType('string',$this->manifest->getStatus());
    }

    public function testGetId(){
        $this->assertNotNull($this->manifest->getId());
        $this->assertInternalType('int',$this->manifest->getId());
    }

    public function testGetToDepotShipment(){
        $this->assertNull($this->manifest->getToDepotShipment());
    }

    public function testGetShipmentIds(){
        $this->assertNull($this->manifest->getShipmentIds());
    }

    public function testGetPriceByShipmentId(){
        $this->assertNull($this->manifest->getPriceByShipmentId(2362877));
    }

    public function testGetTaxesTotal(){
        $this->assertNull($this->manifest->getTaxesTotal());
    }

    public function testGetSubtotal(){
        $this->assertNull($this->manifest->getSubtotal());
    }

    public function testGetTotal(){
        $this->assertNull($this->manifest->getTotal());
    }

    public function testGetTaxesDetails(){
        $this->assertNull($this->manifest->getTaxesDetails());
    }

    public function testGetToDepotId(){
        $this->assertNull($this->manifest->getToDepotId());
    }

    public function testGetBolNumber(){
        $this->assertNull($this->manifest->getBolNumber());
    }

    public function testGetShipmentsLabels(){
        $this->assertNull($this->manifest->getShipmentsLabels());
    }

    public function testGetShipmentsThermalLabels(){
        $this->assertNull($this->manifest->getShipmentsThermalLabels());
    }

    public function testGetManifestSummary(){
        $this->assertNull($this->manifest->getManifestSummary());
    }

    public function testGetToDepotLabel(){
        $this->assertNull($this->manifest->getToDepotLabel());
    }

    public function testGetToDepotThermalLabel(){
        $this->assertNull($this->manifest->getToDepotThermalLabel());
    }

    public function testGetAllPrices(){
        $this->assertNull($this->manifest->getAllPrices());
    }

    protected function setUp(){
        $this->createManifestRequest = $this->getMockBuilder(CreateManifestRequest::class)
                          ->setConstructorArgs(['jhdgjhsgfjhsd','https://www.flagshipcompany.com',["name" => "testManifest"],'testing','1.0.11'])
                          ->setMethods(['execute'])
                          ->getMock();
        $this->manifestRequest = $this->createManifestRequest->execute();
        $response = '{
                        "name": "myManifest",
                        "status": "prequoted",
                        "id": 25
                    }';

        $this->manifest = new Manifest(json_decode($response));
    }
}
