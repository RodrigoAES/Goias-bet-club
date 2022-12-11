<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminBrasileiraoController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PublicBetController;
use App\Http\Controllers\UserCardController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\RankingController;

use App\Helpers\GazetaBrasileiraoScrapHelper;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Admin 401
Route::get('401', function(){
    $response = [
        'status' => 'error', 
        'error' => 'Unauthorized', 
        'authenticated' => false
    ];

    return response()->json($response, 401);
})->name('401');

// Brasileirao Flag Scrap
Route::get('brasileirao/flag/{file}', function($file) {
    GazetaBrasileiraoScrapHelper::requestFlag($file);
});

// Admin Login
Route::post('admin/auth/login', [AdminAuthController::class, 'login']);

// Public disponible cards to make bets
Route::get('cards', [CardController::class, 'allCards']);

// Public make bet
Route::post('bet', [PublicBetController::class, 'createBet']);

// public consult User Card
Route::get('card/{code}', [UserCardController::class, 'getCard']);

// Public consult Ranking
Route::get('ranking/{id}', [RankingController::class, 'getRanking']);

Route::middleware(['auth:api'])->group(function () {
    //Auth
    Route::post('admin/auth/logout', [AdminAuthController::class,'logout']);
    Route::get('admin/auth/validate', [AdminAuthController::class, 'validateToken']); 
    
    //Accounts/users
    Route::get('admin/users', [AdminAccountController::class, 'allUsers']);
    Route::get('admin/user/{id}', [AdminAccountController::class, 'getUser']);
    Route::post('admin/user', [AdminAccountController::class, 'createUser']);
    Route::put('admin/account/update/{id}', [AdminAccountController::class, 'updateAccount']);
    Route::delete('admin/user/delete/{id}', [AdminAccountController::class, 'deleteUser']);
    Route::put('admin/account/status/{id}', [AdminAccountController::class, 'activateAccountToggle']);

    // World Cup matchs
    Route::get('admin/matches', [AdminController::class, 'nextMatches']);

    // Brasileirao Matchs
    Route::get('admin/brasileirao/round/{round}', [AdminBrasileiraoController::class, 'round']);
    
    // Cards
    Route::post('admin/card', [AdminController::class, 'createCard']);
    Route::delete('admin/card/{id}', [AdminController::class, 'deleteCard']);

    // Admin Panel user bets
    Route::get('admin/user-bets', [AdminController::class, 'userBets']);
    Route::post('admin/validate', [AdminController::class, 'validateCard']);
    Route::put('admin/user-card/{id}', [AdminController::class, 'updateUserCard']);
    Route::delete('admin/user-card/{id}', [AdminController::class, 'deleteUserCard']);
    Route::get('admin/search-user-card/{q}', [AdminController::class, 'searchUserCard']);

    // Admin User Cards Ranking
    Route::get('admin/card-ranking/{id}', [AdminController::class, 'cardRanking']);
    Route::post('admin/card-ranking/{id}/publicate', [AdminController::class, 'publicateRanking']);

    // Admin Custom Championships
    Route::get('admin/championships', [CustomController::class, 'allChampionships']);
    Route::post('admin/championship', [CustomController::class, 'createChampionship']);
    Route::delete('admin/championship/{id}', [CustomController::class, 'deleteChampionship']);

    // Admin Custom Matchs 
    Route::get('admin/championship/{id}', [CustomController::class, 'allMatchs']);
    Route::post('admin/match', [CustomController::class, 'createMatch']);
    Route::put('admin/match/{id}', [CustomController::class, 'updateMatch']);
    Route::delete('admin/match/{id}', [CustomController::class, 'deleteMatch']);

    // Admin Custom Teams
    Route::get('admin/teams/{id}', [CustomController::class, 'allTeams']);
    Route::post('admin/team', [CustomController::class, 'createTeam']);
    Route::post('admin/team/{id}', [CustomController::class, 'updateTeam']);
    Route::delete('admin/team/{id}', [CustomController::class, 'deleteTeam']);

});

