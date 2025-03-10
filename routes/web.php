<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
/* hola solo corre
php artisan serve
y en tu navegador escribe
localhost:8000/dashboard
 y listo */

Route::get('/tablas', [DashboardController::class, 'tablas'])->name('tablas');
Route::get('/about', [DashboardController::class, 'aboutFilm'])->name('about');