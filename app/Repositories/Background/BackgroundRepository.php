<?php

namespace App\Repositories\Background;

use App\Http\Requests\BackgroundRequest;
use App\Models\Background;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Storage;

class BackgroundRepository implements BackgroundInterface
{
    use FileTrait;

    public function all()
    {
        $data = Background::all();

        return success_response($data);
    }

    public function show($id)
    {
        $background = Background::find($id);
        if ($background == null) {
            return not_found_response('background not found');
        }

        return success_response($background);
    }

    public function create(BackgroundRequest $request)
    {
        $data = [];
        if ($request['price'] != null) {
            $data['price'] = $request['price'];
        }
        if ($request['is_default'] != null) {
            $data['is_default'] = $request['is_default'];
        }
        if ($request->hasFile('file')) {
            $data['file'] = $this->upload($request->file('file'), 'backgrounds');
        }
        $background = Background::create($data);
        if ($background) {
            return success_response($background);
        }

        return server_error_response();
    }

    public function update($id, BackgroundRequest $request)
    {
        $data = [];
        if ($request['price'] != null) {
            $data['price'] = $request['price'];
        }
        if ($request['is_default'] != null) {
            $data['is_default'] = $request['is_default'];
        }
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('backgrounds', $fileName, 'public');
            $data['file'] = $path;
        }
        $background = Background::find($id);
        if ($background != null) {
            if ($background->file != null) {
                Storage::disk('public')->delete($background->file);
            }
            $background->update($data);

            return success_response($background);
        }

        return not_found_response('background not found');
    }

    public function destroy($id)
    {
        $background = Background::find($id);
        if ($background == null) {
            return not_found_response('background not found');
        }
        if ($background->file != null) {
            Storage::disk('public')->delete($background->file);
        }
        $background->delete();

        return success_response();
    }

    /**
     * set background as default
     */
    public function set_default(BackgroundRequest $request)
    {
        //if ($request['is_default'] == null) return error_response('is default field required');
        if ($request['background_id'] == null) {
            return error_response('background id field is required');
        }
        $background = Background::find($request['background_id']);
        $background->is_default = '1';
        $default = Background::where('is_default', 1)->update(['is_default' => '0']);
        $background->save();

        return success_response();
    }
}
