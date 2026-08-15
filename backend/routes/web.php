<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return ['redirect' => '/login'];
})->name('login');

Route::post('/login', [SessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/register2', [RegisteredUserController::class, 'addInfo']);
Route::get('/verificationCode', [RegisteredUserController::class, 'getCode'])->middleware('auth:sanctum');
Route::post('/verificationCode', [RegisteredUserController::class, 'verifyCode'])->middleware('auth:sanctum');
Route::post('/saveNumber', [RegisteredUserController::class, 'storeNumber'])->middleware('auth:sanctum');
Route::post('logout', [SessionController::class, 'destroy']);
Route::get('/user', function (Request $request) {
    return $request->user();
});



Route::controller(BookController::class)->group(function () {
    Route::get('/books', 'index');
    Route::post('/books', 'store');
    Route::get('/books/{book}', 'show');
    Route::patch('/books/{book}', 'update');
    Route::patch('/books/status/{book}', 'updateStatus');
    Route::delete('/books/{book}', 'destroy');
})->middleware('auth:sanctum');


Route::controller(EmployeeController::class)->group(function () {
    Route::get('/employees', 'index');
    Route::post('/employees', 'store');
    Route::get('/employees/{employee}', 'show');
    Route::get('/employees/{employee}/image', 'getImage')->name('employeeImage');
    Route::patch('/employees/{employee}', 'update');
    Route::delete('/employees/{employee}', 'destroy');
})->middleware('auth:sanctum');

Route::controller(OccupationController::class)->group(function () {
    Route::get('/occupations', 'index');
    Route::post('/occupations', 'store');
    Route::get('/occupations/{occupation}', 'show');
    Route::patch('/occupations/{occupation}', 'update');
    Route::delete('/occupations/{occupation}', 'destroy');
})->middleware('auth:sanctum');

Route::controller(TaskController::class)->group(function () {
    Route::get('/tasks', 'index');
    Route::post('/tasks', 'store');
    Route::get('/tasks/{task}', 'show');
    Route::patch('/tasks/{task}', 'update');
    Route::delete('/tasks/{task}', 'destroy');
})->middleware('auth:sanctum');
