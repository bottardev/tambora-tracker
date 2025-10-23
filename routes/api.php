<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RouteController as RCtrl;
use App\Http\Controllers\Api\TripController as TCtrl;
use App\Http\Controllers\Api\LocationController as LCtrl;
use App\Http\Controllers\Api\EventController as ECtrl;


Route::post('/auth/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/routes/{route}', [RCtrl::class, 'show']);
    Route::get('/routes', [RCtrl::class, 'index']);


    Route::post('/trips', [TCtrl::class, 'store']);
    Route::get('/trips/{trip}', [TCtrl::class, 'show']);
    Route::patch('/trips/{trip}', [TCtrl::class, 'update']);


    Route::post('/trips/{trip}/locations', [LCtrl::class, 'storeBatch']);
    Route::get('/trips/{trip}/last', [LCtrl::class, 'last']);


    Route::post('/trips/{trip}/events', [ECtrl::class, 'store']);
});
