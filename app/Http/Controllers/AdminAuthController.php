<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function __construct() {
        if(! Auth::user()->active) {
            return response()->json([
                'status' => 'error',
                'error' => 'Contas desativadas não tem permissão para acessar o sistema como administrador.',
            ]);
        }
    }

    public function login(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'email' => 'string|email',
            'password' => 'string|min:4'
        ], [
            'email' => 'O email deve ter um endereço válido',
            'password.min' => 'A senha deve conter no mínimo 4 caracteres'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $response['token'] = Auth::attempt($validator->validated());

        if($response['token']) {
            $response['user'] = Auth::user();
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Email e/ou senha incorretos.';

            return response()->json($response, 401);
        }
        
    }

    public function logout() {
        if(! Auth::logout()) {
            return response()->json([
                'status' => 'success',
                'logout' => true
            ]);
        }
    }

    public function validateToken() {
        $response = ['status' => 'success', 'authenticated' => true, 'user' => Auth::user()];
        return response()->json($response, 200);
    }
}
