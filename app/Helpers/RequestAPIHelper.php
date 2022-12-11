<?php

namespace App\Helpers;

class RequestAPIHelper {
    public static function requestAllMatchs() {
        $url = 'http://api.cup2022.ir/api/v1/match';
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
        $response = json_decode($response);

        return $response;
    }
}