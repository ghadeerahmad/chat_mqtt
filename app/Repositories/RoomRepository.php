<?php

namespace App\Repositories;

use App\Models\Room;
use Illuminate\Support\Facades\DB;

class RoomRepository extends BaseRepository
{
    public function model()
    {
        return Room::class;
    }

    /**
     * filter rooms by country id
     *
     * @param  int  $country_id
     * @return mixed
     */
    public function filter($country_id)
    {
        $rooms = $this->model->with(['country'])
            ->where('country_id', $country_id)
            ->where('is_deleted', 'no')
            ->get();
        $data = [];
        foreach ($rooms as $room) {
            $roomData = $room->toArray();
            $roomData['user_count'] = $this->count($room->id)->user_count;
            $data[] = $roomData;
        }
        return $data;
    }
    /**
     * get users count in room
     */
    public function count($id)
    {
        return DB::table('rooms')->select(DB::raw('COUNT(room_users.id) as user_count'))
            ->join('room_users', 'rooms.id', '=', 'room_users.room_id')
            ->where('rooms.id', $id)
            ->first();
    }
}
