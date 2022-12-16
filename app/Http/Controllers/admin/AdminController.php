<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ReservedId;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminRole:view_admin')->only('index');
        $this->middleware('adminRole:create_admin')->only('create');
        $this->middleware('adminRole:update_admin')->only('update');
        $this->middleware('adminRole:delete_admin')->only('delete');
    }

    /**
     * get admins list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('role_id', '!=', null)->get();

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $users]);
    }

    /**
     * get admin details by id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admin = User::where('id', $id)
        ->where('role_id', '!=', null)->first();
        if ($admin == null) {
            return response()->json(['status' => 0, 'message' => 'admin not found']);
        }

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $admin]);
    }

    /**
     * create new admin for dashboard
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|unique:users,email|max:255',
            'password' => 'required',
            'role_id' => 'required|exists:roles,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role_id' => $request['role_id'],
        ];
        $identifier = Random::generate(30);
        $check = User::where('identifier', $identifier)->first();
        while ($check != null) {
            $identifier = Random::generate(30);
            $check = User::where('identifier', $identifier)->first();
        }
        $unique_id = Random::generate(8, '0-9');
        $check = User::where('userId', $unique_id)->first();
        while ($check != null) {
            $unique_id = Random::generate(8, '0-9');
            $check = User::where('userId', $unique_id)->first();
        }
        $check = ReservedId::where('reserved_id', $unique_id)->first();
        while ($check != null) {
            $unique_id = Random::generate(8, '0-9');
            $check = ReservedId::where('reserved_id', $unique_id)->first();
        }
        $data['identifier'] = $identifier;
        $data['userId'] = $unique_id;
        $user = User::create($data);
        if ($user) {
            return response()->json(['status' => 1, 'message' => 'success', 'data' => $user]);
        }

        return response()->json(['status' => 0, 'message' => 'server error'], 500);
    }

    /**
     * update admin
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user == null) {
            return response()->json(['status' => 0, 'message' => 'user not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|max:50',
            'password' => 'nullable',
            'role_id' => 'nullable|exists:roles,id',

        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $data = [];
        if ($request['name'] != null) {
            $data['name'] = $request['name'];
        }
        if ($request['password'] != null) {
            $data['password'] = Hash::make($request['password']);
        }
        if ($request['role_id'] != null) {
            $data['role_id'] = $request['role_id'];
        }
        $user->update($data);

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $user]);
    }

    /**
     * delete admin
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $admin = User::find($id);
        $admin->delete();

        return response()->json(['status' => 1, 'message' => 'success']);
    }
}
