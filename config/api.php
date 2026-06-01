<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Backend Base URL
    |--------------------------------------------------------------------------
    | URL backend REST API. Sesuaikan jika port backend berubah.
    | Contoh: http://localhost:8001/api
    */
    'base_url' => env('API_BASE_URL', 'http://127.0.0.1:8000/api'),

    /*
    |--------------------------------------------------------------------------
    | API Timeout
    |--------------------------------------------------------------------------
    | Timeout dalam detik untuk setiap request ke backend API.
    */
    'timeout' => 30,
];