<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\User;
use App\Models\Card;
use App\Models\UserCard;
use App\Models\Bet;
use App\Models\PublicRanking;
use App\Models\Championship;
use App\Models\Game;
use App\Models\AdminOpt;
use App\Models\Receipt;

use App\Helpers\RequestAPIHelper;
use App\Helpers\GazetaBrasileiraoScrapHelper;

class AdminController extends Controller
{   
    public function __construct() {
        return response()->json(Auth::user());
        if(! Auth::user()->active) {
            return response()->json([
                'status' => 'error',
                'error' => 'Contas desativadas não tem permissão para acessar o sistema como administrador.',
            ]);
        }
    }

    public function nextMatches() {
        $response = RequestAPIHelper::requestAllMatchs();

        $matches = [];
        foreach($response->data as $key => $_match) {
            
            $date = explode('/',explode(' ',$_match->local_date)[0]);
            $date = "$date[2]-$date[0]-$date[1]";
            $time = explode(' ',$_match->local_date)[1];
            
            $timestamp = strtotime("$date $time") - (3600 * 6);
            if(strtotime(date('Y-m-d H:i:s', $timestamp)) >= strtotime('NOW')) {
                $match = [
                    'id' => $_match->id,
                    'away_score' => $_match->away_score,
                    'home_score' => $_match->home_score,
                    'away_name' => $_match->away_team_en,
                    'home_name' => $_match->home_team_en,
                    'away_flag' => $_match->away_flag,
                    'home_flag' => $_match->home_flag,
                    'finished' => $_match->finished,
                    'group' => $_match->group,
                    'datetime' => date('d/m/Y H:i:s', $timestamp) 
                ];
                array_push($matches, $match);
            }
        }
        
        $response = ['status' => 'success'];
        $response['matches'] = $matches;

        return response()->json($response, 200);
    }

    public function createCard(Request $request) {
        $response = ['status' => null];
        $validator = Validator::make($request->all(), [
            'matchs' => 'required|array|min:1',
            'price' => 'required|numeric',
            'type' => 'required|string|in:detailed,common',
            'name' => 'string',
            'championship' => 'required|string',
            'round' => 'numeric|min:1|max:38|max_digits:2',
            'host_percentage' => 'required|numeric|min:0|max:100|max_digits:3',
        ],[
            'matchs.rquired' => 'É necessario adicionar partidas para criar uma cartela.',
            'price.required' => 'A cartela precisa ter um preço.',
            'type.required' => 'É  necessario escolher o tipo da cartela.',
            'type.in' => 'Os valor do tipo deve ser detalhado ou comum.',
            'matchs.min' => 'É necessario no minimo uma partida para criar uma cartela.',
            'price.numeric' => 'O preço precisa ser um número.',
            'championship.required' => 'É necessario um campeonato para registrar cartela.',
            'round.min' => 'A rodada não pode ser menos do que a 1ª',
            'round.max' => 'Não existem rodadas além da 38ª',
            'host_percentage.required' => 'É necessario a porcentagem do organizador.',
            'host_percentage.min' => 'A porcentagem do organizador não pode ser menor do que 0%.',
            'host_percentage.max' => 'A porcentagem do organizador não pode ser maior do que 100%.',
            'host_percentage.max_digits' => 'A  porcentagem deve ser  inserida com no maximo 3 digitos.',
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();
            return response()->json($response, 422);
        }

        if($request->endtime) {
            $endtime = date_create($request->endtime);
            $endtime = date_timestamp_get($endtime);
            if($endtime >= strtotime('now')) {
                $endtime = date('Y-m-d H:i:s', $endtime);

            } else {
                $response['status'] = 'error';
                $response['error'] = ['endtime' => ['A data de encerramento precisa ser depois de agora.']];
                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = ['endtime' => ['É necessario enviar a data e hora de encerramento']];
            return response()->json($response, 422);
        }

        if($validator->validated()['championship'] === 'brasileirao') {
            if(! $validator->validated()['round']) {
                $response['status'] = 'error';
                $response['error'] = ['round' => ['No Brasileirão é necessario inserir a rodada.']];
                return response()->json($response, 422);

            } else {
                $round = $validator->validated()['round'];
            }
        }


        $matchs = '';
        foreach($validator->validated()['matchs'] as $match) {
            $matchs .= "$match,";
        }

        $response['card'] = Card::create([
            'name' => $validator->validated()['name'],
            'matchs' => $matchs,
            'endtime' => $endtime,
            'price' => $validator->validated()['price'],
            'type' => $validator->validated()['type'],
            'championship' => $validator->validated()['championship'],
            'round' => $round ?? null,
            'host_percentage' => $validator->validated()['host_percentage'],
        ]);
        if($response['card']) {
            $response['status'] = 'success';
            return response()->json($response, 200);

        } else {
            $response['error'] = 'Erro interno ao salvar a cartela. Por favor tente novamente.';
            return response()->json($response, 500);
        }
    }

    public function deleteCard($id) {
        $response = ['status' => null];
        $card = Card::find($id);

        if($card) {
            $response['deleted'] = $card->delete();
            if($response['deleted']) {
                $response['status'] = 'success';
                 
                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Erro interno ao tentar deletar. Por favor tente novamente.';

                return response()->json($response, 500);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }
    }

    public function userBets() {
        $response = ['status' => null];

        $user_cards = UserCard::select('*')->orderBy('created_at', 'DESC')->paginate(20);
        if(count($user_cards) > 0) {
           
            foreach($user_cards as $user_card){
                if($user_card->card->championship === 'world-cup') {
                    $matchs = RequestAPIHelper::requestAllMatchs();
                    $_matchs = $matchs->data;
                
                }elseif($user_card->card->championship === 'brasileirao') {
                    $matchs = GazetaBrasileiraoScrapHelper::scrapRound($user_card->card->round);
                    $_matchs = json_decode(json_encode($matchs));

                } elseif(is_numeric($user_card->card->championship)) {
                    $championship = Championship::find($user_card->card->championship);
                    $_matchs = $championship->matchs;
                }
                foreach($user_card->bets as $key => $bet) {
                    foreach($_matchs as $match) {
                        if($bet->match_id == $match->id) {

                            if($user_card->card->championship === 'world-cup') {
                                $datetime = explode(' ',$match->local_date);
                                $date = explode('/', $datetime[0]);
                                $date = "$date[2]-$date[0]-$date[1]";
                                $time = $datetime[1];
    
                                $timestamp = strtotime("$date $time");
    
                                $home_team = $match->home_team_en;
                                $away_team = $match->away_team_en;
                            }
    
                            if($user_card->card->championship === 'brasileirao') {
                                $timestamp = strtotime($match->datetime);
    
                                $home_team = $match->home_name;
                                $away_team = $match->away_name;
                            }
                            
                            if(is_numeric($user_card->card->championship)) {
                                $timestamp = strtotime($match->datetime);
    
                                $home_team = $match->home_name;
                                $away_team = $match->away_name;
                            }

                            $datetime = date('d/m/Y H:i:s', $timestamp);
    
                            $bet->match = [
                                'id' => $match->id,
                                'group' => $match->group,
                                'datetime' => $datetime,
                                'home_score' => $match->home_score,
                                'away_score' => $match->away_score,
                                'home_name' => $home_team,
                                'away_name' => $away_team,
                                'home_flag' => $match->home_flag,
                                'away_flag' => $match->away_flag,
                                'finished' => $match->finished,
                            ];
                        }
                    }
                }
            }
            $response['status'] = 'success';
            $response['user_bets'] = $user_cards;

            return response()->json($response, 200);
            
            
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Ainda não há nenhuma aposta no sistema.';

            return response()->json($response, 200);
        }
    }

    public function updateUserCard($id ,Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'name' => 'string|min:2',
            'phone' => 'string|min:16|max:16',
            'bets' => 'array',
        ], [
            'name.min' => 'O nome precisao mínimo 2 caracteres.',
            'phone.min' => 'Número de celular inválido.',
            'phone.max' => 'Número de celular inválido',
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $data = $validator->validated();

        $card  = UserCard::find($id);
        if($card) {
            if(count($card->bets) === count($data['bets'])) {
                $card->name = $data['name'];
                $card->phone = $data['phone'];
                $card->save();

                if($card->card->type === 'common') {
                    foreach($card->bets as $bet) {
                        foreach($data['bets'] as $_bet) {
                            $_bet = json_decode($_bet);
                            if($bet->id === $_bet->id) {
                                $bet->bet = $_bet->bet;
                                $bet->save();
                            }
                        }
                    }
                }
                
                if($card->card->type === 'detailed') {
                    foreach($card->bets as $bet) {
                        foreach($data['bets'] as $_bet) {
                            $_bet = json_decode($_bet);

                            if($bet->id === $_bet->id) {
                                $bet->home_score = $_bet->home_score;
                                $bet->away_score = $_bet->away_score;
                                $bet->save();
                            }
                        }
                    }
                }
                
                unset($card->bets);
                $card->bets;

                $response['status'] = 'success';
                $response['user_card'] = $card;

                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Número de aposta não coincide com o banco de dados.';

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }

    }

    public function validateCard(Request $request) {
        $response = ['status' => null];

        $card = UserCard::find($request->id);
        if($card) {
            $card->validated = true;
            $response['validated'] = $card->save();
            $response['card'] = $card;
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else{
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }
    }

    public function generateReceipt($id) {
        $user_card = UserCard::where('id', $id)
            ->where('validated', true)
        ->first();

        if($user_card) {
            if($user_card->card->championship === 'world-cup') {
                $matchs = RequestAPIHelper::requestAllMatchs();
                $matchs = $matchs->data;

            } elseif($user_card->card->championship === 'brasileirao') {
                $matchs = GazetaBrasileiraoScrapHelper::scrapRound($user_card->card->round);
                $matchs = json_decode(json_encode($matchs));

            } elseif(is_numeric($user_card->card->championship)) {
                $championship = Championship::find($user_card->card->championship);
                $matchs = $championship->matchs;
            }

            foreach($user_card->bets as $bet) {
                foreach($matchs as $match) {
                    if($bet->match_id === $match->id) {
                        $bet->match = $match;
                    }
                }
            }

            $data['date'] = date('d/m/Y H:i');
            $data['user_card'] = $user_card;

            $bets = json_encode($user_card->bets);

            $receipt = Receipt::where('user_card_id', $user_card->id)
                ->where('code', $user_card->code)
            ->first();
            if($receipt) {
                $receipt->bets = $bets;

            } else {
                $receipt = Receipt::create([
                    'user_card_id' => $user_card->id,
                    'code' => $user_card->code,
                    'bets' => $bets,
                    'name' => $user_card->name,
                    'phone' => $user_card->phone,
                ]);
            }

            $data['signature']['duplicate'] = URL::temporarySignedRoute('receipt.validate', now()->addMonths(4) ,[
                'id' => $receipt->id,
            ]);

            ini_set('max_execution_time', 300);
            $pdf = PDF::loadView('CardReceipt', $data);
            
            return $pdf->download('comprovante.pdf');
        }

        
    }

    public function validateReceipt($id, Request $request) {
        if($request->hasValidSignature()) {
            $receipt = Receipt::find($id);

            if($receipt) {
                $receipt->bets = json_decode($receipt->bets);

                $user_card = UserCard::where('id', $receipt->user_card_id)
                    ->where('validated', true)
                ->first();
                if($user_card) {
                    if($user_card->card->championship === 'world-cup') {
                        $matchs = RequestAPIHelper::requestAllMatchs();
                        $matchs = $matchs->data;
        
                    } elseif($user_card->card->championship === 'brasileirao') {
                        $matchs = GazetaBrasileiraoScrapHelper::scrapRound($user_card->card->round);
                        $matchs = json_decode(json_encode($matchs));
        
                    } elseif(is_numeric($user_card->card->championship)) {
                        $championship = Championship::find($user_card->card->championship);
                        $matchs = $championship->matchs;
                    }

                    foreach($receipt->bets as $bet) {
                        foreach($matchs as $match) {
                            if($bet->match_id === $match->id) {
                                $bet->match = $match;
                            }
                        }
                    }

                    $user_card->bets = $receipt->bets;

                    $data['date'] = date('d/m/Y H:i', strtotime($receipt->created_at));
                    $data['user_card'] = $user_card;

                    $data['signature'] = URL::temporarySignedRoute('receipt.validate', now()->addMonths(4) ,[
                        'id' => $receipt->id,
                    ]);
                    
                    ini_set('max_execution_time', 300);
                    $pdf = PDF::loadView('CardReceipt', $data);
                    
                    return $pdf->download('comprovante.pdf');
                }
            }

        } else {
            return redirect()->route('401');
        }
    }

    public function deleteUserCard($id) {
        $response = ['status' => null];

        $card = UserCard::find($id);
        if($card) {
            $response['deleted'] = $card->delete();
            if($response['deleted']){
                $response['status'] = 'success';

                return response()->json($response, 200);

            }  else {
                $response['status'] = 'error';
                $response['error'] = 'Erro interno ao tentar completar ação. Por tente novamente.';

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);

        }
    }

    public function cardRanking($id, Request $request) {
        $response = ['status' => null];

        $page = $request->page ? intval($request->page) : 1;

        $_card = Card::find($id);
        if($_card) {
            if($_card->championship === 'world-cup') {
                $matchs = RequestAPIHelper::requestAllMatchs();
                $matchs = $matchs->data;

            } elseif($_card->championship === 'brasileirao') {
                $matchs = GazetaBrasileiraoScrapHelper::scrapRound($_card->round);
                $matchs = json_decode(json_encode($matchs));

            } elseif(is_numeric($_card->championship)) {
                $championship = Championship::find($_card->championship);
                $matchs = $championship->matchs;
            }
            
            if($matchs) {
                $user_cards = [];
                foreach($_card->userCards as $card) {
                    if($card->validated == 1) {
                        $card->points = 0;
                        $card->card;
                        foreach($card->bets as $bet) {
                            foreach($matchs as  $_match) {
                                if($bet->match_id == $_match->id){
                                    if($_match->finished === 'TRUE' || $_match->finished === true){
                                        if($_card->type === 'common') {
                                            if($_match->home_score > $_match->away_score){
                                                if($bet->bet === 'victory') {
                                                    $bet->point = true;
                                                    $card->points++;
                                                } else {
                                                    $bet->point = false;
                                                }
                                            }
                                            if($_match->home_score === $_match->away_score){
                                                if($bet->bet === 'draw') {
                                                    $bet->point = true;
                                                    $card->points++;
                                                } else {
                                                    $bet->point = false;
                                                }
                                            }
                                            if($_match->home_score < $_match->away_score){
                                                if($bet->bet === 'loss') {
                                                    $bet->point = true;
                                                    $card->points++;
                                                } else {
                                                    $bet->point = false;
                                                }
                                            }
                                        }

                                        if($_card->type === 'detailed') {
                                            if(
                                                $bet->home_score === $_match->home_score
                                                && $bet->away_score === $_match->away_score
                                            ) {
                                                $bet->point = true;
                                                $card->points++;

                                            } else {
                                                $bet->point = false;
                                            }
                                        }
                                    }
                                    if($_card->championship === 'world-cup') {
                                        $datetime = explode(' ',$_match->local_date);
                                        $date = explode('/', $datetime[0]);
                                        $date = "$date[2]-$date[0]-$date[1]";
                                        $time = $datetime[1];
            
                                        $timestamp = strtotime("$date $time");
            
                                        $home_team = $_match->home_team_en;
                                        $away_team = $_match->away_team_en;
                                    }
            
                                    if($_card->championship === 'brasileirao') {
                                        $timestamp = strtotime($_match->datetime);
            
                                        $home_team = $_match->home_name;
                                        $away_team = $_match->away_name;
                                    }
                                    
                                    if(is_numeric($_card->championship)) {
                                        $timestamp = strtotime($_match->datetime);
            
                                        $home_team = $_match->home_name;
                                        $away_team = $_match->away_name;
                                    }
    
                                    $datetime = date('d/m/Y H:i:s', $timestamp);
    
                                    $bet->match = [
                                        'id' => $_match->id,
                                        'group' => $_match->group,
                                        'datetime' => $datetime,
                                        'home_score' => $_match->home_score,
                                        'away_score' => $_match->away_score,
                                        'home_name' => $home_team,
                                        'away_name' => $away_team,
                                        'home_flag' => $_match->home_flag,
                                        'away_flag' => $_match->away_flag,
                                        'finished' => $_match->finished,
                                    ];
                                }
                            }
                        } 
                        $user_cards[] = $card;
                    }
                     
                }
                foreach($user_cards as $key => $card) {
                    foreach($user_cards as $_key => $c) {
                        if($card->points > $c->points && $key > $_key) {
                            array_splice($user_cards, $_key, 0, array_splice($user_cards, $key, 1));
                        }
                    }
                }


                $award = (($_card->price * count($user_cards)) / 100) * (100 - $_card->host_percentage);

                $winners = [];
                if(count($user_cards) > 0 && $user_cards[0]->points > 0) {
                    $winners[] = $user_cards[0];

                    $first_winner_points = $user_cards[0]->points;

                    for($i=1; $i<count($user_cards); $i++) {
                        if($user_cards[$i]->points === $first_winner_points) {
                            $winners[] = $user_cards[$i];

                        } else {
                            break;
                        }
                    }
                    
                    foreach($winners as $winner) {
                        $winner->award = $award / count($winners);
                    }
                }
                

                $pages_number = ceil(count($user_cards) / 30);

                $offset = $page === 1 ? (($page -1) * 30) : (($page -1 ) * 30) -1;
                $user_cards = array_slice($user_cards, $offset, 30);
            
                $response['pages']['number'] = $pages_number;
                for($l=1; $l==$pages_number; $l++) {
                    $link = ['url' => null, 'active' => null];
                    $link['url'] = url("/admin/card-ranking/$_card->id?page=$l");
                    $link['active'] = $l == $page ? true : false;
                    $response['pages']['links'][] = $link;
                }
                $response['pages']['previous'] = $page == 1 || $pages_number === 1 ? null : url("/admin/card-ranking/$_card->id?page=".$page-1);
                $response['pages']['next'] = $page == $pages_number || $pages_number === 1 ? null : url("/admin/card-ranking/$_card->id?page=".$page+1);

                $response['status'] = 'success';

                $response['ranked_user_cards'] = $user_cards;
                $response['winners'] = $winners;

                return response()->json($response, 200);
            }

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }
    }

    public function publicateRanking($id) {
        $response = ['status' => null];

        $card = Card::find($id);
        if($card) {
            $success = PublicRanking::create([
                'card_id' => $card->id
            ]);
            if($success) {
                $response['status'] = 'success';
                return response()->json($response);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'cartela inexistente.';

            return response()->json($response, 422);
        }
    }

    public function searchUserCard($q) {
        $response = ['status' => null];

        $user_cards = UserCard::where('code', $q)->get();
        if(count($user_cards) === 0) {
            $user_cards = UserCard::where('name', 'like', "%$q%")->get();
        }
        if($user_cards) {
            $matchs = RequestAPIHelper::requestAllMatchs();
            if($matchs) {
                $_matchs = $matchs->data;
                foreach($user_cards as $user_card){
                    $user_card->card;
                    foreach($user_card->bets as $key => $bet) {
                        foreach($_matchs as $match) {
                            if($bet->match_id == $match->id) {

                                $datetime = explode(' ',$match->local_date);
                                $date = explode('/', $datetime[0]);
                                $date = "$date[2]-$date[0]-$date[1]";
                                $time = $datetime[1];
        
                                $timestamp = strtotime("$date $time");
                                $datetime = date('d/m/Y H:i:s', $timestamp);

                                $bet->match = [
                                    'group' => $match->group,
                                    'datetime' => $datetime,
                                    'home_score' => $match->home_score,
                                    'away_score' => $match->away_score,
                                    'home_name' => $match->home_team_en,
                                    'away_name' => $match->away_team_en,
                                    'home_flag' => $match->home_flag,
                                    'away_flag' => $match->away_flag,
                                    'finished' => $match->finished,
                                ];
                            }
                        }
                    }
                }
                $response['status'] = 'success';
                $response['user_cards'] = $user_cards;

                return response()->json($response, 200);
            }
            
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Não há nenhum registro que coincida com a busca.';

            return response()->json($response, 200);
        }
    }

}

