<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;
use App\Models\Championship;
use App\Models\UserCard;
use App\Models\Game;

use App\Helpers\RequestAPIHelper;
use App\Helpers\GazetaBrasileiraoScrapHelper;
use App\Helpers\APIFootballHelper;

class CardController extends Controller
{
    public function allCards() {
        $response = ['status' => null];

        $cards = Card::select('*')->orderBy('created_at', 'DESC')->get();

        $cardsArray = [];
        foreach($cards as $card) {
            $matchs = [];
            $card_matchs = explode(';', $card->matchs);
            array_pop($card_matchs);

            foreach($card_matchs as $matchSource) {
                $matchSource = json_decode($matchSource);

                if($matchSource->src === 'API_FOOTBALL') {
                    $match = APIFootballHelper::requestMatch($matchSource->id);
                } else if ($matchSource->src === 'Gazeta_Scrapper') {
                    $match = GazetaBrasileiraoScrapHelper::match($matchSource->round, $matchSource->id);
                } else if($matchSource->src === 'Custom') {
                    $match = Game::find($matchSource->id);
                }

                $matchs[] = $match;
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
