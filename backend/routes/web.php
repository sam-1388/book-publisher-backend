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
Route::post('/register2',[RegisteredUserController::class,'addInfo'])->middleware('auth:sanctum');
Route::get('logout',[SessionController::class,'destroy']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::controller(BookController::class)->group(function(){
    Route::get('/books','index');
    Route::post('/books','store');
})->middleware('auth:sanctum');


