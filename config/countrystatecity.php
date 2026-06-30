<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CountryStateCity API Keys
    |--------------------------------------------------------------------------
    |
    | Define your API keys as a comma-separated string in your .env file.
    | The system will automatically rotate between them when rate limits (100/day)
    | are reached for a specific key.
    |
    */
    'api_keys' => array_filter(array_map('trim', explode(',', env('CSC_API_KEYS', '')))),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the CountryStateCity API.
    |
    */
    'base_url' => env('CSC_API_BASE_URL', 'https://api.countrystatecity.in/v1'),
];
