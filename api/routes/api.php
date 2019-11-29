<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

header('Access-Control-Allow-Origins:*');
header('Access-Control-Allow-Methods:*');

Route::group(['middleware' => 'cors', 'prefix' => '/v1'], function () {

    Route::get('/init', function() {
        $route = Artisan::call('route:clear');
        $config = Artisan::call('config:clear');
        $cache = Artisan::call('cache:clear');
        $migrate = Artisan::call('migrate');
        echo 'route: '.$route.'<br>'.
            'config: '.$config.'<br>'.
            'cache: '.$cache.'<br>'.
            'migrate: '.$migrate;
    });

    Route::put('/fixture', 'FixtureController@setScore');

    Route::post('/games', 'GameController@add');
    Route::get('/games', 'GameController@get');
    Route::get('/games/{gameId}', 'GameController@get');

    Route::get('/refreshUser', 'UserController@refreshUser');
    Route::post('/login', 'UserController@auth');
    Route::post('/logout', 'UserController@logout');
    Route::post('/register', 'UserController@register');
    Route::put('/activate', 'UserController@activate');
    Route::put('/updateSettings', 'UserController@updateSettings');

    Route::post('/participants', 'ParticipantController@attend');
    Route::delete('/participants', 'ParticipantController@leave');

    Route::post('/tournaments', 'TournamentController@add');
    Route::get('/tournaments', 'TournamentController@get');
    Route::get('/tournaments/{tournamentId}', 'TournamentController@getDetail');
    Route::get('/myTournaments', 'TournamentController@getMine');

    Route::get('/reports', 'ReportController@get');
});
