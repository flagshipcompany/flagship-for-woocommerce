<?php
namespace FlagshipWoocommerce\REST_Controllers;

use FlagshipWoocommerce\FlagshipWoocommerceShipping;

class Package_Box_Controller extends \WP_REST_Controller {

	public static function getSettingsOptionKey() {
		return 'woocommerce_'.FlagshipWoocommerceShipping::$methodId.'_boxes';
	}

	public static function get_namespace() {
		return sprintf('flagship/%s', FlagshipWoocommerceShipping::$version);
	}

	public static function get_boxes() {
		return get_option(self::getSettingsOptionKey(), null);
	}

	public function register_routes() {
		register_rest_route(self::get_namespace(), '/package_boxes/get', array(
				'methods' => \WP_REST_Server::READABLE,
				'callback' => array($this, 'get_package_boxes'),
				'args' => array(
				),
				'permissions_callback' => '__return_true',  //array($this, 'box_permissions'),
	      	),
   		);

   		register_rest_route(self::get_namespace(), '/package_boxes/save', array(
				'methods' => \WP_REST_Server::CREATABLE,
				'callback' => array($this, 'save_package_boxes'),
				'args' => array(
				),
				'permissions_callback' => '__return_true', //array($this, 'box_permissions'),
	      	),
   		);
	}

	public function box_permissions() {
		return current_user_can('manage_options');
	}

	public function get_package_boxes() {
		return new \WP_REST_Response(self::get_boxes(), 200);
	}

	public function save_package_boxes($request) {
		$request_data = $request->get_body();
		$is_valid = $this->validate_boxes($request_data);

		if (!$is_valid) {
			return new \WP_Error('cant-save', __( 'message', 'text-domain' ), array( 'status' => 400 ));
		}

		update_option(self::getSettingsOptionKey(), $request_data);

		return new \WP_REST_Response($request_data, 200);
	}

	protected function validate_boxes($boxes) {
		$boxes = json_decode($boxes, true);

		$validBoxes = array_filter($boxes, function($box) {
			return $box['model']
				&& $box['length'] && floatval($box['length']) == $box['length'] && $box['length'] > 0
				&& $box['width'] && floatval($box['width']) == $box['width'] && $box['width'] > 0
				&& $box['height'] && floatval($box['height']) == $box['height'] && $box['height'] > 0
				&& $box['max_weight'] && floatval($box['max_weight']) == $box['max_weight'] && $box['max_weight'] > 0
				&& (empty($box['extra_charge']) || floatval($box['extra_charge']) == $box['extra_charge']);
		});

		return count($validBoxes) == count($boxes);
	}
}
