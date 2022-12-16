<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchRoomController extends Controller
{
    /**
     * search room by name or id
     *
     * @param  \Illuminate\Http\Request  $requst
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = [];
        if ($request['id'] != null) {
            $rooms = DB::table('rooms')
                ->select(DB::raw('rooms.*,COUNT(room_users.id) as user_count'))
                ->join('users', 'users.id', '=', 'rooms.user_id')
                ->join('room_users', 'room_users.room_id', '=', 'rooms.id')
                ->where('users.userId', $request['id'])
                ->where('rooms.is_deleted', 'no')
                ->groupBy('rooms.id')
                ->first();
            if ($rooms == null) {
                $rooms = DB::table('rooms')
                    ->select(DB::raw('rooms.*,COUNT(room_users.id) as user_count'))
                    ->join('users', 'users.id', '=', 'rooms.user_id')
                    ->join('room_users', 'room_users.room_id', '=', 'rooms.id')
                    ->join('reserved_ids', 'users.reserved_id_id', '=', 'reserved_ids.id')
                    ->where('reserved_ids.reserved_id', $request['id'])
                    ->where('rooms.is_deleted', 'no')
                    ->groupBy('rooms.id')
                    ->first();
            }
            $data[] = $rooms;
        } elseif ($request['name'] != null) {
            $rooms = DB::table('rooms')
                ->select(DB::raw('rooms.*,COUNT(room_users.id) as user_count'))
                ->join('room_users', 'room_users.room_id', '=', 'rooms.id')
                ->where('name', 'like', '%' . $request['name'] . '%')
                ->where('is_deleted', 'no')
                ->groupBy('rooms.id')
                ->get();
            $data = $rooms;
        }

        return success_response($data);
    }
}
