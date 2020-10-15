<?php
namespace FlagshipWoocommerce\Requests;

use Flagship\Shipping\Flagship;
use FlagshipWoocommerce\FlagshipWoocommerceShipping;

class Packing_Request extends Abstract_Flagship_Api_Request {

    protected $debugMode = false;

    public function __construct($token, $debugMode = false,$apiurl) {
    	$this->token = $token;
    	$this->apiUrl = $apiUrl;
        $this->debugMode = $debugMode;
    }

    public function pack_boxes($items, $boxes) {
        $packageBoxes = [];
        $apiRequests = $this->make_api_request($items, $boxes);
        $apiClient = new Flagship($this->token, $this->apiUrl, 'woocommerce', FlagshipWoocommerceShipping::$version);

        try{
            foreach ($apiRequests as $apiRequest) {            
                $packing_results = $apiClient->packingRequest($apiRequest)->execute();
                $packageBoxes[] = $this->prepareBoxesFromPackages($packing_results);
            }
            return $packageBoxes;
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
    }

    protected function prepareBoxesFromPackages($packages)
    {
        foreach ($packages as $key => $package) {
            $temp = [
                "description" => $package->getBoxModel(),
                "length" => $package->getLength(),
                "width" => $package->getWidth(),
                "height" => $package->getHeight(),
                "weight" => $package->getWeight(),
            ];
        }
        return $temp;
    }

    protected function make_api_request($items, $boxes) {

        $shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );

        if(count($shipping_classes) == 0)
        {
            $boxes = $this->make_boxes_request($boxes);
            $items = array_map(function($item){
                unset($item["shipping_class"]);
                return $item;
            }, $items);

            return array([
                'items' => $items,
                'boxes' => $boxes,
                'units' => 'imperial',
            ]);
        }

        foreach ($items as $item) {
            if($item['shipping_class'] != null)
            {
                $packages[$item['shipping_class']]['items'][] = $this->getShippingClassItem($item);
                continue;
            }
            $packages['no_shipping_class']['items'][] = $this->getShippingClassItem($item);
        }
        foreach ($boxes as $box) {
            if(array_key_exists('shipping_class',$box) && $box['shipping_class'] != null)
            {
                $packages[$box['shipping_class']]['boxes'][] = $this->getShippingClassBox($box);

                continue;
            }
            $packages['no_shipping_class']['boxes'][] = $this->getShippingClassBox($box);
        }

        $packages = $this->addUnits($packages,$boxes);

        return $packages;
    }

    protected function addUnits($packages,$boxes)
    {
        $packages = array_map(function($arr) use ($boxes){
            $arr['units'] = 'imperial';
            if(!array_key_exists('boxes',$arr))
            {
                $arr['boxes'] = $this->make_boxes_request($boxes);
            }
            if(!array_key_exists('items', $arr)){
                $arr = [];
            }

            return $arr;
        }, $packages);

        $packages = array_filter($packages,function($value){ return count($value)>0; });
        return $packages;
    }

    protected function getShippingClassBox($box){
            $box['box_model'] = $box['model'];
            $box['weight'] = 0;
            unset($box['id']);
            unset($box['model']);
            unset($box['extra_charge']);
            unset($box['shipping_class']);
            return $box;
    }

    protected function getShippingClassItem($item){
        unset($item['shipping_class']);
        return $item;
    }

    protected function make_boxes_request($boxes) {
        return array_map(function($box) {
            $box['box_model'] = $box['model'];
            $box['weight'] = 0;
            unset($box['id']);
            unset($box['model']);
            unset($box['extra_charge']);
            unset($box['shipping_class']);
            return $box;
        }, $boxes);
    }
}
