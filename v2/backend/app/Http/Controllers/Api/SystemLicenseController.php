<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Licensing\LicenseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemLicenseController extends Controller
{
    public function __invoke(Request $request, LicenseManager $licenseManager): JsonResponse
    {
        return response()->json($licenseManager->currentStatus($request->boolean('refresh')));
    }
}