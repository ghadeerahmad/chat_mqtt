<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackgroundRequest;
use App\Repositories\Background\BackgroundRepository;

class BackgroundController extends Controller
{
    private BackgroundRepository $backgroundRepo;

    /**
     * constructore
     *
     * @return void
     */
    public function __construct(BackgroundRepository $repo)
    {
        $this->middleware('adminRole:view_backgrounds')->only(['index', 'show']);
        $this->middleware('adminRole:create_backgrounds')->only('create');
        $this->middleware('adminRole:update_backgrounds')->only('update');
        $this->middleware('adminRole:delete_backgrounds')->only('destroy');
        $this->backgroundRepo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->backgroundRepo->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BackgroundRequest $request)
    {
        return $this->backgroundRepo->create($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->backgroundRepo->show($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BackgroundRequest $request, $id)
    {
        return $this->backgroundRepo->update($id, $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->backgroundRepo->destroy($id);
    }

    /**
     * set background as default
     *
     * @param  BackgroundRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function set_default(BackgroundRequest $request)
    {
        return $this->backgroundRepo->set_default($request);
    }
}
