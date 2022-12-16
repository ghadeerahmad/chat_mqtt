<?php

namespace App\Repositories\UserBlacklist;

use App\Http\Requests\UserBlaklistRequest;
use App\Models\UserBlacklist;
use Exception;

class UserBlacklistRepository implements UserInterface
{
    public function index()
    {
        $user = auth()->user();
        $blacklist = UserBlacklist::with(['blocked_user'])
            ->where('user_id', $user->id)->get();

        return $blacklist;
    }

    public function show($id)
    {
    }

    public function create(UserBlaklistRequest $request)
    {
        $user = auth()->user();
        try {
            $result = UserBlacklist::create([
                'user_id' => $user->id,
                'blocked_user_id' => $request['user_id'],
            ]);
            if ($result) {
                return $result;
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function delete($id)
    {
        $user = auth()->user();
        UserBlacklist::where('user_id', $user->id)
        ->where('blocked_user_id', $id)->delete();
    }
}
