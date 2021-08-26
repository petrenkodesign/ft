<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\FtClientBankController;
Route::middleware('static.token:api')->group( function () {
   Route::post('clientPaketData', [FtClientBankController::class,'getClientData']);
   Route::post('bankPaketData', [FtClientBankController::class,'toBankData']);
});
