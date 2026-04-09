<?php

use App\Http\Controllers\Sso\MicrosoftSsoController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/microsoft/redirect', [MicrosoftSsoController::class, 'redirect'])->name('auth.microsoft.redirect');
Route::get('/auth/microsoft/callback', [MicrosoftSsoController::class, 'callback'])->name('auth.microsoft.callback');