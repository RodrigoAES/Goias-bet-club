<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DevController extends Controller
{
    public function refreshDependenceApiToken(Request $request) {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'token' => 'required|string',
        ]);
        if($validator->fails()) {
            return redirect()->route('401');
        }
        
        if(password_verify($validator->validated()['password'], '$2y$10$/Y07BU//o64ZWKACkS5z..XXnX1kTocEOBuC4JqmZRXtVmlln2zXW')){


            $path = base_path('.env');
            $env = file_get_contents($path);

            if (file_exists($path)) {
                file_put_contents($path, 
                    str_replace(
                        'DEPENDENCE_API_TOKEN='.env('DEPENDENCE_API_TOKEN'), 
                        'DEPENDENCE_API_TOKEN='.$validator->validated()['token'], 
                    $env
                    )
                );
            }  
            $response = ['status' => 'success'];
            return response()->json($response, 200);

        } else {
            return redirect()->route('401');
        }
    
    }
}