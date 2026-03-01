<?php

namespace App\Services;

use App\Models\MetaApiToken;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaApiService
{
    protected string $baseUrl;
    protected string $adminToken;
    protected ?string $clientToken = null;
    protected int $timeout = 15;

    public function __construct()
    {
        $this->baseUrl    = rtrim(config('services.meta_api.url'), '/');
        $this->adminToken = config('services.meta_api.admin_token');
    }

    /*
    |--------------------------------------------------------------------------
    | Client Token — inject dari database
    |--------------------------------------------------------------------------
    */

    /**
     * Set client token dari model MetaApiToken.
     */
    public function setClientToken(MetaApiToken $metaApiToken): static
    {
        throw_unless(
            $metaApiToken->isValid(),
            \RuntimeException::class,
            "Token '{$metaApiToken->client_name}' tidak aktif atau sudah expired."
        );

        $this->clientToken = $metaApiToken->token;

        return $this;
    }

    /**
     * Set client token langsung dari user (ambil token aktif milik user).
     */
    public function forUser(\App\Models\User $user): static
    {
        $token = MetaApiToken::where('user_id', $user->id)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expired_at')->orWhere('expired_at', '>', now()))
            ->latest()
            ->firstOrFail();

        return $this->setClientToken($token);
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Helper
    |--------------------------------------------------------------------------
    */

    protected function http(string $token = null)
    {
        return Http::timeout($this->timeout)
            ->withToken($token ?? $this->adminToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->acceptJson();
    }

    protected function call(string $method, string $path, array $data = [], string $token = null): array
    {
        $url = $this->baseUrl . $path;

        try {
            $response = match (strtoupper($method)) {
                'GET'    => $this->http($token)->get($url, $data),
                'POST'   => $this->http($token)->post($url, $data),
                'PATCH'  => $this->http($token)->patch($url, $data),
                'DELETE' => empty($data)
                    ? $this->http($token)->delete($url)
                    : $this->http($token)->withBody(json_encode($data), 'application/json')->delete($url),
                default  => throw new \InvalidArgumentException("Method tidak dikenal: {$method}"),
            };

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'data'    => $response->json() ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('[MetaApiService] Request gagal', [
                'method' => $method,
                'url'    => $url,
                'error'  => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status'  => 500,
                'data'    => ['message' => 'Koneksi ke Meta API gagal: ' . $e->getMessage()],
            ];
        }
    }

    protected function clientCall(string $method, string $path, array $data = []): array
    {
        throw_if(
            empty($this->clientToken),
            \RuntimeException::class,
            'Client token belum di-set. Panggil forUser() atau setClientToken() terlebih dahulu.'
        );

        return $this->call($method, $path, $data, $this->clientToken);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔑 Token Management  (Admin)
    |--------------------------------------------------------------------------
    */

    public function listTokens(): array
    {
        return $this->call('GET', '/admin/tokens');
    }

    public function getToken(int $id): array
    {
        return $this->call('GET', "/admin/tokens/{$id}");
    }

    public function createToken(array $payload): array
    {
        return $this->call('POST', '/admin/tokens', $payload);
    }

    public function updateToken(int $id, array $payload): array
    {
        return $this->call('PATCH', "/admin/tokens/{$id}", $payload);
    }

    public function deleteToken(int $id): array
    {
        return $this->call('DELETE', "/admin/tokens/{$id}");
    }

    public function activateToken(int $id): array
    {
        return $this->updateToken($id, ['is_active' => 1]);
    }

    public function deactivateToken(int $id): array
    {
        return $this->updateToken($id, ['is_active' => 0]);
    }

    /*
    |--------------------------------------------------------------------------
    | 📱 Session Management  (Admin)
    |--------------------------------------------------------------------------
    */

    public function listSessions(int $tokenId): array
    {
        return $this->call('GET', "/admin/tokens/{$tokenId}/sessions");
    }

    public function assignSessions(int $tokenId, array $sessions): array
    {
        return $this->call('POST', "/admin/tokens/{$tokenId}/sessions", compact('sessions'));
    }

    public function revokeSessions(int $tokenId, array $sessions): array
    {
        return $this->call('DELETE', "/admin/tokens/{$tokenId}/sessions", compact('sessions'));
    }

    /*
    |--------------------------------------------------------------------------
    | 📊 Access Logs  (Admin)
    |--------------------------------------------------------------------------
    */

    public function getLogs(array $filters = []): array
    {
        return $this->call('GET', '/admin/logs', $filters);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔐 Login & Sesi  (Client)
    |--------------------------------------------------------------------------
    */

    public function loginMeta(string $sessionName): array
    {
        return $this->clientCall('POST', '/login-meta', [
            'sessionName' => $sessionName,
        ]);
    }

    public function loginCookies(string $sessionName, array $cookies): array
    {
        return $this->clientCall('POST', '/login-cookies', [
            'sessionName' => $sessionName,
            'cookies'     => $cookies,
        ]);
    }

    public function checkSession(string $sessionName): array
    {
        return $this->clientCall('GET', '/check-session', [
            'sessionName' => $sessionName,
        ]);
    }

    public function listClientSessions(): array
    {
        return $this->clientCall('GET', '/list-sessions');
    }

    /*
    |--------------------------------------------------------------------------
    | 📅 Schedule Konten  (Client)
    |--------------------------------------------------------------------------
    */

    public function schedule(string $sessionName, array $tasks): array
    {
        return $this->clientCall('POST', '/schedule', [
            'sessionName' => $sessionName,
            'tasks'       => $tasks,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔎 Utilitas  (Client)
    |--------------------------------------------------------------------------
    */

    public function checkAsset(string $sessionName, string $assetId): array
    {
        return $this->clientCall('POST', '/check-asset', [
            'sessionName' => $sessionName,
            'assetId'     => $assetId,
        ]);
    }

    public function checkBusiness(string $sessionName): array
    {
        return $this->clientCall('POST', '/check-business', [
            'sessionName' => $sessionName,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 📋 Cek Konten  (Client)
    |--------------------------------------------------------------------------
    */

    public function checkPosts(string $sessionName, string $assetId): array
    {
        return $this->clientCall('POST', '/check-posts', [
            'sessionName' => $sessionName,
            'assetId'     => $assetId,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 📊 Status & Monitor  (Client)
    |--------------------------------------------------------------------------
    */

    public function getBotStatus(): array
    {
        return $this->clientCall('GET', '/status');
    }
}
