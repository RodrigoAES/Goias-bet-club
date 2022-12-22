<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\APIFootballHelper;

class APIFootballController extends Controller
{
    public function search(Request $request, $current = 'true') {
        $response = ['status' => null];
        if(is_string($request->q)) {
            $response['results'] = APIFootballHelper::search($request->q, $current);
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            return response()->json([
                'countries' => [],
                'leagues' => [],
                'teams' => [],
            ]);
        }
    }

    public function leguesByCountry($code, $current = 'true') {
        $response = ['status' => null];
        if(is_string($code) && strlen($code) === 2) {
            $response['leagues'] = APIFootballHelper::leaguesByCountry($code, $current);
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error.';
            $response['error'] = 'Invalid country code.';

            return response()->json($response, 422);
        }
    }

    public function matchsByLeague(
        $league_id ,
        $current = '1', 
        $season = false, 
        $between = false, 
        $next = false, 
        $round = false
    ) {

        $response = ['status' => null];
        if(is_string($league_id) && is_numeric($league_id)) {
            $response['matchs'] = APIFootballHelper::matchsByLeague($league_id, $current, $season, $between, $next, $round);
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error.';
            $response['error'] = 'Invalid league ID.';

            return response()->json($response, 422);
        }

    }

    public function matchsByTeam($team_id, $current = true, $between = false) {
        $response = ['status' => null];
        if(is_string($team_id) && is_numeric($team_id)) {
            $response['matchs'] = APIFootballHelper::matchsByTeam($team_id, $current, $between);
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error.';
            $response['error'] = 'Invalid team ID.';

            return response()->json($response, 422);
        }
    }

    public function teamsByLeague($league_id, $season) {
        $response = ['status' => null];
        if(is_string($league_id) && is_numeric($league_id)) {
            $response['teams'] = APIFootballHelper::teamsByLeague($league_id, $season);
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error.';
            $response['error'] = 'Invalid country name.';

            return response()->json($response, 422);
        }
    }
}
