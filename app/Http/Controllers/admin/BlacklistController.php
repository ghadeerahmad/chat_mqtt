<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blacklist\CreateBlacklistRequest;
use App\Repositories\BlacklistRepository;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    protected $blacklistRepo;

    public function __construct(BlacklistRepository $blacklistRepository)
    {
        $this->blacklistRepo = $blacklistRepository;
        $this->middleware('adminRole:view_blacklist')->only(['index', 'show']);
        $this->middleware('adminRole:create_blacklist')->only('create');
        $this->middleware('adminRole:delete_blacklist')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = $this->blacklistRepo->all();

        return success_response($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateBlacklistRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBlacklistRequest $request)
    {
        $item = $this->blacklistRepo->create($request['user_id'], $request['expire']);
        if ($item) {
            return success_response($item);
        }

        return server_error_response();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = $this->blacklistRepo->show($id);

        return success_response($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
        $this->blacklistRepo->remove($id);

        return success_response();
    }
}
