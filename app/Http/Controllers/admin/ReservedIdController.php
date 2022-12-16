<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ReservedId;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservedIdController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminRole:view_id')->only('index');
        $this->middleware('adminRole:create_id')->only('create');
        $this->middleware('adminRole:update_id')->only('update');
        $this->middleware('adminRole:delete_id')->only('delete');
        $this->middleware('adminRole:update_user')->only('assign_to_user');
    }

    /**
     * create new reserved id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illumiante\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|unique:reserved_ids,reserved_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $reserved = ReservedId::create(['reserved_id' => $request['id']]);
        if ($reserved) {
            return response()->json(['status' => 1, 'message' => 'success', 'data' => $reserved]);
        }

        return response()->json(['status' => 0, 'message' => 'error'], 500);
    }

    /**
     * get list of ids
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ids = ReservedId::with(['user'])->get();

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $ids], 200);
    }

    /**
     * update reserved id
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illumiante\Http\Response
     */
    public function update(Request $request, $id)
    {
        $reserved_id = ReservedId::find($id);
        if ($reserved_id == null) {
            return response()->json(['status' => 0, 'message' => 'id not found'], 404);
        }
        if ($reserved_id->status == 'TAKEN') {
            return response()->json(['status' => 0, 'message' => 'this id is already been taken by another user'], 500);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|unique:reserved_ids,reserved_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $reserved_id->update(['reserved_id' => $request['id']]);

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $reserved_id]);
    }

    /**
     * delete an id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $reserved_id = ReservedId::find($id);
        if ($reserved_id == null) {
            return response()->json(['status' => 0, 'message' => 'id not found'], 404);
        }
        if ($reserved_id->status == 'TAKEN') {
            return response()->json(['status' => 0, 'message' => 'this id is already been taken by another user'], 500);
        }
        $reserved_id->delete();

        return response()->json(['status' => 1, 'message' => 'success']);
    }

    /**
     * assign id to user
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign_to_user(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reserved_id' => 'required|exists:reserved_ids,reserved_id',
        ]);
        $reserved_id = ReservedId::where('reserved_id', $request['reserved_id'])->first();
        if ($reserved_id->status == 'AVAILABLE') {
            $user = User::find($request['user_id']);
            $user->reserved_id_id = $reserved_id->id;
            $reserved_id->status = 'TAKEN';
            $user->save();
            $reserved_id->save();

            return success_response();
        } else {
            return forbidden_response('id already assigned to another user');
        }
    }
}
