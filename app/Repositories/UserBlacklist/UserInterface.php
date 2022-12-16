<?php

namespace App\Repositories\UserBlacklist;

use App\Http\Requests\UserBlaklistRequest;

interface UserInterface
{
    /**
     * get my black list
     */
    public function index();

    public function show($id);

    /**
     * add user to my blacklist
     *
     * @param  UserBlaklistRequest  $request
     */
    public function create(UserBlaklistRequest $request);

    /**
     * remove user from blacklist
     *
     * @param  int  $id
     */
    public function delete($id);
}
