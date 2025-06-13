<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('seeprime_api')) {
    function seeprime_api(array $params = [])
    {
        $baseUrl = config('services.seeprime_api.base_url');

        if (!$baseUrl) {
            throw new \Exception("SeePrime API base URL not configured.");
        }

        $response = Http::get($baseUrl, $params);

        return $response->successful() ? $response->json() : [];
    }
}
