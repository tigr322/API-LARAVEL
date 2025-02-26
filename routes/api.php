<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/registration', [AuthController::class, 'register']);
Route::middleware('auth:api')->get('/profile', [AuthController::class, 'profile']);

Route::post('/login', [AuthController::class, 'login']);

Route::delete('/user/delete', [AuthController::class, 'delete']);

Route::middleware('auth:api')->put('/user/update', [AuthController::class, 'update']);
Route::get('/show_all', [AuthController::class, 'show_all']);
Route::get('/show_user', [AuthController::class, 'show_user']);
Route::middleware('auth:api')->post('/addconfig', [AuthController::class, 'add_pc_config']);
Route::middleware('auth:api')->put('/user/config/update/id={id}', [AuthController::class, 'update_pc_conf']);
