<?php

use App\Http\Controllers\Api\AccessCatalogController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\SystemLicenseController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'backend-api',
        'product' => config('licensing.product_name'),
        'version' => 'v2-initial',
    ]);
});

Route::get('/system/license-status', SystemLicenseController::class);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/access/catalog', AccessCatalogController::class)->middleware('permission:system.admin');

    Route::get('/forms', [FormController::class, 'index'])->middleware('permission:forms.manage');
    Route::post('/forms', [FormController::class, 'store'])->middleware('permission:forms.manage');
    Route::get('/forms/{form}', [FormController::class, 'show'])->middleware('permission:forms.manage');
    Route::put('/forms/{form}', [FormController::class, 'update'])->middleware('permission:forms.manage');
    Route::post('/forms/{form}/versions', [FormController::class, 'addVersion'])->middleware('permission:forms.manage');
    Route::post('/forms/{form}/publish/{version}', [FormController::class, 'publish'])->middleware('permission:forms.publish');
});