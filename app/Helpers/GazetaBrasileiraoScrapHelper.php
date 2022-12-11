<?php

namespace App\Helpers;

class GazetaBrasileiraoScrapHelper {
    public static function scrapRound($round) {
        $file = file_get_contents('https://www.gazetaesportiva.com/campeonatos/brasileiro-serie-a/');
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($file);
        libxml_use_internal_errors($internalErrors);

        $xPath = new \DOMXPath($dom);
        $node_list = $xPath->query(".//*[contains(concat(' ',normalize-space(@class),' '),' rodadas_grupo_A_numero_rodada_$round ')]");

        foreach($node_list as $node) {
            foreach($node->childNodes as $key => $child) {
                $childs[] = $xPath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," table__games__item ")]', $child);

                $matchs = [];
                foreach($childs as $child) {
                    if($child->length > 0) {
                        foreach($child as $key => $li) {
                            $dates = $xPath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," date ")]', $li);
                            foreach($dates as $date) {
                                $datelocal = $date->textContent;
                                $datelocal = preg_replace( "/<br>|\n/", "", $datelocal);

                                $date_local = explode('•', $datelocal);
                                $datetime = explode(' ',$date_local[0]);
                                $date = explode('/', $datetime[0]);
                                $year = date('Y', strtotime('now'));
                                $date = "$year-$date[1]-$date[0]";
                                $time = "$datetime[1]:00";
                                $local = $date_local[1];

                                $datetime = "$date $time";
                                if(strtotime('+100 minutes', strtotime('now')) > strtotime($datetime)) {
                                    $matchs[$key]['finished'] = true;
                                } else {
                                    $matchs[$key]['finished'] = false;
                                }

                                $matchs[$key]['datetime'] = $datetime;
                                $matchs[$key]['local'] = $local;
                            }
                            $home_scores = $xPath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," score__home ")]', $li);
                            foreach($home_scores as $home_score) {
                                $matchs[$key]['home_score'] = intval($home_score->textContent);
                            }
                            $away_scores = $xPath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," score__guest ")]', $li);
                            foreach($away_scores as $away_score) {
                                $matchs[$key]['away_score'] = intval($away_score->textContent);
                            }
                            $teams_home = $xPath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," teams ")]//*[contains(concat(" ",normalize-space(@class)," ")," home ")]//a', $li);
                            $team_info = [];
                            foreach($teams_home as $a) {
                                foreach($a->attributes as $attribute) {
                                    if($attribute->name === 'title') {
                                        $team_info[] = $attribute;
                                    }
                                }
                                
                            }
                            foreach($team_info as $index => $info) {
                                if($index === 0) {
                                    $matchs[$key]['home_name'] = $info->value;
                                }
                                if($index === 1) {
                                     $matchs[$key]['home_flag'] = explode('/', $info->value)[6];
                                }
                            }
                            $teams_away = $xPath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," teams ")]//*[contains(concat(" ",normalize-space(@class)," ")," guest ")]//a', $li);
                            $team_info = [];
                            foreach($teams_away as $a) {
                                foreach($a->attributes as $attribute) {
                                    if($attribute->name === 'title') {
                                        $team_info[] = $attribute;
                                    }
                                }
                                
                            }
                            foreach($team_info as $index => $info) {
                                if($index === 0) {
                                    $matchs[$key]['away_name'] = $info->value;
                                }
                                if($index === 1) {
                                    $matchs[$key]['away_flag'] = explode('/', $info->value)[6];
                                }
                            }
                            $matchs[$key]['group'] = $round; 
                            $matchs[$key]['id'] = $key;
                        }
                    }
                }
            }
        }
        return $matchs;
    }

    public static function requestFlag($file) {
        $url = "https://json.gazetaesportiva.com/footstats/logos/88x88/$file";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Content-Type: aplication/json',
            "Authorization: Bearer ".env('DEPENDENCE_API_TOKEN')
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);

        echo $response;

    }
}