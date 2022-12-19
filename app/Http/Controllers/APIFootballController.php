<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\APIFootballHelper;

class APIFootballController extends Controller
{
    public function search(Request $request) {
        if(is_string($request->q)) {
            APIFootballHelper::search($request->q);
        }
    }
}
