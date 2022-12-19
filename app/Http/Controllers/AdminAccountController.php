<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

Use App\Models\User;

class AdminAccountController extends Controller
{
    public function __construct() {
        if(! Auth::user()->active) {
            return response()->json([
                'status' => 'error',
                'error' => 'Contas desativadas não tem permissão para acessar o sistema como administrador.',
            ]);
        }
    }

    public function allUsers() {
        $response = ['status' => null];

        $response['users'] = User::paginate(20);
        if(count($response['users']) > 0) {
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Nenhum usuário registrado no sistema.';

            return response()->json($response, 200);
        }
    }


    public function getUser($id) {
        $response = ['status' => 'success'];
        $response['user'] = Auth::user();

        return response()->json($response, 200);
    }

    public function createUser(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|digits:11'
        ], [
            'name.required' => 'É necessario o nome para cadastro.',
            'name.min' => 'O nome precisa ter no mínimo 2 caracteres.',
            'email.required' => 'É necessario um endereço de email para cadastro.',
            'email.email' => 'O email precisa ser um endereço de email válido.',
            'email.unique' => 'Email já está sendo utilizado por outra conta.',
            'password.required' => 'É necessario uma senha para cadastro.',
            'password.min' =>  'A senha necessita ter no mínimo 8 caracteres.',
            'password.confirmed' => 'Senhas não coincidem.',
            'phone.required' => 'É necessario numero de celular para cadastrar a conta.',
            'phone.digits' => 'O celular deve ter 11 digitos.'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);

        $response['user'] = User::create($data);
        if($response['user']) {
            $response['status'] = 'success';
            
            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Erro ao inserir registro no sistema. Por favor tente novamente.';

            return response()->json($response, 422);
        }
    }

    public function updateAccount($id, Request $request) {
        $response = ['status' => null];
        $validator = Validator::make($request->all(),[
            'name' => 'string|min:2',
            'email' => 'string|email',
            'phone' => 'string|numeric|max_digits:11',
            'password' => 'string|min:4|confirmed',
            'current_password' => 'required|string',
        ], [
            'name.min' => 'O nome precisa ter no mínimo 2 caracteres.',
            'email.email' => 'O email precisa ser um endereço de email válido.',
            'phone.numeric' => 'Número de  telefone inválido',
            'phone.max_digits' => 'Número de telefone inválido. (11 digitos sem espaços ou caracteres especiais)',
            'password.min' => 'A senha necessita de no mínimo 4 caracteres.',
            'password.confirmed' => 'Senhas não coincidem.',
            'current_password.required' => 'É necessario a senha atual para trocar as informações da conta.'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();
            
            return response()->json($response, 422);
        }
        $data = $validator->validated();

        $user = User::find($id);

        if(password_verify($data['current_password'], $user->password)) {
            if(isset($data['name']) && $data['name'] != '') {
                $user->name = $data['name'];
            }
            if(isset($data['email']) && $data['email'] != '') {
                $user->email = $data['email'];
            }
            if(isset($data['phone']) && $data['phone'] != '') {
                $user->phone = $data['phone'];
            }
            if(isset($data['password']) && $data['password'] != '') {
                $user->password = Hash::make($data['password']);
            }
    
            $response['updated'] = $user->save();
            if($response['updated']) {
                $response['status'] = 'success';
                $response['user'] =  $user;
    
                return response()->json($response, 200);
    
            } else {
                $response['status'] = 'error';
                $response['error'] = 'Error interno ao inserir registro. Por favor tente novamente.';
                
                return response()->json($response, 500);
            }

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Senha incorreta.';
            
            return response()->json($response, 401);
        }

        
    }

    public function deleteUser($id) {
        $response = ['status' => null];

        if(Auth::id() === 1 || Auth::id() == $id) {
            if(intval($id) != 1) {
                $user = User::find($id);
                if($user) {
                    $response['deleted'] = $user->delete();
                    if($response['deleted']) {
                        $response['status'] = 'success';

                        return response()->json($response, 200);

                    } else {
                        $response['status'] = 'error';
                        $response['error'] = 'Erro interno a tentar excluir registro. Por favor tente novamente.';

                        return response()->json($response, 500);
                    }

                } else {
                    $response['status'] = 'error';
                    $response['error'] = 'Usuario inexistente.';

                    return response()->json($response, 422);
                }
            } else {
                $response['status'] = 'error';
                $response['error'] = 'A conta pricipal do sistema não pode ser excluida.';

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Não autorizado a excluir está conta.';

            return response()->json($response, 401);
        }
    }

    public function activateAccountToggle($id) {
        if(Auth::id() === 1 || Auth::id() == $id) {
            if(intval($id) != 1) {
                $user = User::find($id);

                if($user) {
                    $user->active = $user->active ? false : true;
                    $response['active'] = $user->active;

                    if($user->save()) {
                        $response['status'] = 'success';

                        return response()->json($response, 200);

                    } else {
                        $response['status'] = 'error';
                        $response['error'] = 'Erro interno a tentar modificar registro. Por favor tente novamente.';

                        return response()->json($response, 500);
                    }
                }  else {
                    $response['status'] = 'error';
                    $response['error'] = 'Usuário inexistente.';

                    return response()->json($response, 422);
                }
            } else {
                $response['status'] = 'error';
                $response['error'] = 'A conta pricipal do sistema não pode ser desativada.';

                return response()->json($response, 401);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Não autorizado a modificar a atividade desta conta.';

            return response()->json($response, 401);
        }
    }
}
