<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ParticipanteController as AdminParticipanteController;
use App\Http\Controllers\Admin\RuletaController as AdminRuletaController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\RuletaController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::post('/registro', [ParticipanteController::class, 'store'])->name('participantes.store');

Route::get('/ruleta', [RuletaController::class, 'show'])->name('ruleta.show');
Route::post('/ruleta', [RuletaController::class, 'store'])->name('ruleta.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->middleware('throttle:5,1');
    });

    Route::middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
        Route::get('/', fn () => redirect()->route('admin.participantes.index'))->name('dashboard');
        Route::get('participantes', [AdminParticipanteController::class, 'index'])->name('participantes.index');
        Route::get('ruleta', [AdminRuletaController::class, 'edit'])->name('ruleta.edit');
        Route::put('ruleta', [AdminRuletaController::class, 'update'])->name('ruleta.update');
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
