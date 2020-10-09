<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Collections\GetPickupListCollection;
use Flagship\Shipping\Exceptions\GetPickupListException;

class GetPickupListsCollectionTests extends TestCase{


        public function testGetById(){

            $this->assertNotEmpty($this->pickupList->getById(1085727));
            $this->assertNotNull($this->pickupList->getById(1085727));
            $this->assertSame("148 Brunswick Boul",$this->pickupList->getById(1085729)->pickup->address->address);
        }

        public function testGetBySender(){
            $this->assertNotEmpty($this->pickupList->getBySender('Customer Service'));
            $this->assertNotNull($this->pickupList->getBySender('Customer Service'));
            $this->assertSame('H9R 5P9', $this->pickupList->getBySender('Customer Service')->getById(1085727)->pickup->address->postal_code);
        }

        public function testGetByPhone(){
            $this->assertNotEmpty($this->pickupList->getByPhone('18663208383'));
            $this->assertNotNull($this->pickupList->getByPhone('18663208383'));
            $this->assertSame('2018-11-06', $this->pickupList->getByPhone('18663208383')->first()->pickup->date);
        }

        public function testGetByCourier(){
            $this->assertNotEmpty($this->pickupList->getByCourier('ups'));
            $this->assertNotNull($this->pickupList->getByCourier('ups'));
            $this->assertSame('1', $this->pickupList->getByCourier('ups')->last()->pickup->address->is_commercial);
        }

        public function testGetCommercialPickups(){
            $this->assertNotEmpty($this->pickupList->getCommercialPickups());
            $this->assertNotNull($this->pickupList->getCommercialPickups());
            $this->assertSame('imperial', $this->pickupList->getCommercialPickups()->first()->pickup->units);
        }

        public function testGetByDate(){
            $this->assertNotEmpty($this->pickupList->getByDate('2018-11-06'));
            $this->assertNotNull($this->pickupList->getByDate('2018-11-06'));
            $this->assertSame('FlagShip Courier Solutions',$this->pickupList->getByDate('2018-11-06')->first()->pickup->address->name);
        }

        public function testGetCancelledPickups(){
            $this->expectException(GetPickupListException::class);
            $this->pickupList->getCancelledPickups();
        }

        protected function setUp() {
            $response = '[

            {
                "id":"1085729",
                "confirmation":"2929602E9CP",
                "address":{
                    "name":"FlagShip Courier Solutions",
                    "attn":"Customer Service",
                    "address":"148 Brunswick Boul",
                    "suite":null,
                    "city":"Pointe-Claire",
                    "country":"CA",
                    "state":"QC",
                    "postal_code":"H9R 5P9",
                    "phone":"18663208383",
                    "ext":null,
                    "is_commercial":"1"
                },
                "courier":"ups",
                "units":"imperial",
                "boxes":"1",
                "weight":"1",
                "location":"FrontDesk",
                "date":"2018-11-06",
                "from":"09:00:00",
                "until":"15:00:00",
                "to_country":"CA",
                "instruction":null,
                "cancelled":false
            },
            {
                "id":"1085728",
                "confirmation":"2929602E9CP",
                "address":{
                    "name":"FlagShip Courier Solutions",
                    "attn":"Customer Service",
                    "address":"148 Brunswick Boul",
                    "suite":null,
                    "city":"Pointe-Claire",
                    "country":"CA",
                    "state":"QC",
                    "postal_code":"H9R 5P9",
                    "phone":"18663208383",
                    "ext":null,
                    "is_commercial":"1"
                },
                "courier":"ups",
                "units":"imperial",
                "boxes":"1",
                "weight":"1",
                "location":"FrontDesk",
                "date":"2018-11-06",
                "from":"09:00:00",
                "until":"15:00:00",
                "to_country":"CA",
                "instruction":null,
                "cancelled":false
            },
            {
              "id":"1085727",
              "confirmation":"2929602E9CP",
              "address":{
                 "name":"FlagShip Courier Solutions",
                 "attn":"Customer Service",
                 "address":"148 Brunswick Boul",
                 "suite":null,
                 "city":"Pointe-Claire",
                 "country":"CA",
                 "state":"QC",
                 "postal_code":"H9R 5P9",
                 "phone":"18663208383",
                 "ext":null,
                 "is_commercial":"1"
              },
              "courier":"ups",
              "units":"imperial",
              "boxes":"1",
              "weight":"1",
              "location":"FrontDesk",
              "date":"2018-11-06",
              "from":"09:00:00",
              "until":"15:00:00",
              "to_country":"CA",
              "instruction":null,
              "cancelled":false
            }
        ]';

        $this->pickupList = new GetPickupListCollection();
        $this->pickupList->importPickups(json_decode($response));

    }
}
