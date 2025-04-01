<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//options
Route::get('/actors/all', [ApiController::class, 'getActors']);
Route::get('/categories/all', [ApiController::class, 'getCategories']);
Route::get('/languages/all', [ApiController::class, 'getLanguages']);

//actors
Route::get('/actors', [ActorController::class, 'index']);
Route::post('/actors', [ActorController::class, 'store']);
Route::get('/actors/{id}/edit', [ActorController::class, 'edit']);
Route::put('/actors/{id}/edit', [ActorController::class, 'update']);
Route::delete('/actors/{id}', [ActorController::class, 'destroy']);

//Route::get('/films', [FilmController::class, 'index']);
//Route::post('/films', [FilmController::class, 'store']);
//Route::get('/films/{id}/edit', [FilmController::class, 'edit']);
//Route::put('/films/{id}/edit', [FilmController::class, 'update']);
//Route::delete('/films/{id}', [FilmController::class, 'destroy']);

//categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{id}/edit', [CategoryController::class, 'edit']);
Route::put('/categories/{id}/edit', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
//costumers
Route::get('/customers', [CustomerController::class, 'index']); //  clientes
Route::post('/customers', [CustomerController::class, 'store']); //  nuevo cliente
Route::get('/customers/{id}/edit', [CustomerController::class, 'edit']); // edit
Route::put('/customers/{id}/edit', [CustomerController::class, 'update']); // Act cliente
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']); // Eliminar cliente

//Staff
Route::get('/staff', [StaffController::class, 'index']);
Route::post('/staff', [StaffController::class, 'store']);
Route::get('/staff/{id}/edit', [StaffController::class, 'edit']);
Route::put('/staff/{id}/edit', [StaffController::class, 'update']);
Route::delete('/staff/{id}', [StaffController::class, 'destroy']);
