<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageCreatedEvent;

class MessageController extends Controller
{
    public function store(Request $request)
    {

        $message = $request->message;
        event(new MessageCreatedEvent($message));
        return $message;
    }
}
