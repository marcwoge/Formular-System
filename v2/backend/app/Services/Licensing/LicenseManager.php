<?php

namespace App\Services\Licensing;

use App\Models\LicenseConnector;
use App\Models\LicenseFeature;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class LicenseManager
{
    public function __construct(private readonly LicenseServerClient $client)
    {
    }

    public function currentStatus(bool $refresh = false): array
    {
        $connector = $this->resolveConnector();

        if (! $this->hasRemoteConfiguration($connector)) {
            return $this->recordFreeEdition($connector, 'missing_remote_configuration');
        }

        if (! $refresh && $this->hasFreshValidation($connector)) {
            return $this->buildResponse($connector, 'cached_remote_state');
        }

        try {
            $connector = $this->synchronizeRemoteState($connector);

            return $this->buildResponse($connector, 'remote_validation_ok');
        } catch (Throwable $exception) {
            $connector->forceFill([
                'is_valid' => false,
                'last_error' => $exception->getMessage(),
                'last_error_at' => now(),
            ])->save();

            return $this->recordFreeEdition($connector, 'remote_validation_failed', $exception->getMessage());
        }
    }

    protected function resolveConnector(): LicenseConnector
    {
        $defaults = [
            'driver' => config('licensing.driver'),
            'base_url' => config('licensing.server_url'),
            'product_name' => config('licensing.product_name'),
            'product_code' => config('licensing.product_code'),
            'activation_key' => config('licensing.activation_key'),
            'configuration' => [
                'mode' => config('licensing.mode'),
            ],
        ];

        $connector = LicenseConnector::query()->firstOrCreate([
            'name' => 'default',
        ], $defaults);

        $connector->fill($defaults);

        if (! $connector->installation_id) {
            $connector->installation_id = $this->resolveInstallationId();
        }

        if (! $connector->fingerprint_hash) {
            $connector->fingerprint_hash = $this->buildFingerprintHash($connector->installation_id, $connector->product_code);
        }

        $connector->save();

        return $connector->fresh();
    }

    protected function hasRemoteConfiguration(LicenseConnector $connector): bool
    {
        return filled($connector->base_url)
            && filled($connector->product_code)
            && filled($connector->activation_key);
    }

    protected function hasFreshValidation(LicenseConnector $connector): bool
    {
        if (! $connector->last_validated_at || ! $connector->last_state) {
            return false;
        }

        $threshold = max((int) config('licensing.validate_interval_minutes', 15), 1);

        return $connector->last_validated_at->gt(now()->subMinutes($threshold));
    }

    protected function synchronizeRemoteState(LicenseConnector $connector): LicenseConnector
    {
        return DB::transaction(function () use ($connector) {
            $workingConnector = LicenseConnector::query()->lockForUpdate()->findOrFail($connector->id);
            $statePayload = null;

            if ($workingConnector->activation_id && $workingConnector->access_token) {
                try {
                    $statePayload = $this->client->status($workingConnector->base_url, $workingConnector->access_token);
                } catch (Throwable $exception) {
                    $statePayload = $this->recoverRemoteState($workingConnector, $exception);
                }
            } else {
                $statePayload = $this->activateConnector($workingConnector);
            }

            $state = $statePayload['state'] ?? null;
            if (! is_array($state)) {
                throw new RuntimeException('License server response did not contain a usable state payload.');
            }

            $workingConnector->forceFill([
                'last_state' => $state,
                'last_status' => $state['status'] ?? 'unknown',
                'is_valid' => (bool) ($state['canRun'] ?? false),
                'last_validated_at' => now(),
                'last_error' => null,
                'last_error_at' => null,
            ])->save();

            $this->syncFeatures($workingConnector, $state['activeModules'] ?? []);
            $this->storeValidation($workingConnector, 'licensed', 'remote_valid', null);

            return $workingConnector->fresh();
        });
    }

    protected function activateConnector(LicenseConnector $connector): array
    {
        $response = $this->client->activate($connector->base_url, [
            'activationKey' => $connector->activation_key,
            'productCode' => $connector->product_code,
            'device' => [
                'installationId' => $connector->installation_id,
                'fingerprintHash' => $connector->fingerprint_hash,
                'hostname' => gethostname() ?: php_uname('n'),
                'displayName' => config('licensing.device_name'),
                'platform' => PHP_OS_FAMILY,
                'appVersion' => app()->version(),
            ],
        ]);

        $connector->forceFill([
            'activation_id' => $response['activationId'] ?? $connector->activation_id,
            'access_token' => data_get($response, 'tokens.accessToken'),
            'refresh_token' => data_get($response, 'tokens.refreshToken'),
            'last_heartbeat_at' => now(),
        ])->save();

        return $response;
    }

    protected function recoverRemoteState(LicenseConnector $connector, Throwable $exception): array
    {
        if ($connector->refresh_token) {
            try {
                $refresh = $this->client->refreshToken($connector->base_url, $connector->refresh_token);
                $newAccessToken = data_get($refresh, 'tokens.accessToken');
                $newRefreshToken = data_get($refresh, 'tokens.refreshToken', $connector->refresh_token);

                $connector->forceFill([
                    'access_token' => $newAccessToken,
                    'refresh_token' => $newRefreshToken,
                ])->save();

                return $this->client->status($connector->base_url, $newAccessToken);
            } catch (Throwable) {
                // fall through to a new activation attempt
            }
        }

        return $this->activateConnector($connector);
    }

    protected function recordFreeEdition(LicenseConnector $connector, string $reason, ?string $errorMessage = null): array
    {
        $connector->forceFill([
            'last_status' => 'free',
            'is_valid' => false,
            'last_error' => $errorMessage,
            'last_error_at' => $errorMessage ? now() : $connector->last_error_at,
        ])->save();

        $this->storeValidation($connector, 'free', $reason, $errorMessage);

        return [
            'product' => [
                'name' => config('licensing.product_name'),
                'code' => config('licensing.product_code'),
            ],
            'license' => [
                'edition' => 'free',
                'is_valid' => false,
                'status' => 'free',
                'reason' => $reason,
                'connector' => [
                    'name' => $connector->name,
                    'driver' => $connector->driver,
                    'base_url' => $connector->base_url,
                ],
                'state' => $connector->last_state,
                'features' => [],
            ],
            'free_edition_notice' => [
                'enabled' => (bool) config('licensing.free_edition_notice.enabled', true),
                'text' => config('licensing.free_edition_notice.text'),
            ],
            'checked_at' => now()->toIso8601String(),
        ];
    }

    protected function buildResponse(LicenseConnector $connector, string $reason): array
    {
        $licensed = (bool) $connector->is_valid;

        return [
            'product' => [
                'name' => config('licensing.product_name'),
                'code' => config('licensing.product_code'),
            ],
            'license' => [
                'edition' => $licensed ? 'licensed' : 'free',
                'is_valid' => $licensed,
                'status' => $connector->last_status ?? ($licensed ? 'active' : 'free'),
                'reason' => $reason,
                'connector' => [
                    'name' => $connector->name,
                    'driver' => $connector->driver,
                    'base_url' => $connector->base_url,
                ],
                'state' => $connector->last_state,
                'features' => $connector->features()->orderBy('code')->get(['code', 'name', 'is_enabled', 'payload']),
            ],
            'free_edition_notice' => [
                'enabled' => ! $licensed && (bool) config('licensing.free_edition_notice.enabled', true),
                'text' => config('licensing.free_edition_notice.text'),
            ],
            'checked_at' => optional($connector->last_validated_at)->toIso8601String(),
        ];
    }

    protected function storeValidation(LicenseConnector $connector, string $edition, string $reason, ?string $errorMessage): void
    {
        $connector->validations()->create([
            'mode' => config('licensing.mode', 'free_fallback'),
            'edition' => $edition,
            'result_status' => $connector->last_status,
            'is_valid' => (bool) $connector->is_valid,
            'reason' => $reason,
            'state' => $connector->last_state,
            'error_message' => $errorMessage,
            'validated_at' => now(),
        ]);
    }

    protected function syncFeatures(LicenseConnector $connector, array $featureCodes): void
    {
        $featureCodes = collect($featureCodes)
            ->filter(fn ($value) => is_string($value) && $value !== '')
            ->values();

        $existingCodes = $connector->features()->pluck('code');

        foreach ($featureCodes as $code) {
            LicenseFeature::query()->updateOrCreate(
                [
                    'license_connector_id' => $connector->id,
                    'code' => $code,
                ],
                [
                    'name' => str($code)->replace(['-', '_'], ' ')->title()->toString(),
                    'source' => 'license_server',
                    'is_enabled' => true,
                    'payload' => ['code' => $code],
                    'last_synced_at' => now(),
                ]
            );
        }

        $staleCodes = $existingCodes->diff($featureCodes);
        if ($staleCodes->isNotEmpty()) {
            $connector->features()->whereIn('code', $staleCodes)->update([
                'is_enabled' => false,
                'last_synced_at' => now(),
            ]);
        }
    }

    protected function resolveInstallationId(): string
    {
        $configured = trim((string) config('licensing.installation_id'));
        if ($configured !== '') {
            return $configured;
        }

        $seed = implode('|', [
            config('app.url'),
            config('licensing.product_code'),
            gethostname() ?: php_uname('n'),
        ]);

        return 'forms-hub-' . substr(hash('sha256', $seed), 0, 16);
    }

    protected function buildFingerprintHash(string $installationId, string $productCode): string
    {
        $seed = implode('|', [
            $installationId,
            $productCode,
            config('app.url'),
            gethostname() ?: php_uname('n'),
        ]);

        return hash('sha256', $seed);
    }
}