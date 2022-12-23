<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;
use App\Models\PublicRanking;
use App\Models\Championship;
use App\Models\Game;

use App\Helpers\APIFootballHelper;
use App\Helpers\GazetaBrasileiraoScrapHelper;


class RankingController extends Controller
{   
    public function publicRankingCards() {
        $response = ['status' => null];

        $public_rankings = PublicRanking::all();

        $public_ranking_cards_id = [];

        foreach($public_rankings as $ranking) {
            $public_ranking_cards_id[] = $ranking->card_id;
        }

        $public_ranking_cards = Card::select('*')->whereIn('id', $public_ranking_cards_id);

        dd($public_ranking_cards);


    }

    public function getRanking($id, Request $request) {
        $response = ['status'=> null];

        $ranking = PublicRanking::where('card_id', $id)->first();
        if($ranking) {
            $page = $request->page ? $request->page : 1;

            $card = Card::find($ranking->card_id);
            if($card) {
                $user_cards = [];
                
                foreach($card->userCards as $user_card) {
                    unset($user_card->phone);
                    $user_card->card;
                    $user_card->points = 0;

                    if($user_card->validated) {  
                        foreach($user_card->bets as $bet) {
                            if($bet->match_src === 'API_FOOTBALL') {
                                $match = APIFootballHelper::requestMatch($bet->match_id);
            
                            } else if ($bet->match_src === 'Gazeta_Scrapper') {
                                $match = GazetaBrasileiraoScrapHelper::match($bet->match_round, $bet->match_id);
                                
                            } else if($bet->match_src === 'Custom') {
                                $match = Game::find($bet->match_id);
                            }   
                            
                            if(is_array($match)) {
                                return response()->json(['match' => $match->id]);
                            }
                            if($match->finished){
                                if($card->type === 'common') {
                                    if($match->home_score > $match->away_score){
                                        if($bet->bet === 'victory') {
                                            $bet->point = true;
                                            $user_card->points++;
                                        } else {
                                            $bet->point = false;
                                        }
                                    }
                                    if($match->home_score === $match->away_score){
                                        if($bet->bet === 'draw') {
                                            $bet->point = true;
                                            $user_card->points++;
                                        } else {
                                            $bet->point = false;
                                        }
                                    }
                                    if($match->home_score < $match->away_score){
                                        if($bet->bet === 'loss') {
                                            $bet->point = true;
                                            $user_card->points++;
                                        } else {
                                            $bet->point = false;
                                        }
                                    }
                                }
    
                                if($card->type === 'detailed') {
                                    if(
                                        $bet->home_score === $match->home_score
                                        && $bet->away_score === $match->away_score
                                    ) {
                                        $bet->point = true;
                                        $user_card->points++;
    
                                    } else {
                                        $bet->point = false;
                                    }
                                }
                            } 
    
                            $bet->match = $match;
                        }
                        $user_cards[] = $user_card; 
                    }
                    
                }
                
                foreach($user_cards as $key => $yser_card) {
                    foreach($user_cards as $_key => $_user_card) {
                        if($user_card->points > $_user_card->points && $key > $_key) {
                            array_splice($user_cards, $_key, 0, array_splice($user_cards, $key, 1));
                        }
                    }
                }


                $award = (($card->price * count($user_cards)) / 100) * (100 - $card->host_percentage);

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

                    $link['url'] = url("/admin/card-ranking/$card->id?page=$l");
                    $link['active'] = $l == $page ? true : false;
                    $response['pages']['links'][] = $link;
                }
                $response['pages']['previous'] = $page == 1 || $pages_number === 1 ? null : url("/admin/card-ranking/$card->id?page=".$page-1);
                $response['pages']['next'] = $page == $pages_number || $pages_number === 1 ? null : url("/admin/card-ranking/$card->id?page=".$page+1);

                $response['status'] = 'success';
                
                $response['ranked_user_cards'] = $user_cards;
                $response['winners'] = $winners;

                return response()->json($response, 200);
                

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Cartela inexistente.';

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'message';
            $response['message'] = 'Ainda não foi disponibilizado o ranking para está cartela.';

            return  response()->json($response, 422);
         } 
    }
}
