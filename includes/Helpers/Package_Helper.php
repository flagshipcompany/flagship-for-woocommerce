<?php
namespace FlagshipWoocommerce\Helpers;

use FlagshipWoocommerce\Requests\Packing_Request;
use FlagshipWoocommerce\REST_Controllers\Package_Box_Controller;

class Package_Helper {

	protected $debug_mode;

	public function __construct($debug_mode = false,$apiUrl) {
        $this->debug_mode = $debug_mode;
        $this->apiUrl = $apiUrl;
    }

    public function make_packages($order_items, $options) {
    	$items = $this->extract_items($order_items, $options);
        $boxSplit = isset($options['box_split']) ? $options['box_split'] : null;
        $units = isset($options['units']) ? $options['units'] : 'imperial';

    	switch ($options['box_split']) {
    		case 'packing_api':
    			$package_items = $this->pack_into_boxes($items, $options['token']);
    			break;
    		case 'box_per_item':
    			$package_items = $items;
    			break;
    		case 'by_weight':
    			$split_weight = $options['box_split_weight'];
    			$package_items = $this->split_by_weight($items, $split_weight);
    			break;
    		default:
    			$package_items = $this->make_one_box($items);
    			break;
    	}

    	return array(
            'items' => $package_items,
            'units' => $units,
            'type' => 'package',
        );
    }

    protected function extract_items($order_items, $options) {
        $weight_unit = get_option('woocommerce_weight_unit');
        $dimension_unit = get_option('woocommerce_dimension_unit');
        $output_weight_unit = isset($options['weight_unit']) ? $options['weight_unit'] : 'lbs';
        $output_dimension_unit = isset($options['dimension_unit']) ? $options['dimension_unit'] : 'in';
        $items = array();

        foreach ( $order_items as $item_id => $product_item ) {
            $product = $product_item['product'];

            $weight = $product->get_weight() ? round(wc_get_weight($product->get_weight(), $output_weight_unit, $weight_unit)) : 1;
            $length = $product->get_length() ? round(wc_get_dimension($product->get_length(), $output_dimension_unit, $dimension_unit)) : 1;
            $width = $product->get_width() ? round(wc_get_dimension($product->get_width(), $output_dimension_unit, $dimension_unit)) : 1;
            $height = $product->get_height() ? round(wc_get_dimension($product->get_height(), $output_dimension_unit, $dimension_unit)) : 1;
           	$description = $product->get_name();
            $shippingClass = $product->get_shipping_class();

           	$item = array(
           		'length' => $length,
           		'width' => $width,
           		'height' => $height,
           		'weight' => $weight,
           		'description' => $description,
                'shipping_class' => $shippingClass,
           	);
           	$items = array_merge($items, array_fill(0, $product_item['quantity'], $item));
        }

        return $items;
    }

    protected function make_one_box($items, $return_list = true) {
    	$descriptions = array_unique(array_column($items, 'description'));
    	$description = implode(';', $descriptions);

        if (strlen($description) > 35) {
            $description = $descriptions[0];
        }

        $total_weight = array_sum(array_column($items, 'weight'));

        $item = array(
            'length' => max(array_column($items, 'length')),
            'width' => max(array_column($items, 'width')),
            'height' => max(array_column($items, 'height')),
            'weight' => $total_weight,
            'description' => $description,
        );

        return $return_list ? array($item) : $item;
    }

    protected function pack_into_boxes($items, $token) {
    	$boxes_data = Package_Box_Controller::get_boxes();

    	if (!$boxes_data) {
			return $this->make_one_box($items);
    	}

    	$boxes = json_decode($boxes_data, true);
    	$package_request = new Packing_Request($token, $this->debug_mode);
    	$packed_boxes = $package_request->pack_boxes($items, $boxes);

    	return $packed_boxes ? $packed_boxes : $this->make_one_box($items);
    }

    protected function split_by_weight($items, $split_weight) {
    	if (!$split_weight || max(array_column($items, 'weight')) > $split_weight) {
    		$this->show_notice('Cannot split items into boxes because of the split weight', 'error');

    		return $this->make_one_box($items);
    	}

    	$split_boxes = array();
    	$box_items = null;

    	while (count($items) > 0 && ( $box_items === null || $box_items)) {
    		$split_results = $this->split_one_box($items, $split_weight);
    		$items = $split_results['remaining_items'];
    		$box_items = $split_results['box_items'];

    		if ($box_items) {
    			$split_boxes[] = $this->make_one_box($box_items, false);
    		}
    	}

    	return $split_boxes && $box_items ? $split_boxes : $this->make_one_box($items);
    }

    protected function split_one_box($items, $split_weight) {
    	$box_weight = 0;
    	$selected_items = array();
    	$next_item = reset($items);

    	while (count($items) > 0 && $box_weight + $next_item['weight'] <= $split_weight) {
			$items = array_slice($items, 1);
			$selected_items[] = $next_item;
			$box_weight += $next_item['weight'];
    		$next_item = reset($items);
    	}

    	return array(
    		'remaining_items' => $items,
    		'box_items' => $selected_items,
    	);
    }

    protected function show_notice($message, $type = 'notice') {
        if ($this->debug_mode) {
            wc_add_notice($message, $type);
        }
    }
}
