<?php
namespace FlagshipWoocommerce;

use FlagshipWoocommerce\Helpers\Menu_Helper;
use FlagshipWoocommerce\Helpers\Notification_Helper;
use FlagshipWoocommerce\Helpers\Product_Helper;
use FlagshipWoocommerce\Helpers\Store_Address_Helper;
use FlagshipWoocommerce\Helpers\Script_Helper;
use FlagshipWoocommerce\REST_Controllers\Package_Box_Controller;

class FlagshipWoocommerceShipping {

	public static $methodId = 'flagship_shipping_method';

	public static $version = '3.0.1';

	public static $couriers = array(
		'UPS' => 'ups',
        'DHL' => 'dhl',
        'FedEx' => 'fedex',
        'Purolator' => 'purolator',
        'Canpar' => 'canpar',
		'Dicom' => 'dicom',
	);

	public static $dropShippingAddressFields = array(
		'name' => 'Name',
		'company' => 'Company',
		'street_address' => 'Street Address',
		'suite' => 'Suite',
		'city' => 'City',
		'state' => 'Province',
		'postal_code' => 'Postal Code',
		'phone' => 'Phone',
		'phone_ext' => 'Phone Ext',
	);

	protected static $_instance = null;

	public static function instance() {
		if (is_null( self::$_instance )) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function isDebugMode() {
		return getenv('FLAGSHIP_DEBUG_MODE') == true;
	}

	public static function getSettingsOptionKey() {
		return 'woocommerce_'.self::$methodId.'_settings';
	}

	public static function getFlagshipUrl() {
		return 'https://smartship-ng.flagshipcompany.com';
	}

	public function __construct() {
		$this->showTestEnvNotification();
		$this->hooks();
	}

	public function showTestEnvNotification() {
		$settings = get_option(self::getSettingsOptionKey());
		$testEnv = $settings["test_env"] == 'no' || $settings['test_env'] == null ? 0 : 1;
		if($testEnv){
			add_action( 'admin_notices', array((new Notification_Helper()), 'add_test_env_notice'));
		}
		return;
	}

	public function hooks() {
		add_filter( 'woocommerce_shipping_methods', array($this, 'add_flagship_shipping_method'));
	    add_filter( 'plugin_action_links_' . FLAGSHIP_PLUGIN_NAME, array( $this, 'plugin_action_links' ) );
	    add_action( 'add_meta_boxes', array($this, 'add_custom_meta_box'));
	    add_action( 'woocommerce_process_shop_order_meta', array($this, 'save_meta_box'));
	    add_action('admin_notices', array((new Notification_Helper()), 'flagship_warning_in_notice'));
	    add_action('admin_menu', array((new Menu_Helper()), 'add_flagship_to_menu'));
	    add_filter('woocommerce_general_settings', array((new Store_Address_Helper()), 'add_extra_address_fields'));

	    $productHelper = (new Product_Helper());
	    add_filter('woocommerce_product_data_tabs', array($productHelper, 'add_export_to_product_tabs') );
	    add_action( 'woocommerce_product_data_panels', array($productHelper, 'display_product_export_tab') );
	    add_action('woocommerce_process_product_meta', array($productHelper, 'save_product_export_data') );
	    add_action('rest_api_init', array((new Package_Box_Controller()), 'register_routes') );
	    add_action('admin_enqueue_scripts', array((new Script_Helper()), 'load_scripts'));
	}

	public function plugin_action_links($links) {
		$settingsUrl = 'admin.php?page=wc-settings&tab=shipping&section='.self::$methodId;
		$plugin_links = array(
			'<a href="' . admin_url($settingsUrl) . '">' . __( 'Settings', 'flagship-for-woocommerce' ) . '</a>',
		);

		return array_merge($plugin_links, $links);
	}

	public function save_meta_box($orderId) {
		$orderActionProcessor = $this->init_order_action_processor(wc_get_order($orderId));

        return $orderActionProcessor->processOrderActions($_POST);
	}

	public function add_flagship_shipping_method($methods) {
		$methods[self::$methodId] = new WC_Flagship_Shipping_Method();

		return $methods;
	}

    public function add_custom_meta_box() {
    	global $post;

    	$order = wc_get_order($post->ID);

    	if (!$order) {
    		return;
    	}

    	$orderActionProcessor = $this->init_order_action_processor($order);
    	$orderActionProcessor->addMetaBoxes($order);
    }

	protected function init_order_action_processor($order)
	{
        $settings = get_option(self::getSettingsOptionKey());

        return new Order_Action_Processor($order, $settings);
	}
}
