<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * login admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['status' => 0, 'message' => $validator->errors()->first()]);
        }
        $data = [
            'email' => $request['email'],
            'password' => $request['password'],
        ];
        if (Auth::attempt($data)) {
            $user = auth()->user();
            if ($user->role_id == null) {
                return response()->json(['status' => 0, 'message' => 'forbeddin'], 403);
            }

            $user = User::with(['role'])->find($user->id);
            $token = $user->createToken('api_token')->plainTextToken;
            $permissions = RolePermission::with(['permission'])->where('role_id', $user->role_id)->get();

            return response(['status' => 1, 'token' => $token, 'user' => $user, 'permissions' => $permissions]);
        }

        return response()->json(['status' => 0, 'message' => __('auth.failed')]);
    }
}
