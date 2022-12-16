<?php

namespace App\Repositories\Background;

use App\Http\Requests\BackgroundRequest;

interface BackgroundInterface
{
    public function all();

    public function show($id);

    public function create(BackgroundRequest $request);

    public function update($id, BackgroundRequest $request);

    public function destroy($id);

    public function set_default(BackgroundRequest $request);
}
