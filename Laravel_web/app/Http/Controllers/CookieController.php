<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
//use App\Http\Requests;
use App\Http\Controllers\Controller;

class CookieController extends Controller {
    public function setCookie(Request $request)
    {
        $minutes = 1;
        $response = new Response('Hello World');
        return $response->withCookie(cookie('name', 'virat', $minutes));
    }

    public function getCookie(Request $request)
    {
        $value = $request->cookie('name');
        if ($value) {
        return response($value);
        }
        return response('Cookie không tồn tại', 404);
    }
}
