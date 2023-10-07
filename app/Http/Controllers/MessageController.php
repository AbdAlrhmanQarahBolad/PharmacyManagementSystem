<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function create(User $u1,User $u2)
    {
        $user = request()->user();
        if($user->id != $u1->id)
        return response()->json(['message' => 'unauthorized'], 400);
        if(request('body'))
        return ['message'=>Message::create(['from'=>$u1->id,'to'=>$u2->id,'body'=>request('body')])];
    }
    public function get(User $u1,User $u2)
    {
        $user = request()->user();
        if(($user->id != $u1->id) && ($user->id != $u2->id))
        return response()->json(['message' => 'unauthorized'], 400);
        return['messages'=>Message::filter($u1->id,$u2->id)->get()];
    }
}
