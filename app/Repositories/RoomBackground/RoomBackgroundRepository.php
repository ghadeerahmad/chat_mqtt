<?php

namespace App\Repositories\RoomBackground;

use App\Http\Requests\RoomBackgroundRequest;
use App\Models\Background;
use App\Models\RoomBackground;

class RoomBackgroundRepository implements RoomBackgroundsInterface
{
    public function index()
    {
        $backgrounds = Background::all();

        return success_response($backgrounds);
    }

    public function select(RoomBackgroundRequest $request)
    {
        $background = RoomBackground::with(['background'])->where('room_id', $request['room_id'])
            ->where('background_id', $request['background_id'])->first();
        if ($background == null) {
            $background = RoomBackground::create(['room_id' => $request['room_id'], 'background_id' => $request['background_id']]);
        }
        if ($background) {
            RoomBackground::where('room_id', $request['room_id'])->update(['is_selected' => '0']);
            $background->is_selected = 1;
            $background->save();

            return success_response($background->background);
        }

        return server_error_response();
    }
}
