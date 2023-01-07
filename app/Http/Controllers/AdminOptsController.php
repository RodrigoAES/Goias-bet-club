<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


use App\Models\AdminOpt;

class AdminOptsController extends Controller
{   
    public function __construct() {
        $this->middleware('active');
    }

    public function updateOpts(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|numeric|digits:11',
            'rules' => 'required|array',
            'home_bg' => 'image|mimes:jpg,png',
            'logo' => 'image|mimes:jpg,png',
            'p_color' => ['required', 'regex:/^#(?:[0-9a-f]{3}){1,2}$/i'],
            's_color' => ['required', 'regex:/^#(?:[0-9a-f]{3}){1,2}$/i'],
            'name' => 'required|string',
            'name_color_1' => ['required', 'regex:/^#(?:[0-9a-f]{3}){1,2}$/i'],
            'name_color_2' => ['regex:/^#(?:[0-9a-f]{3}){1,2}$/i'],
            'bonus_bg_image' => 'image|mimes:jps,png',
            'bonus_text_color_1' => ['required', 'regex:/^#(?:[0-9a-f]{3}){1,2}$/i'],
            'bonus_text_color_2' => ['regex:/^#(?:[0-9a-f]{3}){1,2}$/i']
        ], [
            'phone.required' => 'É necessario um número de celular para o atendimento do cliente.',
            'phone.numeric' => 'Número de ceuluar invalido.',
            'phone.digits' => 'O celular deve ter 11 digitos incluido DDD.',
            'rules' => 'É necessario as regras do bolão.',
            'home_bg.image' => 'O fundo da pagina home deve ser uma imagem.',
            'logo.image' => 'A logo do site deve ser uma imagem.',
            'p_color.required' => 'É necessario a cor primaria do site.',
            'p_color.regex' => 'Cor inválida.',
            's_color.required' => 'É necessario a cor secundaria do site.',
            's_color.regex' => 'Cor inválida.',
            'name.required' => 'O site precisa ter um nome/título.',
            'name_color_1.required' => 'O nome/título do site precisa ter uma cor.',
            'name_color_1.regex' => 'Cor inválida.',
            'name_color_2' => 'Cor inválida.',
            'bonus_bg_image.image' => 'O arquivo para o fundo do bonus deve ser uma imagem.',
            'bonus_bg_image.mimes' => 'Extensão de arquivo não suportada (somente jps e png).',
            'bonus_text_color_1' => 'O texto do bonus deve ter pelo menos uma cor.',
            'bonus_text_color_1.regex' => 'Formato de cor inválido (somente hexadecimal).',
            'bonus_text_color_2' => 'Formato de cor inválido (somente hexadecimal).',
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $data = $validator->validated();

        $rules = '';
        foreach($data['rules'] as $rule) {
            $rules .= "$rule|";
        }
        $data['rules'] = $rules;

        if(isset($data['home_bg'])) {
            $data['home_bg'] = Storage::disk('local')->put('Site/Images/HomeBackground', $data['home_bg']);

        } else {
            unset($data['home_bg']);
        }
        
        if(isset($data['logo'])) {
            $data['logo'] = Storage::disk('local')->put('Site/Images/Logo', $data['logo']);

        } else {
            unset($data['logo']);
        }

        if(isset($data['bonus_bg_image'])) {
            $data['bonus_bg_image'] = Storage::disk('local')->put('Site/Images/BonusBackground', $data['bonus_bg_image']);
        } else {
            unset($data['bonus_bg_image']);
        }

        foreach($data as $key => $opt) {
            $config = AdminOpt::where('name', $key)->first();
            if(!$config) {
                return response()->json(['config' => $key]);
            }
            $config->value = $opt;
            $config->save();
            $response['config'][] = $config;
        }
        
        $response['status'] = 'success';
        return response()->json($response, 200);
    }

    
}
