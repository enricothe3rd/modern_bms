<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/reports', function () {
    return view('reports');
})->name('reports');

Route::get('/obligation_requests', function () {
    return view('obligation_requests.index');
})->name('obligationRequests');

Route::get('/setUp', function () {
    return view('setUp.index');
})->name('setUp');


// departments
Route::resource('departments', DepartmentController::class)->except(['create', 'show']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
