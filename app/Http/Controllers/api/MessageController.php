<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $user = auth()->user();
        $chat = Chat::where('id', $request['chat_id'])
            ->where('sender_id', $user->id)
            ->orWhere('reciever_id', $user->id)
            ->first();
        if ($chat == null) {
            return not_found_response('chat not found');
        }
        $messages = [];
        if ($chat->sender_id == $user->id) {
            $messages = Message::with(['user'])
                ->NotDeletedBySender($chat->id)
                ->get();
        } else {
            $messages = Message::with(['user'])
                ->NotDeletedByReciever($chat->id)
                ->get();
        }

        return success_response($messages);
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
            'message' => 'required',
            'reciever_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $user = auth()->user();
        $chat = Chat::where('sender_id', $user->id)
            ->where('reciever_id', $request['reciever_id'])->first();
        if ($chat == null) {
            $chat = Chat::where('reciever_id', $user->id)
                ->where('sender_id', $request['reciever_id'])->first();
        }
        if ($chat == null) {
            $identifier = Random::generate(30);
            $check = Chat::where('identifier', $identifier)->first();
            while ($check != null) {
                $identifier = Random::generate(30);
                $check = Chat::where('identifier', $identifier)->first();
            }
            $chat = Chat::create([
                'sender_id' => $user->id,
                'reciever_id' => $request['reciever_id'],
                'identifier' => $identifier,
            ]);
            if ($chat) {
                $message = Message::create([
                    'user_id' => $user->id,
                    'message' => $request['message'],
                    'chat_id' => $chat->id,
                ]);
                if ($message) {
                    $json = get_message_json(auth()->user(), 'message', $message->message);
                    publish_message($chat->identifier, $json);

                    return success_response($message);
                }

                return server_error_response();
            }
        } else {
            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'message' => $request['message'],
            ]);
            if ($message) {
                $json = get_private_message_json(auth()->user(), 'message', $message);
                publish_message($chat->identifier, $json);

                return success_response($message);
            }

            return server_error_response();
        }
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
        $message = Message::with(['chat'])->where('id', $id)
            ->where('user_id', auth()->user()->id)->first();
        $chat = $message->chat;
        if ($chat->sender_id == auth()->user()->id) {
            $message->delete_sender = 1;
        } else {
            $message->delete_reciever = 1;
        }
        $message->save();

        return success_response();
    }

    /**
     * bulk delete messages
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulk_delete(Request $request)
    {
        $request->validate(['messages' => 'required|array']);
        $messages = Message::with(['chat'])->whereIn('id', $request['messages'])->get();
        foreach ($messages as $message) {
            $chat = $message->chat;
            if ($chat->sender_id == auth()->user()->id) {
                $message->delete_sender = 1;
            } else {
                $message->delete_reciever = 1;
            }
            $message->save();
        }

        return success_response();
    }
}
