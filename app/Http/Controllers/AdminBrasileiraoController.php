<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Helpers\GazetaBrasileiraoScrapHelper;

class AdminBrasileiraoController extends Controller
{
    public function __construct() {
        if(! Auth::user()->active) {
            return response()->json([
                'status' => 'error',
                'error' => 'Contas desativadas não tem permissão para acessar o sistema como administrador.',
            ]);
        }
    }

    public function round($round){
        $response = ['status' => null];
        $response['round']['id'] = intval($round);

        $round = GazetaBrasileiraoScrapHelper::scrapRound($round);
        
        if($round) {
            foreach($round as $match) {
                $datetime = explode(' ', $match['datetime']);
                $date = explode('-', $datetime[0]);
                $date = "$date[2]/$date[1]/$date[0]";
                $time = explode(':', $datetime[1]);
                $time = "$time[0]:$time[1]";
    
                $match['datetime'] = "$date $time";
            }

            $response['status'] = 'success';
            $response['round']['matchs'] = $round;

            return response()->json($response, 200);


        } else {
            $response['status'] = 'error';
            $response['error'] = 'Ocorreu algum erro no sistema. Por favor solicite assistencia técnica.';

            return response()->json($response, 500);
        } 
    }

    public function scrapFlag($file) {
        GazetaBrasileiraoScrapHelper::requestFlag($file);
    }
}
