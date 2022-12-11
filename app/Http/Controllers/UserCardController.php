<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserCard;
use App\Models\Championship;

use App\Helpers\RequestAPIHelper;
use App\Helpers\GazetaBrasileiraoScrapHelper;

class UserCardController extends Controller
{
    public function getCard($code) {
        $response = ['status' => null];

        $user_card = UserCard::where('code', $code)->first();
        if($user_card) {
            if($user_card->card->championship === 'world-cup') {
                $matchs = RequestAPIHelper::requestAllMatchs();

            } elseif($user_card->card->championship === 'brasileirao') {
                $matchs = GazetaBrasileiraoScrapHelper::scrapRound($user_card->card->round);

            } elseif(is_numeric($user_card->card->championship)) {
                $championship = Championship::find($user_card->card->championship);
                $matchs = $championship->matchs;
            }
            
            if($matchs) {
                if($user_card->card->championship === 'world-cup') {
                    $_matchs = $matchs->data;
                }
                if($user_card->card->championship === 'brasileirao') {
                    $_matchs = json_decode(json_encode($matchs));
                }
                if(is_numeric($user_card->card->championship)) {
                    $_matchs = $matchs;
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
                $response['status'] = 'success';
                $response['user_card'] = $user_card;

                return response()->json($response, 200); 
            }

        } else  {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }

    }
}
