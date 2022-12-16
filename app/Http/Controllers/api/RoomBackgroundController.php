<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomBackgroundRequest;
use App\Repositories\RoomBackground\RoomBackgroundRepository;
use App\Traits\RoomTrait;

class RoomBackgroundController extends Controller
{
    use RoomTrait;

    protected RoomBackgroundRepository $room_background_repo;

    /**
     * constructor
     *
     * @return void
     */
    public function __construct(RoomBackgroundRepository $repo)
    {
        $this->room_background_repo = $repo;
    }

    /**
     * get all backgrounds
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->room_background_repo->index();
    }

    /**
     * select a background
     *
     * @param  RoomBackgroundRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function select(RoomBackgroundRequest $request)
    {
        if ($this->isAdmin($request['room_id'])) {
            return $this->room_background_repo->select($request);
        }

        return forbidden_response('forbidden');
    }
}
