<?php

namespace App\Traits;

use App\Models\Background;
use App\Models\RoomBackground;
use App\Models\RoomUserPrivilege;

trait RoomTrait
{
    /**
     * check user if is admin in the room
     *
     * @param  int  $room_id
     * @return bool
     */
    public function isAdmin($room_id)
    {
        $user = auth()->user();
        $check = RoomUserPrivilege::where('user_id', $user->id)
            ->where('room_id', $room_id)->first();
        if ($check != null) {
            return true;
        }

        return false;
    }

    /**
     * get room background
     *
     * @param int room_id
     */
    public function room_background($room_id)
    {
        $background = RoomBackground::with(['background'])
        ->where('room_id', $room_id)
        ->where('is_selected', 1)
        ->first();
        if ($background == null) {
            $background = Background::where('is_default', 1)->first();
        } else {
            $background = $background->background;
        }

        return $background;
    }
}
