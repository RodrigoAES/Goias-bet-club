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

Route::get('team/flag/{file}', function($file) {
    return Storage::download("public/$file");
});