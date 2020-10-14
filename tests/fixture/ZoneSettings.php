<?php

$settings = <<<SETTINGS
{
    "send_tracking_emails": true,
    "box_split": "packing_api",
    "box_split_weight": "10",
    "token": "fake_token"
}
SETTINGS
;

return json_decode($settings, true);
