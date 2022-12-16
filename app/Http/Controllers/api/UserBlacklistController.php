<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserBlaklistRequest;
use App\Models\UserBlacklist;
use Illuminate\Http\Request;

class UserBlacklistController extends Controller
{
    protected $userBlacklistRepo;

    /**
     * constructor
     *
     * @param    $repository
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $blacklist = UserBlacklist::with(['blocked_user'])
            ->where('user_id', $user->id)->get();
        // $result = $this->userBlacklistRepo->index();
        return success_response($blacklist);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserBlaklistRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserBlaklistRequest $request)
    {
        $user = auth()->user();
        $check = UserBlacklist::where('user_id', $user->id)
            ->where('blocked_user_id', $request['user_id'])->first();
        if ($check != null) {
            return error_response('already blocked');
        }
        $result = UserBlacklist::create([
            'user_id' => $user->id,
            'blocked_user_id' => $request['user_id'],
        ]);
        if ($result) {
            return success_response($result);
        }

        return server_error_response();

        // $result = $this->userBlacklistRepo->create($request);
        // if($result) return success_response($result);
        // return server_error_response();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserBlaklistRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        UserBlacklist::where('user_id', $user->id)
            ->where('blocked_user_id', $id)->delete();
        // $this->userBlacklistRepo->delete($id);
        return success_response();
    }
}
