<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DevController;
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