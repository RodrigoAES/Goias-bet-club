<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Card;
use App\Models\UserCard;
use App\Models\Bet;

class PublicBetController extends Controller
{
    public function createBet(Request $request) {
        $response = ['status' => null];
        $validator = Validator::make($request->all(), [
            'card_id' => 'required|numeric',
            'name' => 'required|string|min:2',
            'phone' => 'required|string|min:16|max:16',
            'bets' => 'required|array'
        ], [
            'name.required' => 'É necessario o nome para cadastrar a aposta',
            'name.min' => 'O nome precisa ter no minimo 2 carateres.',
            'phone.required' => 'É necessario o número de celular para cadastrar a cartela.',
            'phone.min' => 'Número de celular inválido.',
            'phone.max' => 'Número de celular inválido.',
            'bets.required' => 'É necessario preencher as apostas para cadastrar a cartela.',
        ]);
        if($validator->fails()){    
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }
        $data = $validator->validated();
        $card = Card::find($data['card_id']);

        if($card) {
            $endtime = date_create($card->endtime);
            $endtime = date_timestamp_get($endtime);
            
            if($endtime >= strtotime('now')) {
                $card_matchs = explode(',', $card->matchs);
                array_pop($card_matchs);

                if(count($card_matchs) === count($data['bets'])) {
                    foreach($data['bets'] as $key => $bet) {
                        $data['bets'][$key] = json_decode($bet);
                    }
                    
                    foreach($data['bets'] as $bet) {
                        if(array_search($bet->match_id, $card_matchs) === false || !$bet->bet) {
                            $response['status'] = 'error';
                            $response['error'] = 'Cartela inválida ao sistema.';
                            
                            return response()->json($response, 422);
                        }
                    }
                    if($card->type === 'detailed') {
                        foreach($data['bets'] as $bet) {
                            if(!$bet->bet->home_score || !$bet->bet->away_score) {
                                $response['status'] = 'error';
                                $response['error'] = 'Cartela inválida ao sistema.';
                                
                                return response()->json($response, 422);
                            }
                        }
                    }
                    
                    $response['user_card'] = UserCard::create([
                        'card_id' => $card->id,
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'code' => substr(md5(time()), rand(0, 24), 6),
                        'created_at' => date('Y-m-d H:i:s', strtotime('now'))
                    ]);
                    if($response['user_card']) {
                        if($card->type === 'common') {
                            foreach($data['bets'] as $bet) {
                                $bets[] = Bet::create([
                                    'user_card_id' => $response['user_card']->id,
                                    'match_id' => $bet->match_id,
                                    'bet' => $bet->bet,
                                ]);
                            }
                        }

                        if($card->type === 'detailed') {
                            foreach($data['bets'] as $bet) {
                                $bets[] = Bet::create([
                                    'user_card_id' => $response['user_card']->id,
                                    'match_id' => $bet->match_id,
                                    'home_score' => $bet->bet->home_score,
                                    'away_score' => $bet->bet->away_score,
                                ]);
                            }
                        }
                        
                        $response['user_card']->bets = $bets;
                        $response['status'] = 'success';

                        return response()->json($response, 200);

                    } else {
                        $response['status'] = 'error';
                        $response['error'] = 'Errr interno ao criar registro. Por favor tente novamente.';
                        
                        return response()->json($response, 500);
                    }  
                } else {
                    $response['status'] = 'error';
                    $response['error'] = 'É necessario preencher todas as apostas da cartela.';
                    
                    return response()->json($response, 422);
                }
            } else {
                $response['status'] = 'error';
                $response['error'] = 'O prazo para apostas nesta cartela já expirou.';
                
                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inecistente.';
            
            return response()->json($response, 422);
        }
    }
}
