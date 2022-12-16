<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\RoomUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $chats = Chat::where('sender_id', $user->id)
            ->orWhere('reciever_id', $user->id)->get();
        $data = [];
        foreach ($chats as $chat) {
            $chat_user = null;
            if ($chat->sender_id != $user->id) {
                $chat_user = User::find($chat->sender_id);
            } else {
                $chat_user = User::find($chat->reciever_id);
            }
            $room = RoomUser::with(['room'])->where('user_id', $chat_user->id)->first();
            $last_message = Message::where('chat_id', $chat->id)->first();
            if ($last_message != null) {
                array_push($data, [
                    'chat' => $chat,
                    'user' => $chat_user,
                    'last_message' => $last_message,
                    'room' => $room != null ? $room->room : null
                ]);
            }
        }

        return success_response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        if (chat_exists($request['user_id'])) {
            $chat = get_chat($request['user_id']);
            $messages = [];
            $user = auth()->user();
            if ($chat->sender_id == $user->id) {
                $messages = Message::with(['user'])
                    ->NotDeletedBySender($chat->id)
                    ->get();
            } else {
                $messages = Message::with(['user'])
                    ->NotDeletedByReciever($chat->id)
                    ->get();
            }
            $data = [
                'chat' => $chat,
                'messages' => $messages,
            ];

            return success_response($data);
        }
        $identifier = create_identifier();
        $chat = Chat::create([
            'sender_id' => auth()->user()->id,
            'reciever_id' => $request['user_id'],
            'identifier' => $identifier,
        ]);
        if ($chat) {
            $data = [
                'chat' => $chat,
                'messages' => [],
            ];

            return success_response($data);
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
        $chat = Chat::find($id);
        if ($chat == null) return not_found_response(trans('chat not found'));
        $messages = [];
        $user = auth()->user();
        if ($chat->sender_id == $user->id) {
            $messages = Message::with(['user'])
                ->NotDeletedBySender($chat->id)
                ->get();
        } else {
            $messages = Message::with(['user'])
                ->NotDeletedByReciever($chat->id)
                ->get();
        }
        $reciever = null;
        $user = auth()->user();
        if ($chat->sender_id != $user->id) {
            $reciever = User::find($chat->sender_id);
        } else {
            $reciever = User::find($chat->reciever_id);
        }

        return success_response([
            'chat' => $chat,
            'user' => $reciever,
            'messages' => $messages,
        ]);
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
        //
    }
}
