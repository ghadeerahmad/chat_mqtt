<?php

use App\Models\RoomBlacklist;
use App\Models\RoomPrivilege;
use App\Models\RoomUserPrivilege;
use Illuminate\Support\Facades\Auth;

if (! function_exists('check_room_role')) {
    function check_room_role($code, $id)
    {
        if (Auth::check()) {
            $user = auth()->user();
            $room_user_privilege = RoomUserPrivilege::where('user_id', $user->id)
                ->where('room_id', $id)
                ->first();
            if ($room_user_privilege == null) {
                return false;
            }
            $room_privielge = RoomPrivilege::with(['room_privilege_roles'])
                ->find($room_user_privilege->room_privilege_id);
            $roles = $room_privielge->room_privilege_roles;
            foreach ($roles as $role) {
                if ($role->room_role->code == $code) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (! function_exists('is_room_admin')) {
    function is_room_admin($room_id, $user_id)
    {
    }
}

if (! function_exists('is_banned')) {
    function is_banned($user_id, $room_id)
    {
        $check = RoomBlacklist::where('user_id', $user_id)
        ->where('room_id', $room_id)->first();
        if ($check != null) {
            return true;
        }

        return false;
    }
}
