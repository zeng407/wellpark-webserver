<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ParkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('activities', [ActivityController::class, 'index']);
Route::put('activitie', [ActivityController::class, 'update']);
Route::get('parks', [ParkController::class, 'index']);
Route::post('park', [ParkController::class, 'store']);
Route::get('latest-parks', [ParkController::class, 'indexLatest']);
Route::post('pred-park', [ParkController::class, 'storePred']);
Route::get('pred-parks', [ParkController::class, 'getPreds']);
Route::post('park-image', [ParkController::class, 'storeImage']);
Route::get('park-images', [ParkController::class, 'getImages']);

