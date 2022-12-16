<?php

namespace App\Http\Middleware;

use App\Models\RoomPrivilege;
use App\Models\RoomUserPrivilege;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $code, $id)
    {
        if (Auth::check()) {
            $user = auth()->user();
            $room_user_privilege = RoomUserPrivilege::where('user_id', $user->id)
            ->where('room_id', $id)
            ->first();
            if ($room_user_privilege == null) {
                return response(['status' => 0, 'message' => 'forbeddin'], 403);
            }
            $room_privielge = RoomPrivilege::with(['room_roles'])->where('room_privilege_id', $room_user_privilege->privilege_id)
            ->where('room_privilege_id', $room_user_privilege->privilege_id)->first();
            $roles = $room_privielge->room_roles;
            foreach ($roles as $role) {
                if ($role->room_role->code == $code) {
                    return $next($request);
                }
            }
        }

        return response(['status' => 0, 'message' => 'forbeddin'], 403);
    }
}
