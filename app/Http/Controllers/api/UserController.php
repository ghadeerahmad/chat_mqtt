<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use App\Models\ReservedId;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;
use PhpMqtt\Client\Facades\MQTT;

class UserController extends Controller
{
    /**
     * check user if exists
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function is_exist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_id' => 'nullable',
            'facebook_id' => 'nullable',
        ]);
        if ($request['google_id'] != null) {
            $check = User::where('google_id', $request['google_id'])->first();
            if ($check != null) {
                return response()->json(['status' => 1, 'exists' => true]);
            }

            return response()->json(['status' => 1, 'exists' => false]);
        }
        if ($request['facebook_id'] != null) {
            $check = User::where('facebook_id', $request['facebook_id'])->first();
            if ($check != null) {
                return response()->json(['status' => 1, 'exists' => true]);
            }

            return response()->json(['status' => 1, 'exists' => false]);
        }

        return response()->json(['status' => 1, 'exists' => false]);
    }

    /**
     * login user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'avatar' => 'nullable',
            'facebook_id' => 'nullable',
            'google_id' => 'nullable',
            'password' => 'nullable',
            'token' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'message' => $validator->errors()->first()], 500);
        }
        if ($request['facebook_id'] == null && $request['google_id'] == null) {
            return response()->json(['status' => 0, 'message' => __('auth.failed')], 500);
        }
        $user = null;
        if ($request['facebook_id'] != null) {
            $user = User::where('email', $request['email'])
                ->orWhere('facebook_id', $request['facebook_id'])
                ->first();
        } elseif ($request['google_id'] != null) {
            $user = User::where('email', $request['email'])
                ->orWhere('google_id', $request['google_id'])
                ->first();
        }
        if ($user != null) {
            $check = Blacklist::where('user_id', $user->id)->count();
            if ($check > 0) {
                return forbidden_response('your account is blocked from login');
            }
            if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']], true)) {
                $token = $user->createToken('api_token')->plainTextToken;
                $user = User::find(auth()->user()->id);
                $user_data = $user->toArray();
                $room = Room::where('user_id', $user->id)->where('is_deleted', 'no')->first();
                $user_data['room'] = $room;

                return response()->json(['status' => 1, 'data' => ['token' => $token, 'user' => $user_data, 'identifier' => $user->identifier, 'remember_token' => $user->remember_token]]);
            } else {
                return response()->json(['status' => 0, 'message' => __('auth.failed')], 500);
            }
        }
        $data = [
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'token' => $request['token'],
            'name' => $request['name'],
        ];
        if ($request['avatar'] != null) {
            $data['avatar'] = $request['avatar'];
        }
        if ($request['facebook_id'] != null) {
            $data['facebook_id'] = $request['facebook_id'];
        }
        if ($request['google_id'] != null) {
            $data['google_id'] = $request['google_id'];
        }
        $identifier = Random::generate(30);
        $check = User::where('identifier', $identifier)->first();
        while ($check != null) {
            $identifier = Random::generate(30);
            $check = User::where('identifier', $identifier)->first();
        }
        $unique_id = Random::generate(8, '0-9');
        while ($unique_id < 10000000) {
            $unique_id = Random::generate(8, '0-9');
        }
        $check = User::where('userId', $unique_id)->first();
        while ($check != null) {
            $unique_id = Random::generate(8, '0-9');
            while ($unique_id < 10000000) {
                $unique_id = Random::generate(8, '0-9');
            }
            $check = User::where('userId', $unique_id)->first();
        }
        $check = ReservedId::where('reserved_id', $unique_id)->first();
        while ($check != null) {
            $unique_id = Random::generate(8, '0-9');
            while ($unique_id < 10000000) {
                $unique_id = Random::generate(8, '0-9');
            }
            $check = ReservedId::where('reserved_id', $unique_id)->first();
        }
        $data['userId'] = $unique_id;
        $data['identifier'] = $identifier;
        $user = User::create($data);
        if ($user) {
            Auth::login($user, true);
            $token = $user->createToken('api_token')->plainTextToken;
            $user = User::with(['room'])->find($user->id);

            return response()->json(['status' => 1, 'data' => ['token' => $token, 'user' => $user, 'identifier' => $user->identifier, 'remember_token' => $user->remember_token]]);
        }

        return response()->json(['status' => 0, 'server error'], 500);
    }

    /**
     * login with remember token
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login_remember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remember_token' => 'required|exists:users,remember_token',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $user = User::where('remember_token', $request['remember_token'])->first();
        if ($user != null) {
            $user_data = $user->toArray();
            $room = Room::where('user_id', $user->id)->where('is_deleted', 'no')->first();
            $user_data['room'] = $room;
            $token = $user->createToken('api_token')->plainTextToken;

            return success_response(['user' => $user_data, 'token' => $token]);
        }

        return server_error_response();
    }

    /**
     * get user profile
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile($id)
    {
        $user = User::with(['reserved_id'])->find($id);
        if ($user == null) {
            return response()->json(['status' => 0, 'message' => 'not found'], 404);
        }
        $data = $user->toArray();
        $user_id = null;
        if ($user->reserved_id != null) {
            $user_id = $user->reserved_id->reserved_id;
        }
        if ($user_id != null) {
            $data['userId'] = $user_id;
        }
        $room = Room::where('user_id', $user->id)
            ->where('is_deleted', 'no')
            ->first();
        $data['room'] = $room;

        return response()->json(['status' => 1, 'data' => $data]);
    }

    /**
     * get my profile
     *
     * @return \Illuminate\Http\Response
     */
    public function my_profile()
    {
        $user = User::with(['reserved_id'])->find(auth()->user()->id);
        $data = $user->toArray();
        $user_id = null;
        if ($user->reserved_id != null) {
            $user_id = $user->reserved_id->reserved_id;
        }
        if ($user_id != null) {
            $data['userId'] = $user_id;
        }
        $room = Room::where('user_id', $user->id)
            ->where('is_deleted', 'no')
            ->first();
        $data['room'] = $room;

        return response()->json(['status' => 1, 'data' => $data]);
    }

    /**
     * logout
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['status' => 1, 'message' => 'success']);
    }

    /**
     * update user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'avatar' => 'nullable|mimes:png,jpg,gif|max:2048',
            'local' => 'nullable|in:en,ar'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $user = User::find(auth()->user()->id);
        $user->name = $request['name'];
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('users', $fileName, 'public');
            $user->avatar = $path;
        }
        if ($request['local'] != null) $user->local = $request['local'];
        $user->save();

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $user]);
    }

    public function test()
    {
        $mqtt = MQTT::publish('/test', 'hello world');
    }
}
