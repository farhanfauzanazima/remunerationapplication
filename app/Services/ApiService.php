<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url', 'http://localhost:8000/api');
        $this->timeout = config('api.timeout', 30);
    }

    // ─── Get token dari session ────────────────────────────
    private function getToken(): ?string
    {
        return Session::get('api_token');
    }

    // ─── Build HTTP client ─────────────────────────────────
    private function client(bool $withAuth = true)
    {
        $client = Http::timeout($this->timeout)
            ->acceptJson()
            ->contentType('application/json');

        if ($withAuth && $this->getToken()) {
            $client = $client->withToken($this->getToken());
        }

        return $client;
    }

    // ─── GET request ──────────────────────────────────────
    public function get(string $endpoint, array $params = [], bool $withAuth = true): array
    {
        try {
            $response = $this->client($withAuth)
                ->get($this->baseUrl . $endpoint, $params);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // ─── POST request ─────────────────────────────────────
    public function post(string $endpoint, array $data = [], bool $withAuth = true): array
    {
        try {
            $response = $this->client($withAuth)
                ->post($this->baseUrl . $endpoint, $data);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // ─── PUT request ──────────────────────────────────────
    public function put(string $endpoint, array $data = [], bool $withAuth = true): array
    {
        try {
            $response = $this->client($withAuth)
                ->put($this->baseUrl . $endpoint, $data);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // ─── DELETE request ───────────────────────────────────
    public function delete(string $endpoint, bool $withAuth = true): array
    {
        try {
            $response = $this->client($withAuth)
                ->delete($this->baseUrl . $endpoint);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // ─── Download file (PDF) ──────────────────────────────
    public function download(string $endpoint): ?\Illuminate\Http\Response
    {
        try {
            $response = Http::timeout(60)
                ->withToken($this->getToken())
                ->get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                $contentType        = $response->header('Content-Type') ?? 'application/pdf';
                $contentDisposition = $response->header('Content-Disposition') ?? 'attachment; filename="file.pdf"';
                $filename           = 'download.pdf';

                if (preg_match('/filename[^;=\n]*=([\'"](.*)[\'"]|([^\n]*))/', $contentDisposition, $matches)) {
                    $filename = trim($matches[2] ?? $matches[3] ?? $filename, " \t\n\r\0\x0B\"'");
                }

                return response($response->body(), 200, [
                    'Content-Type'        => $contentType,
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // ─── Stream file (Preview PDF) ────────────────────────
    public function stream(string $endpoint): ?\Illuminate\Http\Response
    {
        try {
            $response = Http::timeout(60)
                ->withToken($this->getToken())
                ->get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                return response($response->body(), 200, [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="preview.pdf"',
                ]);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // ─── Handle response ──────────────────────────────────
    private function handleResponse(Response $response): array
    {
        $data = $response->json() ?? [];

        return [
            'success'     => $response->successful(),
            'status'      => $response->status(),
            'data'        => $data['data'] ?? null,
            'message'     => $data['message'] ?? null,
            'errors'      => $data['errors'] ?? null,
            'raw'         => $data,
        ];
    }

    // ─── Error response ───────────────────────────────────
    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'status'  => 500,
            'data'    => null,
            'message' => 'Koneksi ke server gagal: ' . $message,
            'errors'  => null,
            'raw'     => [],
        ];
    }
}
