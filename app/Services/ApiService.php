<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url');
        $this->timeout = config('api.timeout', 30);
    }

    protected function getToken(): ?string
    {
        return Session::get('api_token');
    }

    protected function client()
    {
        $request = Http::timeout($this->timeout)->acceptJson();

        if ($token = $this->getToken()) {
            $request = $request->withToken($token);
        }

        return $request;
    }

    public function get(string $path, array $query = []): array
    {
        try {
            $response = $this->client()->get($this->baseUrl . $path, $query);

            return $this->handleResponse($response);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function post(string $path, array $data = []): array
    {
        try {
            $response = $this->client()->post($this->baseUrl . $path, $data);

            return $this->handleResponse($response);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function put(string $path, array $data = []): array
    {
        try {
            $response = $this->client()->put($this->baseUrl . $path, $data);

            return $this->handleResponse($response);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function delete(string $path): array
    {
        try {
            $response = $this->client()->delete($this->baseUrl . $path);

            return $this->handleResponse($response);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function download(string $path)
    {
        $response = $this->client()->get($this->baseUrl . $path);

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'))
            ->header('Content-Disposition', $response->header('Content-Disposition'));
    }

    public function stream(string $path)
    {
        $response = $this->client()->get($this->baseUrl . $path);

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'));
    }

    protected function handleResponse($response): array
    {
        $json = $response->json() ?? [];

        return [
            'success' => (bool) ($json['success'] ?? $response->successful()),
            'status' => $response->status(),
            'data' => $json['data'] ?? null,
            'message' => $json['message'] ?? null,
            'errors' => $json['errors'] ?? null,
            'raw' => $json,
        ];
    }

    protected function errorResponse(\Throwable $e): array
    {
        return [
            'success' => false,
            'status' => 500,
            'data' => null,
            'message' => 'Tidak dapat terhubung ke server: ' . $e->getMessage(),
            'errors' => null,
            'raw' => [],
        ];
    }
}