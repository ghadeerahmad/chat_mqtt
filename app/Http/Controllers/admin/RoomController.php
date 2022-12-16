<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminRole:view_room')->only(['index', 'show']);
        $this->middleware('adminRole:update_room')->only('update');
        $this->middleware('adminRole:delete_room')->only('delete');
    }

    /**
     * get list of rooms
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::with(['user', 'country'])
            ->where('is_deleted', 'no')
            ->paginate(20);

        return response()->json(['status' => 1, 'data' => $rooms]);
    }

    /**
     * get room details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = Room::where('id', $id)->where('is_deleted', 'no')->first();
        if ($room == null) {
            return response()->json(['status' => 0, 'message' => 'room not found'], 404);
        }

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $room]);
    }

    /**
     * create new room
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'description' => 'required|max:255',
            'image' => 'nullable|mimes:jpg,png,gif|max:2048',
            'topic' => 'required|max:50',
            'max_users' => 'required',
            'password' => 'nullable|max:255',
            'user_id' => 'required',
            'background' => 'nullable|mimes:jpg,png,gif|max:2048',
            'country_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
        }
        $data = [
            'name' => $request['name'],
            'description' => $request['description'],
            'topic' => $request['topic'],
            'max_users' => $request['max_users'],
            'user_id' => $request['user_id'],
            'country_id' => $request['country_id'],
        ];
        if ($request['background'] != null) {
            $data['background'] = $request['background'];
        }
        if ($request['password'] != null) {
            $data['password'] = $request['password'];
        }
        $identifier = Random::generate(10);
        $idf = Room::where('identifier', $identifier)->first();
        while ($idf != null) {
            $identifier = Random::generate(10);
            $idf = Room::where('identifier', $identifier)->first();
        }
        $data['identifier'] = $identifier;
        $room = Room::create($data);
        if ($room) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('rooms', $fileName, 'public');
                $room->image = $path;
                $room->save();
            }
            if ($request->hasFile('background')) {
                $file = $request->file('background');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('rooms', $fileName, 'public');
                $room->background = $path;
                $room->save();
            }
            $room = Room::with(['user', 'country'])->find($room->id);

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $room]);
        }

        return response()->json(['status' => 0, 'message' => 'error'], 500);
    }

    /**
     * update room
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $room = Room::where('id', $id)->where('is_deleted', 'no')->first();
        if ($room == null) {
            return response()->json(['status' => 0, 'message' => 'room not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'description' => 'required|max:255',
            'image' => 'nullable|mimes:jpg,png,gif|max:2048',
            'topic' => 'required|max:50',
            'max_users' => 'required',
            'password' => 'nullable|max:255',
            'background' => 'nullable|mimes:jpg,png,gif|max:2048',
            'country_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $data = [
            'name' => $request['name'],
            'description' => $request['description'],
            'topic' => $request['topic'],
            'max_users' => $request['max_users'],
            'country_id' => $request['country_id'],
        ];
        if ($request['background'] != null) {
            $data['background'] = $request['background'];
        }
        if ($request['password'] != null) {
            $data['password'] = $request['password'];
        }

        $room->update($data);
        if ($room) {
            if ($request->hasFile('image')) {
                if ($room->image != null) {
                    Storage::disk('public')->delete($room->image);
                }
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('rooms', $fileName, 'public');
                $room->image = $path;
                $room->save();
            }
            if ($request->hasFile('background')) {
                if ($room->background != null) {
                    Storage::disk('public')->delete($room->background);
                }
                $file = $request->file('background');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('rooms', $fileName, 'public');
                $room->background = $path;
                $room->save();
            }
            $room = Room::with(['user', 'country'])->find($room->id);

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $room]);
        }

        return response()->json(['status' => 0, 'message' => 'error'], 500);
    }

    /**
     * soft delete the room
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $room = Room::find($id);
        $room->is_deleted = 'yes';
        $room->save();

        return response()->json(['status' => 1, 'message' => 'success']);
    }
}
