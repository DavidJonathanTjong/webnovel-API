<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NovelController;
use App\Http\Controllers\Api\DetailNovelController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// register user
Route::post('register', [AuthController::class, 'register']);
//mengakses http://127.0.0.1:8000/api/register pada class Authcontroller di function register
//login user
Route::post('login', [AuthController::class, 'login']);
//logout user
Route::get('logout', [AuthController::class, 'logout']);
// mengambil data user
Route::get('users', [UserController::class , 'list'])->name('users.list');
Route::get('/penggunav2', [UserController::class , 'get'])->name('users');
// update dan delete user
Route::put('users/edit/{user}', [UserController::class , 'update']);
Route::delete('users/delete/{user}', [UserController::class , 'destroy']);


//crud novel (admin) 
// Route::apiResource('novels', NovelController::class); // (untuk yang GET abaikan karena sudah memakai class list)
Route::get('listnovel', [NovelController::class , 'list'])->name('listnovel.list');
Route::post('novels', [NovelController::class , 'store']);
Route::put('novels/edit/{novel}', [NovelController::class , 'update']);
Route::delete('novels/delete/{novel}', [NovelController::class , 'destroy']);

//crud detail novel 
// Route::apiResource('detailnovel', DetailNovelController::class); //(untuk yang GET abaikan karena sudah memakai class list)
Route::get('listdetailnovel', [DetailNovelController::class , 'list'])->name('listdetailnovel.list');
Route::post('detailnovel', [DetailNovelController::class , 'store']);
Route::put('detailnovel/edit/{detailnovel}', [DetailNovelController::class , 'update']);
Route::delete('detailnovel/delete/{detailnovel}', [DetailNovelController::class , 'destroy']);