<?php

if (! function_exists('not_found_response')) {
    function not_found_response($message)
    {
        return response()->json(['status' => 0, 'message' => $message], 404);
    }
}

if (! function_exists('success_response')) {
    function success_response($data = [])
    {
        if (empty($data)) {
            return response()->json(['status' => 1, 'message' => 'success']);
        }

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $data]);
    }
}

if (! function_exists('server_error_response')) {
    function server_error_response()
    {
        return response()->json(['status' => 0, 'message' => 'server error'], 500);
    }
}

if (! function_exists('error_response')) {
    function error_response($message)
    {
        return response()->json(['status' => 0, 'message' => $message], 500);
    }
}

if (! function_exists('forbidden_response')) {
    function forbidden_response($message)
    {
        return response()->json(['status' => 0, 'message' => $message], 403);
    }
}
