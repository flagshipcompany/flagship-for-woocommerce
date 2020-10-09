<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Packing;

class PackingTest extends TestCase{

    public function testGetBoxModel(){
        $this->assertNotNull($this->packing->getBoxModel());
        $this->assertSame("Le grande box", $this->packing->getBoxModel());
    }

    public function testeGetLength(){
        $this->assertNotNull($this->packing->getLength());
        $this->assertSame("30", $this->packing->getLength());
    }

    public function testGetWidth(){
        $this->assertNotNull($this->packing->getWidth());
        $this->assertSame("30",$this->packing->getWidth());
    }

    public function testGetHeight(){
        $this->assertNotNull($this->packing->getHeight());
        $this->assertSame("20",$this->packing->getHeight());
    }

    public function testGetWeight(){
        $this->assertNotNull($this->packing->getWeight());
        $this->assertSame(10,$this->packing->getWeight());
    }

    public function testGetItems(){
        $this->assertNotNull($this->packing->getItems());
        $this->assertInternalType('array',$this->packing->getItems());
        $this->assertCount(3,$this->packing->getItems());
    }

    protected function setUp(){
        $response = '{
                "box_model": "Le grande box",
                "length": "30",
                "width": "30",
                "height": "20",
                "weight": 10,
                "items": [
                    "Item 4",
                    "Item 3",
                    "Item 2"
                ]
            }';

        $this->packing = $this->getMockBuilder(Packing::class)
                          ->setConstructorArgs([json_decode($response)])
                          ->setMethods(['__construct'])
                          ->getMock();

    }

}
