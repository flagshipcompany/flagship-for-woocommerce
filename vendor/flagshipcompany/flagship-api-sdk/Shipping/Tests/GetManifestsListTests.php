<?php

namespace Flagship\Shipping\Tests;

use Flagship\Shipping\Requests\GetManifestsListRequest;
use Flagship\Shipping\Objects\Manifest;
use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Collections\ManifestListCollection;
use Flagship\Shipping\Exceptions\ManifestListException;

class GetManifestsListTest extends TestCase{

    public function testGetByStatus(){
        $this->assertNotNull($this->manifestList->getByStatus('confirmed'));
        $this->assertSame(23,$this->manifestList->getByStatus('confirmed')->first()->getId());
        $this->assertInstanceOf(ManifestListCollection::class,$this->manifestList->getByStatus('prequoted'));
        $this->expectException(ManifestListException::class);
        $this->assertTrue($this->manifestList->getByStatus('cancelled'));
    }

    protected function setUp(){

        $response = '[
            {
                "id": "25",
                "name": "myManifest",
                "status": "prequoted"
            },
            {
                "id": "24",
                "name": "myManifest",
                "status": "prequoted"
            },
            {
                "id": "23",
                "name": "MyNewManifest",
                "status": "confirmed",
                "to_depot_id": "3372200"
            },
            {
                "id": "22",
                "name": "myManifest",
                "status": "prequoted"
            },
            {
                "id": "21",
                "name": "MyManifest",
                "status": "prequoted"
            },
            {
                "id": "20",
                "name": "completeManifest",
                "status": "confirmed",
                "to_depot_id": "3372195"
            }]';

        $this->getManifestsListRequest = $this->getMockBuilder(GetManifestsListRequest::class)
            ->setConstructorArgs(['testToken','localhost','testing','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
        $this->manifestListsRequest = $this->getManifestsListRequest->execute();
        $this->manifestList = new ManifestListCollection();
        $this->manifestList->importManifests(json_decode($response));
    }
}
