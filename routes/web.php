<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SettingController;

Route::get('/', [ChatController::class, 'index']);
Route::post('/generate', [ChatController::class, 'generate'])->name('generate');
Route::get('/get-current-model', [ChatController::class, 'getCurrentModel'])->name('get-current-model');


Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');
Route::delete('/settings/{id}', [SettingController::class, 'destroy'])->name('settings.destroy');

Route::post('/settings/test-groq-key', [SettingController::class, 'testGroqKey'])->name('settings.testGroqKey');
