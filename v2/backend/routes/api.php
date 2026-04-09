<?php

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