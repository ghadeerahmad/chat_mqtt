<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Background;
use App\Models\Favourite;
use App\Models\Room;
use App\Models\RoomBackground;
use App\Models\RoomBlacklist;
use App\Models\RoomChat;
use App\Models\RoomPrivilegeRole;
use App\Models\RoomUser;
use App\Models\RoomUserPrivilege;
use App\Models\SystemSetting;
use App\Models\User;
use App\Repositories\RoomRepository;
use App\Traits\RoomTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;

class RoomController extends Controller
{
    use RoomTrait;

    protected $roomRepo;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepo = $roomRepository;
    }

    /**
     * get top rooms
     *
     * @return \Illuminate\Http\Response
     */
    public function top()
    {
        $rooms = Room::with(['country'])->where('is_deleted', 'no')->get();
        $data = [];
        foreach ($rooms as $room) {
            $count = $this->roomRepo->count($room->id);
            $room_data = $room->toArray();
            $room_data['user_count'] = $count->user_count;
            array_push($data, $room_data);
        }

        return response()->json(['status' => 1, 'message' => trans('success'), 'data' => $data]);
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
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'image' => 'nullable|mimes:png,jpg,gif|max:2048',
            'topic' => 'required',
            'country_id' => 'required',
            'max_users' => 'required',
            'password' => 'nullable',
            'pinned_message' => 'nullable|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $data = [
            'name' => $request['name'],
            'description' => $request['description'],
            'topic' => $request['topic'],
            'country_id' => $request['country_id'],
            'max_users' => $request['max_users'],
            'user_id' => auth()->user()->id,
            'pinned_message' => $request['pinned_message'],
        ];
        if ($request['password'] != null) {
            $data['password'] = $request['password'];
        }
        $identifier = Random::generate(30);
        $check = Room::where('identifier', $identifier)->first();
        while ($check != null) {
            $identifier = Random::generate(30);
            $check = Room::where('identifier', $identifier)->first();
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
            RoomUserPrivilege::create(['user_id' => auth()->user()->id, 'room_id' => $room->id, 'room_privilege_id' => 1]);
            $room = Room::with(['user'])->find($room->id);

            return response()->json(['status' => 1, 'message' => trans('success'), 'data' => $room]);
        }

        return response()->json(['status' => 0, 'message' => trans('error')], 500);
    }

    /**
     * join room
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function joinRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'password' => 'nullable',
        ]);
        $room = Room::find($request['id']);
        $background = RoomBackground::with(['background'])
            ->where('room_id', $room->id)
            ->where('is_selected', 1)
            ->first();
        if ($background == null) {
            $background = Background::where('is_default', 1)->first();
        } else {
            $background = $background->background;
        }
        $room->makeVisible(['identifier']);
        $user = auth()->user();
        $isBlocked = RoomBlacklist::where('room_id', $request['id'])
            ->where('user_id', $user->id)->first();
        if ($isBlocked != null) {
            if ($isBlocked->until == null) {
                return response()->json(['status' => 0, 'message' => trans('you are blocked in this room')], 403);
            }
            $now = time();
            if ($isBlocked->until > $now) {
                $minutes = $isBlocked->until - $now;
                $minutes = $minutes / 60;

                return forbidden_response(trans('you are blocked in this room') . $minutes . ' minutes');
            }
        }
        $system_pinned_message = SystemSetting::first();
        $check = RoomUser::where('user_id', $user->id)
            ->where('room_id', '!=', $room->id)
            ->first();
        if ($check != null) {
            return response()->json(['status' => 0, 'message' => trans('already joined in another room')], 403);
        }
        if (is_banned($user->id, $request['id'])) {
            return response()->json(['status' => 0, 'message' => 'forbidden'], 403);
        }
        if ($room == null || $room->is_deleted == 'yes') {
            return response()->json(['status' => 0, 'message' => 'Room Not Found'], 404);
        }
        if ($room->password != null && $request['password'] == null) {
            return response()->json(['status' => 0, 'message' => trans('This Room is Locked you need to enter password')], 403);
        }
        if ($room->password != null) {
            if ($room->password == $request['password']) {
                $check = RoomUser::where('user_id', $user->id)
                    ->where('room_id', $room->id)
                    ->first();
                if ($check == null) {
                    // $count = DB::table('room_users')->select(DB::raw('COUNT(id) as count'))
                    //     ->where('room_id', $room->id)->first();
                    // if ($count->count >= $room->max_users && $user->id != $room->user_id) {
                    //     return error_response('room is full');
                    // }
                    $join = RoomUser::create(['user_id' => $user->id, 'room_id' => $room->id]);
                }
                $room_users = RoomUser::with(['user'])->where('room_id', $room->id)->get();

                $users_data = [];
                foreach ($room_users as $us) {
                    $room_privilege = RoomUserPrivilege::with(['room_privilege'])
                        ->where('room_id', $room->id)
                        ->where('user_id', $us->user_id)->first();
                    $room_user = $us->user->toArray();
                    $room_user['privilege'] = $room_privilege->room_privilege ?? null;
                    array_push($users_data, ['user' => $room_user]);
                }
                $privilege = RoomUserPrivilege::where('room_id', $room->id)
                    ->where('user_id', $user->id)->first();
                $message_data = get_message_json($user, 'join', $user->name . ' ' . trans('joined the room'));
                publish_message($room->identifier, $message_data);
                $roles = [];
                if ($privilege != null) {
                    $privilege_roles = RoomPrivilegeRole::with(['room_role'])->where('room_privilege_id', $privilege->room_privilege_id)->get();
                    $roles = $privilege_roles;
                }
                $room_data = $room->toArray();
                $room_data['user'] = User::with(['reserved_id'])->find($room->user_id);
                $room_data['background'] = $background;
                $is_fav = false;
                $check = Favourite::where('user_id', $user->id)
                    ->where('room_id', $room->id)->first();
                if ($check != null) {
                    $is_fav = true;
                }
                $room_data['is_favourite'] = $is_fav;

                return response()->json(['status' => 1, 'data' => ['room' => $room_data, 'system_room_message' => $system_pinned_message->rooms_pinned_message, 'users' => $users_data, 'roles' => $roles, 'privilege' => $privilege]]);
            }

            return response()->json(['status' => 0, 'message' => trans('passwords not matched')], 403);
        }
        $check = RoomUser::where('user_id', $user->id)->first();
        if ($check == null) {
            $join = RoomUser::create(['user_id' => $user->id, 'room_id' => $room->id]);
        }
        $room_users = RoomUser::with(['user'])->where('room_id', $room->id)->get();
        $users_data = [];
        foreach ($room_users as $us) {
            $room_privilege = RoomUserPrivilege::with(['room_privilege'])
                ->where('room_id', $room->id)
                ->where('user_id', $us->id)->first();
            $room_user = $us->user->toArray();
            $room_user['privilege'] = $room_privilege->room_privilege ?? null;
            array_push($users_data, ['user' => $room_user]);
        }
        $message_data = get_message_json($user, 'join', $user->name . ' ' . trans('joined the room'));
        $privilege = RoomUserPrivilege::where('room_id', $room->id)
            ->where('user_id', $user->id)->first();
        $roles = [];
        if ($privilege != null) {
            $privilege_roles = RoomPrivilegeRole::with(['room_role'])->where('room_privilege_id', $privilege->room_privilege_id)->get();
            $roles = $privilege_roles;
        }
        $room_data = $room->toArray();
        $room_data['user'] = User::with(['reserved_id'])->find($room->user_id);
        $room_data['background'] = $background;
        publish_message($room->identifier, $message_data);
        $is_fav = false;
        $check = Favourite::where('user_id', $user->id)
            ->where('room_id', $room->id)->first();
        if ($check != null) {
            $is_fav = true;
        }
        $room_data['is_favourite'] = $is_fav;

        return response()->json(['status' => 1, 'data' => ['room' => $room_data, 'system_room_message' => $system_pinned_message->rooms_pinned_message, 'users' => $users_data, 'roles' => $roles, 'privilege' => $privilege]]);
    }

    /**
     * leave room
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function leaveRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $user = auth()->user();
        $room = Room::find($request['id']);
        $room->makeVisible(['identifier']);
        $room_user = RoomUser::where('user_id', $user->id)
            ->where('room_id', $request['id'])->first();
        if ($room_user == null) {
            return response()->json(['status' => 0, 'message' => trans('you are not in the room')], 500);
        }
        $room_user->delete();
        $message_data = get_message_json($user, 'leave', $user->name . ' ' . trans('left'));
        publish_message($room->identifier, $message_data);

        return response()->json(['status' => 1, 'message' => trans('success')]);
    }

    /**
     * update room details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!check_room_role('update_room', $id)) {
            return response(['status' => 0, 'message' => 'forbidden'], 403);
        }
        $room = Room::with(['user'])->find($id);
        if ($room == null || $room->is_deleted == 'yes') {
            return response()->json(['status' => 0, 'message' => trans('room not found')], 404);
        }
        $background = $this->room_background($id);
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|max:50',
            'description' => 'nullable|max:255',
            'topic' => 'nullable|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'password' => 'nullable',
            'image' => 'nullable|max:2048|mimes:jpg,png,gif,jpeg',
            'max_users' => 'nullable',
            'pinned_message' => 'nullable|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $data = [];
        if ($request['name'] != null) {
            $data['name'] = $request['name'];
        }
        if ($request['description'] != null) {
            $data['description'] = $request['description'];
        }
        if ($request['topic'] != null) {
            $data['topic'] = $request['topic'];
        }
        if ($request['country_id'] != null) {
            $data['country_id'] = $request['country_id'];
        }
        if ($request['password'] != null) {
            $data['password'] = $request['password'];
        }
        if ($request['max_users'] != null) {
            $data['max_users'] = $request['max_users'];
        }
        if ($request['pinned_message'] != null) {
            $data['pinned_message'] = $request['pinned_message'];
        }
        $room->update($data);
        $room->makeVisible(['identifier']);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($room->image != null) {
                Storage::disk('public')->delete($room->image);
            }
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('rooms', $fileName, 'public');
            $room->image = $path;
            $room->save();
        }
        $room_data = $room->toArray();
        $room_data['background'] = $background;

        return response()->json(['status' => 1, 'message' => trans('success'), 'data' => $room_data]);
    }

    /**
     * delete room
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!check_room_role('delete_room', $id)) {
            return response()->json(['status' => 0, 'message' => 'forbidden'], 403);
        }
        $room = Room::find($id);
        RoomUser::where('room_id', $id)->delete();
        $room->is_deleted = 'yes';
        $room->save();

        return response()->json(['status' => 1, 'message' => trans('success')]);
    }

    /**
     * send message to room
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'message' => 'required|max:255',
            'is_special' => 'nullable|in:0,1'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $room = Room::find($request['room_id']);
        if ($room == null) {
            return response()->json(['status' => 0, 'message' => trans('room not found')], 404);
        }
        $room->makeVisible(['identifier']);
        $check = RoomUser::where('room_id', $request['room_id'])
            ->where('user_id', auth()->user()->id)->first();
        if ($check == null) {
            return response()->json(['status' => 0, 'message' => trans('you are not in the room')], 403);
        }
        //if (is_banned($user->id, $request['room_id'])) return response()->json(['status' => 0, 'message' => 'forbidden'], 403);
        $special = false;
        if ($request['is_special'] != null) {
            $special = $request['is_special'] == 1 ? true : false;
        }
        $message_data = get_message_json($user, 'message', $request['message'], null, $special);
        publish_message($room->identifier, $message_data);
        RoomChat::create(['room_id' => $request['room_id'], 'user_id' => auth()->user()->id, 'message' => $request['message']]);

        return response()->json(['status' => 1, 'message' => trans('success')]);
    }

    /**
     * kick users from room
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function kick(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        if ($request['user_id'] == auth()->user()->id) {
            return response()->json(['status' => 0, 'message' => 'server error'], 500);
        }
        $user = auth()->user();
        $room = Room::find($request['room_id']);
        $room->makeVisible(['identifier']);
        if ($room == null) {
            return response()->json(['status' => 0, 'message' => trans('room not found')], 404);
        }
        if (!check_room_role('kick_user', $request['room_id'])) {
            return response()->json(['status' => 0, 'message' => 'forbidden'], 403);
        }
        RoomUser::where('user_id', $request['user_id'])
            ->where('room_id', $request['room_id'])->delete();
        $kicked_user = User::find($request['user_id']);
        $now = time() + (60 * 60);
        RoomBlacklist::create(['room_id' => $request['room_id'], 'user_id' => $request['user_id'], 'until' => $now]);
        $message_data = get_message_json($user, 'kick', $user->name . ' ' . trans('kicked') . ' ' . $kicked_user->name, $kicked_user);
        publish_message($room->identifier, $message_data);

        return response()->json(['status' => 1, 'message' => trans('success')]);
    }

    /**
     * ban users from room
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ban(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        if ($request['user_id'] == auth()->user()->id) {
            return response()->json(['status' => 0, 'message' => trans('you can\'t ban your self')], 500);
        }
        if (!check_room_role('ban_user', $request['room_id'])) {
            return response()->json(['status' => 0, 'message' => 'forbidden'], 403);
        }
        $check = RoomBlacklist::where(['user_id' => $request['user_id'], 'room_id' => $request['room_id']])->first();
        if ($check != null) {
            return response()->json(['status' => 0, 'message' => trans('already banned')], 500);
        }
        $banned = RoomBlacklist::create(['user_id' => $request['user_id'], 'room_id' => $request['room_id']]);
        $room = Room::find($request['room_id']);
        $room->makeVisible(['identifier']);
        if ($banned) {
            $user = auth()->user();
            RoomUser::where('room_id', $request['room_id'])
                ->where('user_id', $request['user_id'])->delete();
            $banned_user = User::find($request['user_id']);
            $message_data = get_message_json($user, 'ban', $user->name . ' ' . trans('banned') . ' ' . $banned_user->name, $banned_user);
            publish_message($room->identifier, $message_data);

            return response()->json(['status' => 1, 'message' => trans('success')]);
        }

        return response()->json(['status' => 0, 'message' => 'server error'], 500);
    }

    /**
     * get room blacklist
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function blacklist($id)
    {
        if (!check_room_role('view_blacklist', $id)) {
            return response()->json(['status' => 0, 'message' => 'forbidden'], 403);
        }

        $blacklist = RoomBlacklist::with(['user'])->where('room_id', $id)->get();
        $user = auth()->user();

        return response()->json(['status' => 1, 'data' => $blacklist]);
    }

    /**
     * get joined users list
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function users($id)
    {
        $room = Room::find($id);
        if ($room == null) {
            not_found_response('room not found');
        }
        $users = RoomUser::where('room_id', $id)->get();
        $data = [];
        $ids = [];
        foreach ($users as $user) {
            $usr = User::with(['reserved_id'])->find($user->user_id);
            $user_array = $usr->toArray();
            $privilege = RoomUserPrivilege::with(['room_privilege'])->where('room_id', $id)
                ->where('user_id', $user->user_id)->first();

            $user_array['privilege'] = $privilege->room_privilege ?? null;
            array_push($data, ['user' => $user_array]);
        }

        return success_response($data);
    }

    /**
     * filter rooms by country
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function filter($id)
    {
        $rooms = $this->roomRepo->filter($id);

        return success_response($rooms);
    }
}
