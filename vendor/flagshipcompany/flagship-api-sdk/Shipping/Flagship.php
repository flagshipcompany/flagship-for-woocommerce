<?php

namespace Flagship\Shipping;

use Flagship\Shipping\Requests\AvailableServicesRequest;
use Flagship\Shipping\Requests\QuoteRequest;
use Flagship\Shipping\Requests\CreatePickupRequest;
use Flagship\Shipping\Requests\GetShipmentListRequest;
use Flagship\Shipping\Requests\PrepareShipmentRequest;
use Flagship\Shipping\Requests\EditShipmentRequest;
use Flagship\Shipping\Requests\ConfirmShipmentRequest;
use Flagship\Shipping\Requests\ConfirmShipmentByIdRequest;
use Flagship\Shipping\Requests\CancelShipmentRequest;
use Flagship\Shipping\Requests\CancelPickupRequest;
use Flagship\Shipping\Requests\EditPickupRequest;
use Flagship\Shipping\Requests\TrackShipmentRequest;
use Flagship\Shipping\Requests\GetPickupListRequest;
use Flagship\Shipping\Requests\PackingRequest;
use Flagship\Shipping\Requests\GetShipmentByIdRequest;
use Flagship\Shipping\Requests\GetDhlEcommRatesRequest;
use Flagship\Shipping\Requests\CreateManifestRequest;
use Flagship\Shipping\Requests\AssociateShipmentRequest;
use Flagship\Shipping\Requests\GetManifestByIdRequest;
use Flagship\Shipping\Requests\AssociateToDepotRequest;
use Flagship\Shipping\Requests\GetManifestsListRequest;
use Flagship\Shipping\Requests\CancelManifestByIdRequest;
use Flagship\Shipping\Requests\ConfirmManifestByIdRequest;
use Flagship\Shipping\Requests\GetDhlEcommOpenShipmentsRequest;
use Flagship\Shipping\Requests\ValidateTokenRequest;
use Flagship\Shipping\Requests\GetAddressByTokenRequest;
use Flagship\Shipping\Objects\Rate;
use Flagship\Shipping\Objects\Manifest;
use Flagship\Shipping\Exceptions\CreateManifestException;
use Flagship\Shipping\Exceptions\AssociateShipmentException;
use Flagship\Shipping\Exceptions\CreateDepotShipmentException;
use Flagship\Shipping\Exceptions\AssociateToDepotException;
use Flagship\Shipping\Exceptions\ConfirmManifestByIdException;
use Flagship\Shipping\Exceptions\GetManifestByIdException;

class Flagship{

    public function __construct(string $apiToken, string $apiUrl, string $flagshipFor='', string $version=''){
        $this->apiUrl = $apiUrl;
        $this->apiToken = $apiToken;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function availableServicesRequest() : AvailableServicesRequest {
        $availableServicesRequest = new AvailableServicesRequest($this->apiToken,$this->apiUrl,$this->flagshipFor,$this->version);
        return $availableServicesRequest;
    }

    public function getAddressByTokenRequest(string $token) : GetAddressByTokenRequest {
        $getAddressByTokenRequest = new GetAddressByTokenRequest($token,$this->apiUrl,$this->flagshipFor,$this->version);
        return $getAddressByTokenRequest;
    }

    /* DHL Ecomm Requests start */
    public function getDhlEcommRatesRequest(array $payload) : GetDhlEcommRatesRequest {
        $dhlEcommRatesRequest = new GetDhlEcommRatesRequest($this->apiToken,$this->apiUrl,$payload,$this->flagshipFor,$this->version);
        return $dhlEcommRatesRequest;
    }

    public function createManifestRequest(array $payload) : CreateManifestRequest {
        $createManifestRequest = new CreateManifestRequest($this->apiToken,$this->apiUrl,$payload,$this->flagshipFor,$this->version);
        return $createManifestRequest;
    }

    public function associateShipmentRequest(int $manifestId, array $payload) : AssociateShipmentRequest {
        $associateShipmentRequest = new AssociateShipmentRequest($this->apiToken,$this->apiUrl,$manifestId,$payload,$this->flagshipFor,$this->version);
        return $associateShipmentRequest;

    }

    public function createDepotShipmentRequest(array $payload) : ConfirmShipmentRequest {
        $depotShipmentRequest =  $this->confirmShipmentRequest($payload);
        return $depotShipmentRequest;
    }

    public function getManifestByIdRequest(int $manifestId) : GetManifestByIdRequest {
        $getManifestByIdRequest = new GetManifestByIdRequest($this->apiToken, $this->apiUrl,$manifestId,$this->flagshipFor,$this->version);
        return $getManifestByIdRequest;
    }

    public function associateToDepotRequest(int $manifestId, array $payload) : AssociateToDepotRequest {
        $associateToDepotRequest = new AssociateToDepotRequest($this->apiToken,$this->apiUrl,$manifestId,$payload,$this->flagshipFor,$this->version);
        return $associateToDepotRequest;
    }

    public function getManifestsListRequest() : GetManifestsListRequest {
        $manifestsListRequest = new GetManifestsListRequest($this->apiToken,$this->apiUrl,$this->flagshipFor,$this->version);
        return $manifestsListRequest;
    }

    public function cancelManifestByIdRequest(int $manifestId) : CancelManifestByIdRequest {
        $cancelManifestByIdRequest = new CancelManifestByIdRequest($this->apiToken,$this->apiUrl,$manifestId,$this->flagshipFor,$this->version);
        return $cancelManifestByIdRequest;
    }

    public function confirmManifestbyIdRequest(int $manifestId) : ConfirmManifestByIdRequest {
        $confirmManifestbyIdRequest = new ConfirmManifestByIdRequest($this->apiToken,$this->apiUrl,$manifestId,$this->flagshipFor,$this->version);
        return $confirmManifestbyIdRequest;
    }

    public function getDhlEcommOpenShipmentsRequest() : GetDhlEcommOpenShipmentsRequest {
        $dhlEcommOpenShipmentsRequest = new GetDhlEcommOpenShipmentsRequest($this->apiToken,$this->apiUrl,$this->flagshipFor,$this->version);
        return $dhlEcommOpenShipmentsRequest;
    }

    public function createCompleteDhlEcommShipment(array $confirmedShipmentIds,string $manifestName,array $depotPayload) : Manifest {
        try{
            $manifestPayload = [ "name" => $manifestName ];
            $associateShipmentPayload = [ "shipment_ids" => $confirmedShipmentIds ];

            $manifestId = $this->createManifestRequest($manifestPayload)->execute()->getId();
            $associateShipment = $this->associateShipmentRequest($manifestId,$associateShipmentPayload)->execute();
            if($associateShipment === TRUE){
               $depotShipment = $this->createDepotShipmentRequest($depotPayload)->execute();
               $associateToDepotPayload = ["shipment_id" => $depotShipment->getId() ];
               $this->associateToDepotRequest($manifestId,$associateToDepotPayload)->execute();
               $this->confirmManifestbyIdRequest($manifestId)->execute();
            }
            $manifest = $this->getManifestByIdRequest($manifestId)->execute();
            return $manifest;
        } catch(CreateManifestException $e){
            echo $e->getMessage();
        } catch(AssociateShipmentException $e){
            echo $e->getMessage();
        } catch(CreateDepotShipmentException $e){
            echo $e->getMessage();
        } catch(AssociateToDepotException $e){
            echo $e->getMessage();
        } catch(ConfirmManifestByIdException $e){
            echo $e->getMessage();
        } catch(GetManifestByIdException $e){
            echo $e->getMessage();
        }
    }

    /* DHL Ecomm Requests end */

    public function validateTokenRequest(string $token) : ValidateTokenRequest {
        $validateTokenRequest = new ValidateTokenRequest($this->apiUrl,$token,$this->flagshipFor,$this->version);
        return $validateTokenRequest;
    }

    public  function createQuoteRequest(array $payload) : QuoteRequest {
        $request = new QuoteRequest($this->apiToken,$this->apiUrl,$payload,$this->flagshipFor,$this->version);
        return $request;
    }

    public function getShipmentListRequest() : GetShipmentListRequest {
        $shipmentListRequest = new GetShipmentListRequest($this->apiUrl,$this->apiToken,$this->flagshipFor,$this->version);
        return $shipmentListRequest;
    }

    public function getShipmentByIdRequest(int $id) : GetShipmentByIdRequest {
        $shipmentRequest = new GetShipmentByIdRequest($this->apiUrl,$this->apiToken,$this->flagshipFor,$this->version,$id);
        return $shipmentRequest;
    }

    public function prepareShipmentRequest(array $payload) : PrepareShipmentRequest {
        $prepareShipmentRequest = new PrepareShipmentRequest($this->apiUrl,$this->apiToken,$payload,$this->flagshipFor,$this->version);
        return $prepareShipmentRequest;
    }

    public function packingRequest(array $payload) : PackingRequest {
        $packingRequest = new PackingRequest($this->apiUrl,$this->apiToken,$payload,$this->flagshipFor,$this->version);
        return $packingRequest;
    }

    public function editShipmentRequest(array $payload,int $shipmentId) : EditShipmentRequest {
        $editShipmentRequest = new EditShipmentRequest($this->apiUrl,$this->apiToken,$payload,$shipmentId,$this->flagshipFor,$this->version);
        return $editShipmentRequest;
    }

    public function trackShipmentRequest(int $id) : TrackShipmentRequest {
        $trackShipment = new TrackShipmentRequest($this->apiUrl,$this->apiToken,$id,$this->flagshipFor,$this->version);
        return $trackShipment;
    }

    public function confirmShipmentRequest(array $payload) : ConfirmShipmentRequest {
        $confirmShipmentRequest = new ConfirmShipmentRequest($this->apiUrl,$this->apiToken,$payload,$this->flagshipFor,$this->version);
        return $confirmShipmentRequest;
    }

    public function confirmShipmentByIdRequest(int $id) : ConfirmShipmentByIdRequest {
        $confirmShipmentByIdRequest = new ConfirmShipmentByIdRequest($this->apiUrl,$this->apiToken,$id,$this->flagshipFor,$this->version);
        return $confirmShipmentByIdRequest;
    }

    public function cancelShipmentRequest(int $id) : CancelShipmentRequest {
        $cancelShipmentRequest = new CancelShipmentRequest($this->apiUrl,$this->apiToken,$id,$this->flagshipFor,$this->version);
        return $cancelShipmentRequest;
    }

    public function createPickupRequest(array $pickupPayload) : CreatePickupRequest {
        $createPickupRequest = new CreatePickupRequest($this->apiUrl,$this->apiToken,$pickupPayload,$this->flagshipFor,$this->version);
        return $createPickupRequest;
    }

    public function editPickupRequest(array $editPickupPayload,int $id) : EditPickupRequest {
        $editPickupRequest = new EditPickupRequest($this->apiUrl,$this->apiToken,$editPickupPayload,$id,$this->flagshipFor,$this->version);
        return $editPickupRequest;
    }

    public function cancelPickupRequest(int $id) : CancelPickupRequest {
        $cancelPickupRequest = new CancelPickupRequest($this->apiUrl,$this->apiToken,$id,$this->flagshipFor,$this->version);
        return $cancelPickupRequest;
    }

    public function getPickupListRequest() : GetPickupListRequest {
        $getPickupListRequest = new GetPickupListRequest($this->apiUrl,$this->apiToken,$this->flagshipFor,$this->version);
        return $getPickupListRequest;
    }

}
