<?php

use App\Http\Controllers\ParticipanteController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::post('/registro', [ParticipanteController::class, 'store'])->name('participantes.store');
