<?php
namespace FlagshipWoocommerce\Helpers;

use FlagshipWoocommerce\FlagshipWoocommerceShipping;
use FlagshipWoocommerce\Routes\Get_Package_Boxes;

class Script_Helper {

	public function load_scripts($hook) {
		if ($hook != 'flagship_page_flagship/boxes') {
			return;
		}

		$vuejsFileName = FlagshipWoocommerceShipping::isDebugMode() ? 'vue.js' : 'vue.prod.js';

	    wp_register_script('axios', $this->get_file_path('axios.min.js'));
	    wp_register_script('vuejs', $this->get_file_path($vuejsFileName));
	    wp_register_script('package_boxes', $this->get_file_path('package_boxes.js'), array('vuejs', 'axios'));
	    wp_register_style('flagship_style', $this->get_file_path('flagship_style.css', true));
	}

	protected function get_file_path($file_name, $is_css = false) {
		$sub_folder = $is_css ? 'css/' : 'js/';

		return plugin_dir_url(FLAGSHIP_PLUGIN_FILE).'assets/'.$sub_folder.$file_name;
	}
}