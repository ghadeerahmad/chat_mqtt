<?php

namespace App\Repositories;

interface BaseContract
{
    public function all();

    public function show($id);
}
