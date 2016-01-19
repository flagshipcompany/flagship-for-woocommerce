<?php

class Flagship_Notification
{
    public $notifications;

    public function __construct()
    {
        $this->notifiactions = array();
    }

    public function add($type = 'success', $message)
    {
        if (!isset($this->notifications[$type])) {
            $this->notifications[$type] = array();
        }

        $this->notifications[$type][] = $message;

        return $this;
    }

    public function view()
    {
        if (!$this->notifications) {
            return;
        }

        Flagship_View::notification(array('notifications' => $this->notifications));
    }
}
