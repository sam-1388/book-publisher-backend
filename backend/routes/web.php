<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return ['status'=>'done'];
});
Route::get('/login',function(){
    return ['redirect'=>'/login'];
})->name('login');
Route::post('/login',[SessionController::class,'store']);
Route::post('/register',[RegisteredUserController::class,'store']);
Route::post('/register2',[RegisteredUserController::class,'addInfo']);
Route::get('/verificationCode',[RegisteredUserController::class,'getCode'])->middleware('auth:sanctum');
Route::post('/verificationCode',[RegisteredUserController::class,'verifyCode'])->middleware('auth:sanctum');
Route::post('/saveNumber',[RegisteredUserController::class,'storeNumber'])->middleware('auth:sanctum');
Route::get('logout',[SessionController::class,'destroy']);
Route::get('/user', function (Request $request) {
    return $request->user();
});



Route::controller(BookController::class)->group(function(){
    Route::get('/books','index');
    Route::post('/books','store');
})->middleware('auth:sanctum');


