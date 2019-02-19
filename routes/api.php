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
Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('dashboard', function () {
        return response()->json(['data' => 'Test Data']);
    });
    
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::post('login', 'AuthController@login')->name('api.name');
        Route::post('logout', 'AuthController@logout')->name('api.logout');
        Route::post('refresh', 'AuthController@refresh')->name('api.refresh');
        Route::post('me', 'AuthController@me')->name('api.me');
    });
    Route::apiResource('products', 'ProductsController')->middleware(['auth:api']);
    Route::get('load-image/{filename}','ProductsController@loadImage');
    
});
