<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SalesController;
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


Route::controller(RegisteredUserController::class)->group(function () {
    Route::post('/register', 'store');
    Route::post('/register2', 'addInfo');
    Route::get('/users', 'index');
});

Route::post('/login', [
    SessionController::class,
    'store',
])->middleware('throttle:login');


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
    Route::post('/guest/books', 'getBooksForGuests')->withoutMiddleware('auth:sanctum');
    Route::get('/books/{book}', 'show');
    Route::patch('/books/{book}', 'update');
    Route::patch('/books/status/{book}', 'updateStatus');
    Route::delete('/books/{book}', 'destroy');
})->middleware('auth:sanctum');


Route::controller(EmployeeController::class)->group(function () {
    Route::get('/employees', 'index');
    Route::post('/employees', 'store');
    Route::get('/employees/{employee}', 'show')->can('view','employee');
    Route::get('/employees/{employee}/image', 'getImage')->name('employeeImage');
    Route::patch('/employees/{employee}', 'update')->can('update','employee');
    Route::delete('/employees/{employee}', 'destroy')->can('delete','employee');
})->middleware('auth:sanctum');

Route::controller(OccupationController::class)->group(function () {
    Route::get('/occupations', 'index');
    Route::post('/occupations', 'store');
    Route::get('/occupations/{occupation}', 'show')->can('view','occupation');
    Route::patch('/occupations/{occupation}', 'update')->can('update','occupation');
    Route::delete('/occupations/{occupation}', 'destroy')->can('delete','occupation');
})->middleware('auth:sanctum');

Route::controller(TaskController::class)->group(function () {
    Route::get('/tasks', 'index');
    Route::post('/tasks', 'store');
    Route::get('/tasks/{task}', 'show')->can('view','task');
    Route::patch('/tasks/{task}', 'update')->can('update','task');
    Route::delete('/tasks/{task}', 'destroy')->can('delete','task');
})->middleware('auth:sanctum');


Route::controller(ResourceController::class)->group(function () {
    Route::get('/resources', 'index');
    Route::post('/resources', 'store');
    Route::get('/resources/{resource}', 'show')->can('view','resource');
    Route::patch('/resources/{resource}', 'update')->can('update','resource');
    Route::delete('/resources/{resource}', 'destroy')->can('delete','resource');
})->middleware('auth:sanctum');



Route::controller(OrderController::class)->group(function () {
    Route::get('/orders', 'index')->middleware('auth:sanctum');
    Route::get('/sessionItems', 'getSessionItems');
    Route::get('/sessionUserId', 'getUserId');
    Route::post('/orders', 'store');
    Route::get('/orders/{order}', 'show');
    Route::post('/orders/items/files/{orderItem}', 'downloadFile')->middleware('auth:sanctum');
    Route::patch('/orders/items/{orderItem}', 'updateItem')->middleware('auth:sanctum');
    Route::patch('/orders/{order}', 'update');
    Route::delete('/orders/{order}', 'destroy');
});

Route::controller(SalesController::class)->group(function () {
    Route::post('/sales', 'index');
})->middleware('auth:sanctum');
