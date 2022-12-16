<?php

if (! function_exists('check_facebook_token')) {
    function check_facebook_token($token)
    {
    }
}

if (! function_exists('check_google_token')) {
    function check_google_token($token)
    {
        $client = new Google\Client();
        $client->setApplicationName('chat');
        $client->setDeveloperKey('AIzaSyDD3dLeSlvRBB9yFCILylWeNzl5VTTpQg0');
        $client->setClientId('941625999514-lkseopihtq57qbddlr347b17m1ka6bvt.apps.googleusercontent.com');
        $client->setClientSecret('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');
        //$client->setAccessToken($token);
        $client->addScope(['https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/plus.login']);
        $result = $client->verifyIdToken($token);

        return $result;
    }
}
