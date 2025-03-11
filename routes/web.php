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
Route::get('/newfilm', [DashboardController::class, 'newFilm'])->name('newfilm');
Route::get('/newactor', [DashboardController::class, 'newActor'])->name('newactor');
Route::get('/newcategory', [DashboardController::class, 'newCat'])->name('newcategory');
