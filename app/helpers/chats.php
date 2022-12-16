<?php

use App\Models\Chat;
use Nette\Utils\Random;

if (! function_exists('chat_exists')) {
    function chat_exists($user_id)
    {
        $user = auth()->user();
        $check = Chat::where('sender_id', $user_id)
            ->where('reciever_id', $user->id)->first();
        if ($check != null) {
            return true;
        }
        $check = Chat::where('sender_id', $user->id)
            ->where('reciever_id', $user_id)->first();
        if ($check != null) {
            return true;
        }

        return false;
    }
}

if (! function_exists('get_chat')) {
    function get_chat($user_id)
    {
        $user = auth()->user();
        $check = Chat::where('sender_id', $user_id)
            ->where('reciever_id', $user->id)->first();
        if ($check != null) {
            return $check;
        }
        $check = Chat::where('sender_id', $user->id)
            ->where('reciever_id', $user_id)->first();
        if ($check != null) {
            return $check;
        }

        return null;
    }
}

if (! function_exists('create_identifier')) {
    function create_identifier()
    {
        $identifier = Random::generate(30);
        $check = Chat::where('identifier', $identifier)->first();
        while ($check != null) {
            $identifier = Random::generate(30);
            $check = Chat::where('identifier', $identifier)->first();
        }

        return $identifier;
    }
}
