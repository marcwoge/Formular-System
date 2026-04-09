<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Sso\MicrosoftSsoService;
use Illuminate\Http\JsonResponse;

class AuthProvidersController extends Controller
{
    public function __invoke(MicrosoftSsoService $microsoftSsoService): JsonResponse
    {
        return response()->json([
            'providers' => [
                [
                    'key' => 'local',
                    'label' => 'Lokale Anmeldung',
                    'enabled' => true,
                    'login_url' => null,
                ],
                [
                    'key' => 'microsoft',
                    'label' => 'Microsoft 365',
                    'enabled' => $microsoftSsoService->isEnabled(),
                    'login_url' => $microsoftSsoService->isEnabled() ? '/auth/microsoft/redirect' : null,
                ],
            ],
        ]);
    }
}