<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;
use App\Models\Championship;
use App\Models\UserCard;

use App\Helpers\RequestAPIHelper;
use App\Helpers\GazetaBrasileiraoScrapHelper;

class CardController extends Controller
{
    public function allCards() {
        $response = ['status' => null];

        $cards = Card::select('*')->orderBy('created_at', 'DESC')->get();

        $cardsArray = [];
        foreach($cards as $card) {
            if($card->championship === 'world-cup') {
                $allMatchs = RequestAPIHelper::requestAllMatchs();

            } elseif($card->championship === 'brasileirao') {
                $allMatchs = GazetaBrasileiraoScrapHelper::scrapRound($card->round);
                $allMatchs = ['data' => json_decode(json_encode($allMatchs))];
                $allMatchs = json_decode(json_encode($allMatchs));

            } elseif(is_numeric($card->championship)) {
                $championship = Championship::find($card->championship);
                $allMatchs = ['data' => $championship->matchs];
                $allMatchs = json_decode(json_encode($allMatchs));
            }


            $matchs = [];
            foreach(explode(',', $card->matchs) as $matchId) {
                foreach($allMatchs->data as $match) {
                    if($match->id == $matchId) {
                        if($card->championship === 'world-cup') {
                            $datetime = explode(' ',$match->local_date);
                            $date = explode('/', $datetime[0]);
                            $date = "$date[2]-$date[0]-$date[1]";
                            $time = $datetime[1];

                            $timestamp = strtotime("$date $time");

                            $home_team = $match->home_team_en;
                            $away_team = $match->away_team_en;
                        }

                        if($card->championship === 'brasileirao') {
                            $timestamp = strtotime($match->datetime);

                            $home_team = $match->home_name;
                            $away_team = $match->away_name;
                        }

                        if(is_numeric($card->championship)) {
                            $timestamp = strtotime($match->datetime);

                            $home_team = $match->home_name;
                            $away_team = $match->away_name;
                        }
                        
                        $datetime = date('d/m/Y H:i', $timestamp);

                        $matchs[] = [
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

            $validated_cards = UserCard::where('card_id', $card->id)
                ->where('validated', true)
            ->count();

            $card->award = (($card->price * $validated_cards) / 100) * (100 - $card->host_percentage);
            
            $card->validated_user_cards = $validated_cards;
            $card->matchs = $matchs;
            $cardsArray[] = $card;
        }

        $response['status'] = 'success';
        $response['cards'] = $cardsArray;

        return response()->json($response, 200);
    }
}
