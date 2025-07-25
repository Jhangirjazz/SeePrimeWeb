<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// if (!function_exists('seeprime_api')) {
//     function seeprime_api(array $params = [])
//     {
//         $baseUrl = config('services.seeprime_api.base_url');

//         if (!$baseUrl) {
//             throw new \Exception("SeePrime API base URL not configured.");
//         }

//         $response = Http::get($baseUrl, $params);

//         return $response->successful() ? $response->json() : [];
//     }
// }

function seeprime_api($params, $type = 'select') {
    $base = match($type) {
        'select' => env('SEEPRIME_API_SELECT'),
        'insert' => env('SEEPRIME_API_INSERT'),
        'update' => env('SEEPRIME_API_UPDATE'),
        default => env('SEEPRIME_API_SELECT'),
    };

    $response = $type === 'select'
        ? Http::get($base, $params)
        : Http::asForm()->post($base, $params);

    Log::info('API Raw Response: ' . $response->body());

    $data = $response->json();

     if (is_array($data)) {
        if (array_is_list($data)) {
            return $data; // multiple rows
        } else {
            return $data; // flat object like login
        }
    }

    Log::warning('Invalid API response, returning empty array: ' . $response->body());
    return [];
}


function seeprime_url(string $path = '', string $type = 'insert'): string
{
     if (str_starts_with($path, 'http')) {
        Log::warning('seeprime_url() called with full URL. Bypassing base.');
        return $path;
    }

    $base = match($type) {
        'select' => env('SEEPRIME_API_SELECT'),
        'insert' => env('SEEPRIME_API_INSERT'),
        'update' => env('SEEPRIME_API_UPDATE'),
        'delete' => env('SEEPRIME_API_DELETE'),
        'asset'  => env('SEEPRIME_ASSET_BASE'),
        default => env('SEEPRIME_API_SELECT'),
    };

    // ✅ Just clean trailing slashes, don't strip APIS folder
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}


// if(!function_exists('seeprime_url')){
//     function seeprime_url(string $path = ''): string
//     {   $apiBase = config('services.seeprime_api.base_url');
//     if(!$apiBase){
//             throw new \Exception("Seeprime url not configured."); 
//         }
//             $basePath = preg_replace('/\/APIS\/SELECT\.php$/', '', $apiBase);
//             return rtrim($basePath, '/') . '/' . ltrim($path, '/');
//     }
// }