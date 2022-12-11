<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;
use App\Models\PublicRanking;
use App\Models\Championship;

use App\Helpers\RequestAPIHelper;
use App\Helpers\GazetaBrasileiraoScrapHelper;


class RankingController extends Controller
{
    public function getRanking($id, Request $request) {
        $response = ['status'=> null];

        $ranking = PublicRanking::where('card_id', $id);
        if($ranking) {
            $page = $request->page ? $request->page : 1;

            $_card = Card::find($id);
            if($_card) {
                if($_card->championship === 'world-cup') {
                    $matchs = RequestAPIHelper::requestAllMatchs();
                    $matchs = $matchs->data;

                }else if($_card->championship === 'brasileirao') {
                    $matchs = GazetaBrasileiraoScrapHelper::scrapRound($_card->round);
                    $matchs = json_decode(json_encode($matchs));

                } else if(is_numeric($_card->championship)) {
                    $championship = Championship::find($_card->championship);
                    $matchs = $championship->matchs;
                }


                if($matchs) {
                    $user_cards = [];
                    foreach($_card->userCards as $card) {
                        unset($card->phone);
                        $card->points = 0;
                        if($card->validated == true) {
                            foreach($card->bets as $bet) {
                                $card->card;
                                foreach($matchs as  $_match) {  
                                    if($bet->match_id == $_match->id) {
                                        if($_match->finished === 'TRUE' || $_match->finished === true) {
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
        } else {
            $response['status'] = 'error.';
            $response['error'] = 'Ranking não existente ou não autorizado para vizualização';

            return  response()->json($response, 422);
         } 
    }
}
