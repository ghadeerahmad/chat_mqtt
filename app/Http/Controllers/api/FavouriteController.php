<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Favourite\CreateFavouriteRequest;
use App\Repositories\FavouriteRepository;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    protected $favRepo;

    public function __construct(FavouriteRepository $favouriteRepo)
    {
        $this->favRepo = $favouriteRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return success_response($this->favRepo->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateFavouriteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFavouriteRequest $request)
    {
        $fav = $this->favRepo->create($request);
        if ($fav) {
            return success_response();
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
        //
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
        $this->favRepo->delete($id);

        return success_response();
    }
}
