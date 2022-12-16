<?php

use PhpMqtt\Client\Facades\MQTT;

if (!function_exists('publish_message')) {
    function publish_message($topic, $message)
    {
        MQTT::publish('/' . $topic, $message);
    }
}
if (!function_exists('get_message_json')) {
    function get_message_json($user, $type, $message, $reciever = null, $is_special = false)
    {
        $msg = ['user_id' => null, 'text' => $message];
        if ($reciever != null) {
            $msg = ['user_id' => $reciever->id, 'text' => $message];
        }
        $data = [
            'user' => $user,
            'type' => $type,
            'message' => $msg,
            'is_special' => $is_special
        ];

        return json_encode($data);
    }
}
if (!function_exists('get_private_message_json')) {
    function get_private_message_json($user, $type, $message)
    {
        $data = [
            'user' => $user,
            'type' => $type,
            'message' => $message,
        ];

        return json_encode($data);
    }
}
if (!function_exists('publish_ads')) {
    function publish_ads($ads)
    {
        MQTT::publish('/ads', json_encode($ads));
    }
}
