<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserCard;
use App\Models\Championship;
use App\Models\Game;

use App\Helpers\RequestAPIHelper;
use App\Helpers\GazetaBrasileiraoScrapHelper;
use App\Helpers\APIFootballHelper;

class UserCardController extends Controller 
{
    public function getCard($code) {
        $response = ['status' => null];

        $user_card = UserCard::where('code', $code)->first();
        if($user_card) {
            $user_card->card;
            foreach($user_card->bets as $key => $bet) {
                if($bet->match_src === 'API_FOOTBALL') {
                    $match = APIFootballHelper::requestMatch($bet->match_id);

                } else if ($bet->match_src === 'Gazeta_Scrapper') {
                    $match = GazetaBrasileiraoScrapHelper::match($bet->match_round, $bet->match_id);

                } else if($bet->match_src === 'Custom') {
                    $match = Game::find($bet->match_id);
                }    

                $bet->match = $match;
            }

            $response['status'] = 'success';
            $response['user_card'] = $user_card;

            return response()->json($response, 200); 

        } else  {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }

    }
}
