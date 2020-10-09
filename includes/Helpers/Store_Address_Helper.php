<?php
namespace FlagshipWoocommerce\Helpers;

class Store_Address_Helper {

    public function add_extra_address_fields($settings)
    {
        $addressSectionStartEnd = $this->getAddressStartEndPos($settings);

        if (!$addressSectionStartEnd) {
            return $settings;
        }

        $fields = $this->getFields();

        array_splice($settings, $addressSectionStartEnd[1], 0, array($fields['phone']));
        array_splice($settings, $addressSectionStartEnd[0] + 1, 0, array($fields['store_name'], $fields['attn']));

        return $settings;
    }

    protected function getAddressStartEndPos($settings)
    {
        $startPosition = null;
        $endPosition = null;

        array_walk($settings, function($val, $key) use (&$startPosition, &$endPosition) {
            if ($val['id'] == 'store_address' && $val['type'] == 'sectionend') {
                $endPosition = $key;
            } elseif ($val['id'] == 'store_address' && $val['type'] == 'title') {
                $startPosition = $key;
            }
        });

        if (is_null($startPosition) || is_null($endPosition)) {
            return;
        }

        return array($startPosition, $endPosition);
    }

    protected function getFields()
    {
        return array(
            'store_name' => array(
                "title" => __('Store name', 'flagship-for-woocommerce'),
                "desc" => __('The store name will be used for FlagShip shipments', 'flagship-for-woocommerce'),
                "id" => "woocommerce_store_name",
                "default" => "",
                "type" => "text",
                "desc_tip" => true,
            ),
            'attn' => array(
                "title" => __('Attention', 'flagship-for-woocommerce'),
                "desc" => __('Attention will be the sender of FlagShip shipments', 'flagship-for-woocommerce'),
                "id" => "woocommerce_store_attn",
                "default" => "",
                "type" => "text",
                "desc_tip" => true,
            ),
            'phone' => array(
                "title" => __('Phone', 'flagship-for-woocommerce'),
                "desc" => __('This phone number will be used for FlagShip shipments', 'flagship-for-woocommerce'),
                "id" => "woocommerce_store_phone",
                "default" => "",
                "type" => "text",
                "desc_tip" => true,
            ),
        );
    }

}