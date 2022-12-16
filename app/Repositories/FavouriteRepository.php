<?php

namespace App\Repositories;

use App\Http\Requests\Favourite\CreateFavouriteRequest;
use App\Models\Favourite;
use Illuminate\Support\Facades\DB;

class FavouriteRepository extends BaseRepository
{
    public function model()
    {
        return Favourite::class;
    }

    /**
     * get favourite list
     *
     * @return mixed
     */
    public function all()
    {
        $user = auth()->user();
        $fav = Favourite::with(['room'])
            ->join('rooms', 'rooms.id', '=', 'favourites.room_id')
            ->where('rooms.is_deleted', 'no')
            ->where('favourites.user_id', $user->id)
            ->get();
        $data = [];
        foreach ($fav as $item) {
            $itemData = $item->toArray();
            $count = $this->count($item->room_id)->user_count;
            $itemData['user_count'] = $count;
            $itemData['room']['user_count'] = $count;
            $data[] = $itemData;
        }
        return $data;
    }

    /**
     * add new room to favourite
     *
     * @param  CreateFavouriteRequest  $request
     * @return mixed
     */
    public function create(CreateFavouriteRequest $request)
    {
        $user = auth()->user();
        $check = Favourite::where('user_id', $user->id)
            ->where('room_id', $request['room_id'])
            ->first();
        if ($check == null) {
            $fav = Favourite::create([
                'user_id' => $user->id,
                'room_id' => $request['room_id'],
            ]);

            return $fav;
        }

        return $check;
    }

    /**
     * remove room from favourite
     *
     * @param  int  $room_id
     * @return bool
     */
    public function delete($room_id)
    {
        $user = auth()->user();
        Favourite::where('user_id', $user->id)
            ->where('room_id', $room_id)
            ->delete();

        return true;
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
