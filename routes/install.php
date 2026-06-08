<?php

use App\Http\Controllers\Installation\InstallController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Install Routes
|--------------------------------------------------------------------------
|
| Here is where you can register install routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{any?}', [InstallController::class, 'index'])->where('any', '.*')->name('install');
