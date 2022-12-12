<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{

    public function login(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'user' => 'required|string',
            'password' => 'string|min:4'
        ], [
            'user.required' => 'É necessario celular ou endereço de email para fazer login',
            'password.min' => 'A senha deve conter no mínimo 4 caracteres'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }
        $data = $validator->validated();

        if(is_numeric($data['user'])) {
            if(strlen($data['user']) === 11) {
                $response['token'] = Auth::attempt([
                    'phone' => $validator->validated()['user'],
                    'password' => $validator->validated()['password'],
                ]);

            } else {
                $response['status'] = 'error';
                $response['error'] = ['user' => ['Número de telefone inválido.']];

                return response()->json($response, 422);
            }

        } elseif(! is_numeric($data['user'])) {
            if(filter_var($data['user'], FILTER_VALIDATE_EMAIL)) {
                $response['token'] = Auth::attempt([
                    'email' => $validator->validated()['user'],
                    'password' => $validator->validated()['password'],
                ]);

            } else {
                $response['status'] = 'error';
                $response['error'] = ['user' => ['Endereço de email inválido.']];

                return response()->json($response, 422);
            }
        }

        if($response['token']) {
            $response['user'] = Auth::user();
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Telefone/Email e/ou senha incorretos.';

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

    public function username()
    {
        return 'phone';
    }
}
