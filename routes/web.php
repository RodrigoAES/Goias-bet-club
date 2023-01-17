<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\DevController;

use App\Models\AdminOpt;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Dev refresh World-Cup API token
Route::post('dev/refresh', [DevController::class, 'refreshDependenceApiToken']);

Route::get('logo', function() {
    $logo = AdminOpt::select('value')->where('name', 'logo')->first();
    return Storage::download($logo['value']);
});

Route::get('home_bg', function() {
    $home_bg = AdminOpt::select('value')->where('name', 'home_bg')->first();
    return Storage::download($home_bg['value']);
});

Route::get('home_content_bg', function() {
    $home_content_bg = AdminOpt::select('value')->where('name', 'home_content_bg')->first();
    return Storage::download($home_content_bg['value']);
});

Route::get('rules_bg', function() {
    $rules_bg = AdminOpt::select('value')->where('name', 'rules_bg')->first();
    return Storage::download($rules_bg['value']);
});

Route::get('card_bg', function() {
    $card_bg = AdminOpt::select('value')->where('name', 'card_bg')->first();
    return Storage::download($card_bg['value']);
});

Route::get('bonus_bg_image', function() {
    $bonus_bg = AdminOpt::select('value')->where('name', 'bonus_bg_image')->first();
    return Storage::download($bonus_bg['value']);
});

Route::get('team/flag/{file}', function($file) {
    return Storage::download("public/$file");
});