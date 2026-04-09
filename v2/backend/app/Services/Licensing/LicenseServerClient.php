<?php

namespace App\Services\Licensing;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class LicenseServerClient
{
    public function activate(string $baseUrl, array $payload): array
    {
        return $this->request('post', $baseUrl, '/api/v1/device/activate', [
            'json' => $payload,
        ]);
    }

    public function status(string $baseUrl, string $accessToken): array
    {
        return $this->request('get', $baseUrl, '/api/v1/device/status', [
            'token' => $accessToken,
        ]);
    }

    public function heartbeat(string $baseUrl, string $accessToken): array
    {
        return $this->request('post', $baseUrl, '/api/v1/device/heartbeat', [
            'token' => $accessToken,
        ]);
    }

    public function refreshToken(string $baseUrl, string $refreshToken): array
    {
        return $this->request('post', $baseUrl, '/api/v1/device/refresh-token', [
            'json' => ['refreshToken' => $refreshToken],
        ]);
    }

    protected function request(string $method, string $baseUrl, string $path, array $options = []): array
    {
        $timeout = max((int) config('licensing.request_timeout', 5), 1);
        $url = rtrim($baseUrl, '/') . $path;

        $client = Http::timeout($timeout)
            ->acceptJson()
            ->asJson();

        if (! empty($options['token'])) {
            $client = $client->withToken($options['token']);
        }

        /** @var Response $response */
        $response = match (strtolower($method)) {
            'get' => $client->get($url),
            'post' => $client->post($url, $options['json'] ?? []),
            default => throw new RuntimeException('Unsupported license server method.'),
        };

        if ($response->failed()) {
            $body = $response->json();
            $message = data_get($body, 'error.message')
                ?? data_get($body, 'message')
                ?? $response->body();

            throw new RuntimeException(sprintf(
                'License server request failed (%s %s): %s',
                strtoupper($method),
                $path,
                $message,
            ));
        }

        return $response->json() ?? [];
    }
}