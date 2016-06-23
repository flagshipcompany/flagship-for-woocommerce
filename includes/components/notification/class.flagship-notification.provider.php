<?php

require_once __DIR__.'/class.flagship-notification.php';

class Flagship_Notification_Provider
{
    public function provide(Flagship_Application $flagship)
    {
        $flagship['notification'] = new Flagship_Notification();
    }
}
