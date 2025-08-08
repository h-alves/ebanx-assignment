<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::post('/reset', [AccountController::class, 'reset'])->name('account.reset');
Route::get('/balance', [AccountController::class, 'balance'])->name('account.balance');
Route::post('/event', [EventController::class, 'event'])->name('account.event');