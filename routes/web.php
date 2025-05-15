<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\HomeController;

// Route::get('/', function () {
//     return Inertia::render('welcome');
// })->name('home');
// Temporary add this route to check the exact path

// Route::get('/excel', [ExcelController::class,"index"]);
// Route::post('/import', [ExcelController::class,"import"]);
// Route::get('/export', [ExcelController::class,"export"]);
//
Route::post('/radios/download', [RadioController::class, 'downloadMultiple'])->name('radios.downloadMultiple');
Route::get('/radios-upload/{id}', [RadioController::class, 'upload'])->name('upload');
Route::get('/', [HomeController::class,"home"])->name('home');
Route::get('/radio', [RadioController::class,"index"])->name('listen');
Route::post('/imporadio', [RadioController::class,"import"]);
Route::get('/exporadio', [RadioController::class,"export"]);
Route::get('/data', [RadioController::class,"data"]);
Route::get('/plusrad/{rad}', [RadioController::class,"show"])->name('plusrad');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
