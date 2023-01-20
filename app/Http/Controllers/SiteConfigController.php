<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AdminOpt;
use App\Models\User;
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

            $activeAccounts = User::select('id')->where('active', true)->get();
            foreach($activeAccounts as $account) {
                $ids[] = $account->id;
            }

            $response['attendants'] = Attendant::whereIn('user_id', $ids);
            
            $response['status'] = 'success';
            return response()->json($response, 200);
        }
    }
}
