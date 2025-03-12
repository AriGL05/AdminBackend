<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
/* hola solo corre
php artisan serve
y en tu navegador escribe
localhost:8000/dashboard
 y listo */

Route::get('/tablas/{tipo?}', [DashboardController::class, 'tablas'])->name('tablas');
Route::get('/about', [DashboardController::class, 'aboutFilm'])->name('about');

// New contact routes
Route::get('/contact', [DashboardController::class, 'contact'])->name('contact');
Route::post('/contact', [DashboardController::class, 'submitContact'])->name('contact.submit');
// Fallback route - Redirect all unknown routes to dashboard
Route::fallback(function () {
    return redirect()->route('dashboard');
});
Route::get('/tablas', [DashboardController::class, 'tablas'])->name('tablas');
Route::get('/newfilm', [DashboardController::class, 'newFilm'])->name('newfilm');
Route::get('/newactor', [DashboardController::class, 'newActor'])->name('newactor');
Route::get('/newcategory', [DashboardController::class, 'newCat'])->name('newcategory');


//--Info--//
Route::get('/actors/all', [ApiController::class, 'getActors']);
Route::get('/categories/all', [ApiController::class, 'getCategories']);
Route::get('/languages/all', [ApiController::class, 'getLanguages']);


Route::get('/actors', [ActorController::class, 'index']);
Route::post('/actors', [ActorController::class, 'store']);
Route::get('/actors/{id}/edit', [ActorController::class, 'edit']);
Route::put('/actors/{id}/edit', [ActorController::class, 'update']);
Route::delete('/actors/{id}', [ActorController::class, 'destroy']);

Route::get('/films', [FilmController::class, 'index']);
Route::post('/films', [FilmController::class, 'store']);
Route::get('/films/{id}/edit', [FilmController::class, 'edit']);
Route::put('/films/{id}/edit', [FilmController::class, 'update']);
Route::delete('/films/{id}', [FilmController::class, 'destroy']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{id}/edit', [CategoryController::class, 'edit']);
Route::put('/categories/{id}/edit', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
