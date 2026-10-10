<?php

return [
    'admin_emails' => array_values(array_filter(array_map('strtolower', array_map('trim', explode(',', (string) env('ADMIN_EMAILS', '')))))),
    'contact' => env('CRIME_TIMELINE_CONTACT'),
    'brave_api_key' => env('BRAVE_SEARCH_API_KEY'),
    'nine_router_api_key' => env('NINE_ROUTER_API_KEY'),
    'nine_router_endpoint' => env('NINE_ROUTER_ENDPOINT'),
    'nine_router_model' => env('NINE_ROUTER_MODEL'),
    'official_confidence' => (float) env('CRIME_TIMELINE_OFFICIAL_CONFIDENCE', 0.95),
    'backfill_start_year' => (int) env('CRIME_TIMELINE_BACKFILL_START_YEAR', now()->subYear()->year),
    'search' => [
        'endpoint' => 'https://api.search.brave.com/res/v1/web/search',
        'country' => 'ID',
        'results_per_query' => 20,
        'queries' => array_values(array_filter(array_map('trim', explode('|', (string) env('CRIME_TIMELINE_SEARCH_QUERIES', ''))))),
    ],
    'fetch' => ['max_bytes' => 1048576, 'timeout' => 15, 'max_redirects' => 3],
    'categories' => [],
    'kecamatan' => [],
];
