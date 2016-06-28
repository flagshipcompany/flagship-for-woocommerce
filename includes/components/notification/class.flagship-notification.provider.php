<?php

require_once __DIR__.'/class.flagship-notification.php';

class Flagship_Notification_Provider
{
    public function provide(Flagship_Application $ctx)
    {
        $ctx['notification'] = new Flagship_Notification($ctx);
    }
}
