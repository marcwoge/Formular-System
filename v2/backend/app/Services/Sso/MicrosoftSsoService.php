<?php

namespace App\Services\Sso;

use App\Models\ExternalIdentity;
use App\Models\IdentityProvider;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use RuntimeException;

class MicrosoftSsoService
{
    public function isEnabled(): bool
    {
        return (bool) config('sso.microsoft.enabled')
            && filled(config('services.azure.client_id'))
            && filled(config('services.azure.client_secret'));
    }

    public function frontendRedirectUrl(): string
    {
        return (string) config('sso.microsoft.frontend_redirect_url', config('app.url', 'http://localhost:8080').'/');
    }

    public function syncUser(SocialiteUser $socialiteUser): User
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Microsoft SSO is not enabled.');
        }

        $email = strtolower(trim((string) ($socialiteUser->getEmail() ?: data_get($socialiteUser->user, 'mail') ?: data_get($socialiteUser->user, 'userPrincipalName'))));
        $externalId = (string) $socialiteUser->getId();
        $provider = $this->resolveProvider();

        if ($externalId === '') {
            throw new RuntimeException('Microsoft SSO response does not include a stable external identifier.');
        }

        if ($email === '') {
            throw new RuntimeException('Microsoft SSO response does not include an email address.');
        }

        $this->assertDomainAllowed($email);

        return DB::transaction(function () use ($provider, $socialiteUser, $externalId, $email) {
            $identity = ExternalIdentity::query()
                ->with('user.roles')
                ->where('identity_provider_id', $provider->id)
                ->where('external_identifier', $externalId)
                ->first();

            $user = $identity?->user;

            if (! $user) {
                $user = User::query()->where('email', $email)->first();
            }

            if (! $user) {
                if (! config('sso.microsoft.auto_provision', true)) {
                    throw new RuntimeException('Automatic user provisioning is disabled for Microsoft SSO.');
                }

                $displayName = trim((string) $socialiteUser->getName()) ?: $email;
                $loginName = Str::before($email, '@');

                $user = User::query()->create([
                    'name' => $displayName,
                    'display_name' => $displayName,
                    'login_name' => Str::limit(Str::slug($loginName, '-'), 120, ''),
                    'email' => $email,
                    'password' => Str::password(32),
                    'status' => 'active',
                    'is_guest' => false,
                    'department' => data_get($socialiteUser->user, 'department'),
                    'location' => data_get($socialiteUser->user, 'officeLocation'),
                ]);

                $defaultRoleId = Role::query()->where('slug', config('sso.microsoft.default_role_slug', 'standardbenutzer'))->value('id');
                if ($defaultRoleId) {
                    $user->roles()->syncWithoutDetaching([$defaultRoleId]);
                }
            } else {
                $user->forceFill([
                    'name' => trim((string) $socialiteUser->getName()) ?: $user->name,
                    'display_name' => trim((string) $socialiteUser->getName()) ?: $user->display_name,
                    'department' => data_get($socialiteUser->user, 'department') ?: $user->department,
                    'location' => data_get($socialiteUser->user, 'officeLocation') ?: $user->location,
                    'status' => 'active',
                    'last_login_at' => now(),
                ])->save();
            }

            ExternalIdentity::query()->updateOrCreate(
                [
                    'identity_provider_id' => $provider->id,
                    'external_identifier' => $externalId,
                ],
                [
                    'user_id' => $user->id,
                    'external_username' => data_get($socialiteUser->user, 'userPrincipalName', $email),
                    'attributes' => $socialiteUser->user,
                    'last_synced_at' => now(),
                ]
            );

            return $user->fresh(['roles.permissions', 'groups.roles.permissions']);
        });
    }

    protected function resolveProvider(): IdentityProvider
    {
        return IdentityProvider::query()->updateOrCreate(
            ['name' => config('sso.microsoft.provider_name', 'microsoft-365')],
            [
                'type' => 'microsoft_entra_oidc',
                'is_enabled' => $this->isEnabled(),
                'configuration' => [
                    'tenant' => config('services.azure.tenant'),
                    'client_id' => config('services.azure.client_id') ? 'configured' : null,
                ],
            ]
        );
    }

    protected function assertDomainAllowed(string $email): void
    {
        $domains = config('sso.microsoft.allowed_email_domains', []);
        if ($domains === []) {
            return;
        }

        $domain = Str::after($email, '@');
        if (! in_array($domain, $domains, true)) {
            throw new RuntimeException('The Microsoft account domain is not allowed for this installation.');
        }
    }
}