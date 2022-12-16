<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $code)
    {
        if (Auth::check()) {
            $user = auth()->user();
            if ($user->role_id == null) {
                return response(['status' => 0, 'message' => 'forbeddin'], 403);
            }
            $role = Role::with(['permissions'])->find($user->role_id);
            if (count($role->permissions) > 0) {
                foreach ($role->permissions as $permission) {
                    if ($permission->permission->code == $code) {
                        return $next($request);
                    }
                }
            }
        }

        return response(['status' => 0, 'message' => 'forbeddin'], 403);
    }
}
