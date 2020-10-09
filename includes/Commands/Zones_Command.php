<?php
namespace FlagshipWoocommerce\Commands;

use FlagshipWoocommerce\FlagshipWoocommerceShipping;

class Zones_Command {

    /**
    * List all the shipping zones with FlagShip enabled.
    *
    * ## OPTIONS
    *
    * [--enabled]
    * : Return only zones with FlagShip enabled.
    *
    * ## EXAMPLES
    *
    *     # List all the shipping zones (if --enabled is set, only zones with FlagShip enabled).
    *     $ wp fcs zones list [--enabled]
    *     Canada/United States
    */
    public function list($args, $assoc_args) {
        $enabledOnly = isset($assoc_args['enabled']);
        $enabledZones = $this->getShippingZones($enabledOnly);

        array_walk($enabledZones, function($zone) {
            \WP_CLI::line($zone->get_zone_name());
        });
    }

    /**
    * Create a shipping zone with location (without specifying a user as required in WooCommerce-cli).
    *
    * ## OPTIONS
    *
    * [<name>]
    * : The zone name. If there needs to be space, use underscore instead. Example: to set the name to 'United States', enter 'United_States'
    *
    * [<location_code>]
    * : The location code. If it is a country, it is like 'CA'. If it is a region, it is like 'CA:QC'
    *
    * [<location_type>]
    * : The location type. It could be country, state, or continent
    *
    * [--<enable_flagship]
    * : Add FlagShip shipping method.
    *
    * ## EXAMPLES
    *
    *     # Add a zone called United States for United States 
    *     $ wp fcs zones create United_States US --enable_flagship
    */
    public function create($args, $assoc_args) {
        $zoneName = isset($args[0]) ? str_replace('_', ' ', $args[0]) : null;
        $locationCode = isset($args[1]) ? $args[1] : null;
        $locationType = isset($args[2]) ? $args[2] : null;

        if (!$zoneName || !$locationCode || !$locationType) {
            \WP_CLI::error('Invalid arguments!');
        }

        $zone = new \WC_Shipping_Zone();
        $zone->set_zone_name($zoneName);
        $location = array(
            'code' => $locationCode,
            'type' => $locationType,
        );
        $zone->set_locations(array($location));

        $msg = sprintf('Shipping zone %s has been saved for location: %s.', $zoneName, $locationCode);

        if (isset($assoc_args['enable_flagship'])) {
            $zone->add_shipping_method(FlagshipWoocommerceShipping::$methodId);
            $msg .= ' FlagShip shipping is enabled';            
        }

        $zone->save();

        \WP_CLI::success($msg);
    }

    protected function getShippingZones($enabledOnly = false) {
        $flagshipMethod = FlagshipWoocommerceShipping::$methodId;
        $shippingZones = array_map(function($zone) {
            return new \WC_Shipping_Zone($zone);
        }, \WC_Data_Store::load( 'shipping-zone' )->get_zones());

        if (!$enabledOnly) {
            return $shippingZones;
        }

        $enabledZones = array_filter($shippingZones, function($zone) use ($flagshipMethod) {
            $methods = $zone->get_shipping_methods();
            $methods = array_filter($methods, function($val) {
                return $val->is_enabled();
            });
            $methodNames = array_map(function($val) {
                return $val->id;
            }, $methods);

            return in_array($flagshipMethod, $methodNames);
        });

        return $enabledZones;
    }
}