<?php

use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\RecipientController;
use App\Http\Controllers\Admin\VillageController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::get('/dashboard', 'HomeController@index')->name('home');

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');

Route::resource('/districts', DistrictController::class);

Route::resource('/villages', VillageController::class);

Route::resource('/recipients', RecipientController::class);

Route::resource('/users', UserController::class);

Route::get('/about', function () {
    return view('about');
})->name('about');
