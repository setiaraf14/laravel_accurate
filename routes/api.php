<?php

use Illuminate\Http\Request;
use Illuminate\Routing\RouteGroup;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccurateController;

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


Route::prefix('accurate')->group( function() {
    Route::post('add-data-db', [AccurateController::class, 'addData']);
    Route::get('store-data-accurate', [AccurateController::class, 'storeData']);
    Route::get('bulk-data-accurate', [AccurateController::class, 'bulkData']);
    Route::get('open-db-accurate', [AccurateController::class, 'openDb']);
});
