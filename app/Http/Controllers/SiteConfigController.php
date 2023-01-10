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
            foreach($opts as  $opt) {
                if($opt->name === 'phone') {
                    $response['phone'] = $opt->value;

                } else if($opt->name === 'rules') {
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

                } else if ($opt->name === 'bonus_bg_image') {
                    $response['bonus_bg_image'] = $opt->value;

                } else if ($opt->name === 'bonus_text_color_1') {
                    $response['bonus_text_color_1'] = $opt->value;
                    
                } else if ($opt->name === 'bonus_text_color_2') {
                    $response['bonus_text_color_2'] = $opt->value;
                    
                }
            }

            $response['attendants'] = Attendant::all();
            
            $response['status'] = 'success';
            return response()->json($response, 200);
        }
    }
}
