<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

Use App\Models\User;
Use App\Models\Attendant;

class AdminAccountController extends Controller
{
    public function __construct() {
        $this->middleware('active');
    }

    public function allUsers() {
        $response = ['status' => null];

        $users = User::all();
        foreach($users as $user) {
            $user->permissions = [
                'payment_permission' => $user->attendant->payment_permission,
                'doubt_permission' => $user->attendant->doubt_permission,
                'validate_permission' => $user->attendant->validate_permission,
            ];
            $user->slug = $user->attendant->slug;

            unset($user->attendant);
        }
        
        $response['users'] = $users;
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
            'phone' => 'required|string|digits:11|unique:users',
            'level' => 'required|string',
            'slug' => 'required|string|unique:attendants',
            'payment_permission' => 'required|boolean',
            'doubt_permission' => 'required|boolean',
            'validate_permission' => 'required|boolean',
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
            'phone.digits' => 'O celular deve ter 11 digitos.',
            'level.required' => ' É necessario escolher o nivel de poder da conta.'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $data = $validator->validated();
    
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->active = 1;
        if($user) {
            $attendant = Attendant::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'slug' => Str::of($data['slug'])->slug('-'),
                'payment_permission' => $data['payment_permission'],
                'doubt_permission' => $data['doubt_permission'],
                'validate_permission' => $data['validate_permission'],
            ]);

            if($attendant) {
                $response['user'] = $user;
                $response['status'] = 'success';
                
                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Erro ao inserir registro no sistema. Por favor tente novamente.';
    
                return response()->json($response, 500);
            }   
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Erro ao inserir registro no sistema. Por favor tente novamente.';

            return response()->json($response, 500);
        }
    }

    public function updateAccount($id, Request $request) {
        $response = ['status' => null];
        $validator = Validator::make($request->all(),[
            'name' => 'string|min:2',
            'email' => 'string|email',
            'phone' => 'string|numeric|max_digits:11',
            'slug' => 'string',
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
            if(isset($data['slug']) && $data['slug'] != '') {
                $attendant = Attendant::where('user_id', $user->id)->first();
                $attendant->slug = Str::of($data['slug'])->slug('-');
                $attendant->save();
            }
            if(isset($data['password']) && $data['password'] != '') {
                $user->password = Hash::make($data['password']);
            }
    
            $response['updated'] = $user->save();
            if($response['updated']) {
                $user->slug = $attendant->slug;
                
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
        
        $user = User::find($id);
        if($user) {
            if($user->level != 'admin') {
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
                $response['error'] = 'A conta principal do sitema não pode ser excluida.';

                return response()->json($response, 422);
                }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Usuario inexistente.';

            return response()->json($response, 422);
        }
    }

    public function activateAccountToggle($id) {
        $user = User::find($id);
        if($user) {
            if($user->level != 'admin') {
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
            } else {
                $response['status'] = 'error';
                $response['error'] = 'A conta pricipal do sistema não pode ser desativada.';

                return response()->json($response, 422);
            }
        }  else {
            $response['status'] = 'error';
            $response['error'] = 'Usuário inexistente.';

            return response()->json($response, 422);
        }       
    }

    public function upadateAccountPermissions($id, Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'payment_permission' => 'required|boolean',
            'doubt_permission' => 'required|boolean',
            'validate_permission' => 'required|boolean',
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $attendant = Attendant::where('user_id', $id)->first();
        if($attendant) {
            $attendant->payment_permission = intval($validator->validated()['payment_permission']);
            $attendant->doubt_permission = intval($validator->validated()['doubt_permission']);
            $attendant->validate_permission = intval($validator->validated()['validate_permission']);

            if($attendant->save()) {
                $response['attendant'] = $attendant;
                $response['status'] = 'success';

                return response()->json($response);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Erro interno ao atualizar registro. Por favor tente novamente.';

                return response()->json($response, 500);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Atendente inexistente.';

            return response()->json($response, 422);
        }
    }
}
