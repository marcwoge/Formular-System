<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Branding\BrandingManager;
use App\Services\Licensing\LicenseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemLicenseController extends Controller
{
    public function __invoke(
        Request $request,
        LicenseManager $licenseManager,
        BrandingManager $brandingManager,
    ): JsonResponse {
        $licenseStatus = $licenseManager->currentStatus($request->boolean('refresh'));
        $licenseStatus['branding'] = $brandingManager->resolve($licenseStatus);

        return response()->json($licenseStatus);
    }
}