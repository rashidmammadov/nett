<?php

use Illuminate\Http\Request;

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

    Route::get('/refreshUser', 'UserController@refreshUser');
    Route::post('/login', 'UserController@auth');
    Route::post('/logout', 'UserController@logout');
    Route::post('/register', 'UserController@register');

    Route::post('/participant', 'ParticipantController@attend');
    Route::delete('/participant', 'ParticipantController@leave');

    Route::post('/tournament', 'TournamentController@add');
});
