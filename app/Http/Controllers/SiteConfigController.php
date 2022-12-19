<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AdminOpt;

class SiteConfigController extends Controller
{
    public function getOpts() {
        $response = ['status' => null];

        $opts = AdminOpt::all();
        if($opts) {
            foreach($opts as  $opt) {
                if($opt->name === 'phone') {
                    $response['phone'] = $opt->value;

                }else if($opt->name === 'rules') {
                    foreach(explode('|', $opt->value) as $rule) {
                        $rules[] = $rule;
                    } 
                    array_pop($rules);

                    $response['rules'] = $rules;
                    
                }else if($opt->name === 'home_bg') {
                    $response['home_bg'] = $opt->value;

                }else if($opt->name === 'logo') {
                    $response['logo'] = $opt->value;

                }else if($opt->name === 'name') {
                    $response['name'] = $opt->value;

                } else if($opt->name === 'p_color') {
                    $response['p_color'] = $opt->value;

                } else if($opt->name === 's_color') {
                    $response['s_color'] = $opt->value;

                } else if($opt->name === 'name_color_1') {
                    $response['name_color_1'] = $opt->value;

                } else if ($opt->name === 'name_color_2') {
                    $response['name_color_2'] = $opt->value;
                }
            }
            $response['status'] = 'success';
            return response()->json($response, 200);
        }
    }
}
