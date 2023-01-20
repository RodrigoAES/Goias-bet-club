<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AdminOpt;
use App\Models\Attendant;

class SiteConfigController extends Controller
{
    public function getOpts() {
        $response = ['status' => null];

        $opts = AdminOpt::all();
        if($opts) {
            foreach($opts as $opt) {
                if($opt->name === 'rules') {
                    foreach(explode('|', $opt->value) as $rule) {
                        $rules[] = $rule;
                    } 
                    array_pop($rules);

                    $response['rules'] = $rules;
                } else {
                    $response[$opt->name] = $opt->value;
                }
            }

            $response['attendants'] = Attendant::all();
            
            $response['status'] = 'success';
            return response()->json($response, 200);
        }
    }
}
