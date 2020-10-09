<?php
namespace FlagshipWoocommerce\Helpers;

class Notification_Helper {

    public function flagship_warning_in_notice()
    {

        if (!isset($_REQUEST['flagship_warning']) || empty($_REQUEST['flagship_warning'])) {
            return;
        }

        $message = trim($_REQUEST['flagship_warning']);

        echo '<div class="notice notice-error is-dismissible"><p><strong>'
        .$message.
        '</strong></p></div>';
    }

    public function add_tracking_email_invalid_notice()
    {
        echo '<div class="updated notice error">
              <p>'.__( 'Email addresses for tracking are invalid.', 'flagship-for-woocommerce').'</p>
            </div>';
    }

    public function add_token_invalid_notice()
    {
        echo '<div class="updated notice error">
              <p>'.__( 'Invalid FlagShip Token', 'flagship-for-woocommerce').'</p>
            </div>';
    }

    public function add_test_env_notice()
    {
        echo '<div class="updated notice error">
              <p>'.__( 'You are using FlagShip in test mode. Any shipments made in the test environment will not be processed', 'flagship-for-woocommerce').'</p>
            </div>';
    }
}
