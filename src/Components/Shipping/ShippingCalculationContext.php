<?php

namespace FS\Components\Shipping;

/**
 * Keep deferred background calculations out of the storefront rate cache.
 */
class ShippingCalculationContext
{
    private static $routes = array();
    private static $registered = false;

    public static function register()
    {
        if (self::$registered) {
            return;
        }
        self::$registered = true;
        add_filter('woocommerce_cart_shipping_packages', array(__CLASS__, 'addCacheContext'), 100);
        // Track the actual dispatched request, including nested batch requests.
        add_filter('rest_request_before_callbacks', array(__CLASS__, 'beforeRestCallbacks'), -1000, 3);
        add_filter('rest_request_after_callbacks', array(__CLASS__, 'afterRestCallbacks'), 1000, 3);
    }

    public static function beforeRestCallbacks($response, $handler, $request)
    {
        self::$routes[] = $request->get_route();
        return $response;
    }

    public static function afterRestCallbacks($response, $handler, $request)
    {
        array_pop(self::$routes);
        return $response;
    }

    public static function addCacheContext($packages)
    {
        $context = self::allowsQuotes() ? 'quotes-v1' : 'deferred-v1';
        foreach ($packages as &$package) {
            // WooCommerce includes this field in its shipping package hash.
            $package['flagship_calculation_context'] = $context;
        }
        unset($package);
        return $packages;
    }

    public static function allowsQuotes()
    {
        if ((defined('REST_REQUEST') && REST_REQUEST) || !empty(self::$routes)) {
            $route = !empty(self::$routes)
                ? end(self::$routes)
                : (isset($GLOBALS['wp']->query_vars['rest_route']) ? $GLOBALS['wp']->query_vars['rest_route'] : '');
            $route = rtrim($route, '/');
            return in_array($route, array(
                '/wc/store/v1/cart',
                '/wc/store/v1/cart/update-customer',
                '/wc/store/v1/cart/update-item',
                '/wc/store/v1/cart/apply-coupon',
                '/wc/store/v1/cart/remove-coupon',
                '/wc/store/v1/cart/select-shipping-rate',
                '/wc/store/v1/checkout',
            ), true);
        }

        // WooCommerce's frontend AJAX dispatcher uses wc-ajax, not POST action.
        $wcAction = isset($_GET['wc-ajax']) ? sanitize_key(wp_unslash($_GET['wc-ajax'])) : '';
        if ($wcAction !== '') {
            return in_array($wcAction, array(
                'update_order_review', 'checkout', 'update_shipping_method',
                'apply_coupon', 'remove_coupon', 'get_cart_totals',
            ), true);
        }

        if (wp_doing_ajax()) {
            $action = isset($_POST['action']) ? sanitize_key(wp_unslash($_POST['action'])) : '';
            return in_array($action, array(
                'woocommerce_update_order_review', 'woocommerce_checkout',
                'woocommerce_update_shipping_method', 'woocommerce_apply_coupon',
                'woocommerce_remove_coupon', 'woocommerce_get_cart_totals',
            ), true);
        }

        // A non-AJAX add-to-cart submission can also target the cart page.
        if (isset($_REQUEST['add-to-cart']) || isset($_GET['remove_item']) || isset($_GET['undo_item'])) {
            return false;
        }

        return is_cart() || is_checkout();
    }
}
