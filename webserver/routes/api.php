<?php

use App\Http\Controllers\ParkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('parks', [ParkController::class, 'index']);
Route::get('latest-parks', [ParkController::class, 'indexLatest']);
Route::post('pred-park', [ParkController::class, 'storePred']);
Route::get('pred-parks', [ParkController::class, 'getPreds']);
