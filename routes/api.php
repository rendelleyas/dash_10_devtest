<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function(){
    return 'test here';
});

Route::get('/allblacks', [PlayerController::class, 'allBlackWithFeatures']);





// Route::group(['middleware' => 'cors'], function () {
//     Route::get('/test', function(){
//         return 'test here';
//     });
// });
