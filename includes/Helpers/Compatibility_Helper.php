<?php
namespace FlagshipWoocommerce\Helpers;

use  FlagshipWoocommerce\REST_Controllers\Package_Box_Controller;

class Compatibility_Helper {

    public function modify_settings($settings, $option_value) {
        $map = array(
            'box_split' => 'default_package_box_split',
            'box_split_weight' => 'default_package_box_split_weight',
        );

        foreach ($map as $key => $value) {
            if (!isset($settings[$key]) && isset($option_value[$value])) {
                $settings[$key] = $key == 'box_split' ? $this->convert_box_split($option_value[$value]) : $option_value[$value];
            }
        }

        if (!Package_Box_Controller::get_boxes() && isset($option_value['package_box'])) {
            $this->convert_to_box_option($option_value['package_box']);
        }

        return $settings;
    }

    protected function convert_to_box_option($boxes_data) {
    	if (!is_array($boxes_data)) {
    		return;
    	}

    	$id = 0;

        $boxes = array_map(function($box) use (&$id) {
        	++$id;
        	$box['id'] = $id;
            $box['model'] = $box['model_name'];
            unset($box['model_name']);

            if (isset($box['markup'])) {
                $box['extra_charge'] = $box['markup'];
                unset($box['markup']);
            }

            return $box;
        }, $boxes_data);

        update_option(Package_Box_Controller::getSettingsOptionKey(), json_encode($boxes));
    }


    protected function convert_box_split($split_option) {
    	$map = array(
            'no' => 'by_weight',
            'yes' => 'one_box',
            'each' => 'box_per_item',
            'packing' => 'packing_api',
        );

        return isset($map[$split_option]) ? $map[$split_option] : 'one_box';
    }
}