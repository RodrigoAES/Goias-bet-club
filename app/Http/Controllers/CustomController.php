<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\Championship;
use App\Models\Team;
use App\Models\Game;

class CustomController extends Controller
{
    public function __construct() {
        if(! Auth::user()->active) {
            return response()->json([
                'status' => 'error',
                'error' => 'Contas desativadas não tem permissão para acessar o sistema como administrador.',
            ]);
        }
    }

    // Championships
    public function allChampionships() {
        $response = ['status' => null];

        $response['championships'] = Championship::all();
        if($response['championships']) {
            $response['status'] = 'success';
            return response()->json($response, 200);

        } else {
            $request['status'] = 'error';
            $request['error'] = 'Erro interno ao consultar registro. Por favor tente novamente.';

            return response()->json($response, 422);
        }
    }

    public function createChampionship(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ], [
            'name.required' => 'O campeonato precisa ter um nome.'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $response['championship'] = Championship::create($validator->validated());
        if($response['championship']) {
            $response['status'] = 'success';
            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Erro interno ao inserir registro. Por favor tente novamente.';

            return response()->json($response, 500);
        }
    }

    public function deleteChampionship($id) {
        $response = ['status' => null];

        $championship = Championship::find($id);
        if($championship) {
            if($championship->delete()) {
                $response['deleted'] = true;
                $response['status'] = 'success';

                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $resposne['error'] = 'Error interno ao excluir registro. Poor tente novamente.';

                return response()->json($response, 500);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Campeonato inexistente.';

            return response()->json($response, 422);
        }
    }

    // Teams 
    public function allTeams($id) {
        $response = ['status' => null];

        $response['teams'] = Team::select('*')->where('championship_id', $id)->orderBy('created_at', 'DESC')->get();
        if($response['teams']) {
            $response['status'] = 'success';
            
            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Erro interno ao consultar registros. Por favor tente novamente.';

            return response()->json($response, 500);
        }
    }

    public function createTeam(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'flag' => 'image|mimes:jpg,jpeg,png',
            'championship_id' => 'required|numeric',
        ], [
            'name.required' => 'O time precisa ter um nome.',
            'flag.image' => 'A bandeira precisa ser um arquivo de imagem.',
            'flag.mimes' => 'Tipo de arquivo não suportado.',
            'champioship_id.required' => 'É necessario selecionar um campeonato para criar um time.'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $championship = Championship::find($validator->validated()['championship_id']);

        if($championship) {
            $flag = Storage::disk('local')->put('public', $validator->validated()['flag']);
            $flag = explode('/', $flag)[1];
            $flag = url("team/flag/$flag");
            
            $response['team'] = Team::create([
                'name' => $validator->validated()['name'],
                'flag' => $flag,
                'championship_id' => $validator->validated()['championship_id']
            ]);
            if($response['team']) {
                $response['status'] = 'success';

                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Erro interno ao inserir registro. Por favor tente novamente.';

                return response()->json($response, 500);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Campeonato inexistente.';

            return response()->json($response, 422);
        }
    } 

    public function updateTeam($id, Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'name' => 'min:1|string',
            'flag' => 'image|mimes:jpg,jpeg,png'
        ], [
            'name.min' => 'O time precisa ter um nome.',
            'flag.image' => 'A bandeira precisa ser um arquivo de imagem.',
            'flag.mimes' => 'Tipo de arquivo não suportado.'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $team = Team::find($id);
        if($team) {
            $team->name = $validator->validated()['name'];

            if(isset($validator->validated()['flag'])) {
                $flag = Storage::disk('local')->put('public', $validator->validated()['flag']);
                $flag = url(str_replace('public', 'storage', $flag));

                $oldFlag = explode('/', $team->flag)[4];
                Storage::delete("public/$oldFlag");

                $team->flag = $flag;
            } 

            if($team->save()) {
                $response['status'] = 'success';
                $response['team'] = $team;

                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Error interno ao inserir registro. Por favor tente novamente.';
    
                return response()->json($response, 500);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Time não existente';

            return response()->json($response, 422);
        }
    }

    public function deleteTeam($id) {
        $response = ['status' => null];

        $team = Team::find($id);
        if($team) {
            $response['deleted'] = $team->delete();
            if($response['deleted']) {
                $response['status'] = 'success';

                return response()->json($response, 200);

            }else {
                $response['status'] = 'error';
                $response['error'] = 'Error interno ao excluir registro. Por favor tente novamente.';
    
                return response()->json($response, 500);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Time não existente';

            return response()->json($response, 422);
        }
    }

    // Matchs

    public function allMatchs($id) {
        $response = ['status' => null];

        $championship = Championship::find($id);

        if($championship) {
            $response['matchs'] = $championship->matchs;
            if($response['matchs']) {
                $response['status'] = 'success';

                return response()->json($response);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Erro interno ao consultar registro. Por favor tente novamente.';

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Campeonato inexistente.';

            return response()->json($response, 422);
        }
    }

    public function createMatch(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'championship_id' => 'required|numeric',
            'group' => 'required|string',
            'datetime' => 'required|date',
            'finished' => 'required|boolean',
            'home_score' => 'required|numeric',
            'away_score' => 'required|numeric',
            'home_team_id' => 'required|numeric',
            'away_team_id' => 'required|numeric',
        ], [
            'championship_id.required' => 'É necessario selecionar um campeonato para registrar a partida.',
            'group.required' => 'É necessario um grupo/rodada para registrar a partida.',
            'datetime.required' => 'É necessario a data e hora da partida.',
            'finished.required' => 'É necessario a informação da partida já ou não ter terminado.',
            'home_score.required' => 'É necessario o placar do time da casa.',
            'away_score.required' => 'É necessario o placar do time visitante.',
            'home_team_id.required' => 'É necessario escolher o time da casa.',
            'away_team_id.required' => 'É necessario escolher o time visitante.' 
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $data = $validator->validated();

        $data['datetime'] = explode('T', $data['datetime']);
        $data['datetime'] = $data['datetime'][0].' '.$data['datetime'][1].':00';

        $data['finished'] = $data['finished'] === '1' ? true : false;

        if(strtotime($data['datetime']) > strtotime('now')) {
            $home_team = Team::find($data['home_team_id']);
            $away_team = Team::find($data['away_team_id']);

            if($home_team && $away_team) {
                unset($data['home_team_id']);
                unset($data['away_team_id']);

                $data['home_name'] = $home_team->name;
                $data['away_name'] = $away_team->name;
                $data['home_flag'] = $home_team->flag;
                $data['away_flag'] = $away_team->flag;

                $response['match'] = Game::create($data);

                if($response['match']) {
                    $response['status'] = 'success';

                    return response()->json($response, 200);
                
                } else {
                    $response['status'] = 'error';
                    $response['error'] = 'Error interno aoinserir registro. Por favor tente novamente.';

                    return response()->json($response, 500);
                }
            } else {
                $response['status'] = 'error';
                $response['error'] = ['teams' => ['Time inexistente']];

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = ['datetime' => ['A data de termino da partida  precisa ser depois de agora']];

            return response()->json($response, 422);
        }
    }

    public function updateMatch($id ,Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'group' => 'required|string',
            'datetime' => 'required|date',
            'finished' => 'required|boolean',
            'home_score' => 'required|numeric',
            'away_score' => 'required|numeric',
        ], [
            'group.required' => 'É necessario um grupo/rodada para registrar a partida.',
            'datetime.required' => 'É necessario a data e hora da partida.',
            'finished.required' => 'É necessario a informação da partida já ou não ter terminado.',
            'home_score.required' => 'É necessario o placar do time da casa.',
            'away_score.required' => 'É necessario o placar do time visitante.', 
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $match = Game::find($id);
        if($match) {
            $data = $validator->validated();

            $response['updated'] = $match->update($data);
            if($response['updated']) {
                $response['status'] = 'success';
                $response['match'] = $match;

                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Erro interno ao atulizar registro. Por favor tente novamente.';

                return response()->json($response, 500);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Partida inexistente.';

            return response()->json($response, 422);
        }

    }

    public function deleteMatch($id) {
        $response = ['status' => null];
        $match = Game::find($id);

        if($match) {
            $response['deleted'] = $match->delete();
            if($response['deleted']) {
                $response['status'] = 'success';

                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Errro interno a excluir registro. Por favor tente novamente.';

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Partida inexistente.';

            return response()->json($response, 422);
        }
    } 
}
