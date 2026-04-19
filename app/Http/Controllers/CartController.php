<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    //


    public function add(Request $request)
    {
        return response()->json([
            'message' => 'ok'
        ]);
    }
}
