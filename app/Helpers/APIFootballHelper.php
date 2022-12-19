<?php

namespace App\Helpers;

class APIFootballHelper {
    public static function requestMatch() {

        $url = 'https://api-football-v1.p.rapidapi.com/v2/fixtures/live';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            "x-rapidapi-host: api-football-v1.p.rapidapi.com",
		    "x-rapidapi-key: 6e64059c43msh880a092c33ffec8p13d910jsn08fc535cb6c1"
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

        dd($response);

    }

    public static function search($q) {
        // Request
        function request($url) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = [
                "x-rapidapi-host: ".env('API_FOOTBALL_HOST'),
                "x-rapidapi-key: ".env('API_FOOTBALL_KEY')
            ];
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);

            $results = [];
            if(is_array($response->errors) && count($response->errors) === 0 && $response->results > 0) {
                foreach($response->response as $result) {
                    $results[] = $result;
                }
            }

            return $results;
        }

        if(str_contains($q, '-')) {
            $q = explode('-', $q);

            foreach($q as $key => $string) {
                $q[$key] = ltrim($string);
                $q[$key] = rtrim($q[$key]);
            }
         }

        if(is_string($q)) {
            $countries = request("https://api-football-v1.p.rapidapi.com/v3/countries?name=$q");
            $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?name=$q");
            $teams = request("https://api-football-v1.p.rapidapi.com/v3/teams?name=$q");

        } else if (is_array($q)) {
            // Countries
            $countries = request("https://api-football-v1.p.rapidapi.com/v3/countries?name=$q[0]");
            if(count($countries) === 0) {
                $countries = request("https://api-football-v1.p.rapidapi.com/v3/countries?name=$q[1]");
            }

            // Leagues
            if(count($q) === 2) {
                $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?name=$q[0]&country=$q[1]");
                if(count($leagues) === 0) {
                    $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?country=$q[0]&name=$q[1]");
                }
                if(count($leagues) === 0) {
                    $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?name=$q[0]&season=$q[1]");
                }

            } else if(count($q) === 3) {
                $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?name=$q[0]&season=$q[1]&round=$q[2]");
                
                if(count($leagues) === 0) {
                    $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?country=$q[0]&name=$q[1]&season=$q[2]");
                }
                if(count($leagues) === 0) {
                    $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?name=$q[0]&country=$q[1]&season=$q[2]");
                }

            } else if (count($q) === 4) {
                $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?name=$q[0]&country=$q[1]&season=$q[2]");
                if(count($leagues) === 0) {
                    $leagues = request("https://api-football-v1.p.rapidapi.com/v3/leagues?country=$q[0]&name=$q[1]&season=$q[2]");
                }
                foreach($leagues as $league) {
                    $matchs = request(
                        "https://api-football-v1.p.rapidapi.com/v3/fixtures?".
                        "league=".$league->league->id.
                        "&season=$q[2]".
                        "&round=Regular Season - $q[3]".
                        "&timezone=America/Sao_Paulo"
                    );
                    $league->matchs = $matchs;
                }
            } 

            // Teams 
            $teams = [];
            if(count($q) === 2) {
                $teams = request("https://api-football-v1.p.rapidapi.com/v3/teams?name=$q[0]&country=$q[1]");
                if(count($teams) === 0) {
                    $teams = request("https://api-football-v1.p.rapidapi.com/v3/teams?country=$q[0]&name=$q[1]");
                }
            }


        }

        foreach($leagues as $key => $league) {
            foreach($league->seasons as $season) {
                $seasons[] = [
                    'season' => $season->year,
                    'start' => date('d/m/Y H:i', strtotime($season->start)),
                    'end' => date('d/m/Y H:i', strtotime($season->end)),
                ];
            }

            $matchs = [];
            if(isset($league->matchs)) {
                foreach($league->matchs as $_key => $match) {
                    $finished = $match->fixture->status->long === 'Match Finished' ? true : false;
    
                    if($finished) {
                        $home_score = $match->score->fulltime->home;
                        $away_score = $match->score->fulltime->away;
    
                    } else {
                        $home_score = $match->goals->home;
                        $away_score = $match->goals->away;
    
                        if(
                            $match->score->extratime->home != null 
                            && $match->score->extratime->away != null
                        ) {
                            $home_score = $home_score - $match->score->extratime->home;
                            $away_score = $away_score - $match->score->extratime->away;
                        }
    
                        if(
                            $match->score->penalty->home != null
                            && $match->score->penalty->away != null
                        ) {
                            $home_score = $home_score - $match->score->penalty->home;
                            $away_score = $away_score - $match->score->penalty->away;
                        }
                    }
    
                    $league->matchs[$_key] = [
                        'id' => $match->fixture->id,
                        'date' => date('d/m/Y H:i',strtotime($match->fixture->date)),
                        'finished' => $finished,
                        'home_name' => $match->teams->home->name,
                        'away_name' => $match->teams->away->name,
                        'home_flag' => $match->teams->home->logo,
                        'away_flag' => $match->teams->away->logo,
                        'home_score' => $home_score,
                        'away_score' => $away_score,
    
                    ];
                }

                $matchs = $league->matchs;
            }

            $leagues[$key] = [
                'id' => $league->league->id,
                'name' =>$league->league->name,
                'logo' => $league->league->logo,
                'seasons' => $seasons,
                'matchs' => $matchs,
            ];
        }

        foreach($teams as $key => $team) {
            $teams[$key] = [
                'id' => $team->team->id,
                'code' => $team->team->code,
                'name' => $team->team->name,
                'founded' => $team->team->founded,
                'flag' => $team->team->logo,
            ];
        }

        $data = [
            'countries' => $countries,
            'leagues' => $leagues,
            'teams' => $teams,
        ];

        dd($data);
    }
}