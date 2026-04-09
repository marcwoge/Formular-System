<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use App\Services\Sso\MicrosoftSsoService;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;

class MicrosoftSsoController extends Controller
{
    public function __construct(private readonly MicrosoftSsoService $microsoftSsoService)
    {
    }

    public function redirect(): RedirectResponse
    {
        abort_unless($this->microsoftSsoService->isEnabled(), 404);

        return Socialite::driver('azure')
            ->scopes(['openid', 'profile', 'email', 'User.Read'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            abort_unless($this->microsoftSsoService->isEnabled(), 404);

            $socialiteUser = Socialite::driver('azure')
                ->scopes(['openid', 'profile', 'email', 'User.Read'])
                ->user();

            $user = $this->microsoftSsoService->syncUser($socialiteUser);
            $token = $user->createToken('Microsoft SSO', array_values(array_unique(array_merge(['*'], $user->permissionSlugs()))));

            return redirect()->away(
                $this->buildFrontendRedirect([
                    'sso' => 'success',
                    'provider' => 'microsoft',
                    'access_token' => $token->plainTextToken,
                    'user_email' => $user->email,
                ])
            );
        } catch (RuntimeException $exception) {
            return redirect()->away(
                $this->buildFrontendRedirect([
                    'sso' => 'error',
                    'provider' => 'microsoft',
                    'message' => $exception->getMessage(),
                ])
            );
        } catch (\Throwable $exception) {
            return redirect()->away(
                $this->buildFrontendRedirect([
                    'sso' => 'error',
                    'provider' => 'microsoft',
                    'message' => 'Microsoft SSO callback failed.',
                ])
            );
        }
    }

    protected function buildFrontendRedirect(array $payload): string
    {
        $baseUrl = rtrim($this->microsoftSsoService->frontendRedirectUrl(), '/');

        return $baseUrl.'/#'.http_build_query($payload, '', '&', PHP_QUERY_RFC3986);
    }
}