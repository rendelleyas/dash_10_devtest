<?php

use Illuminate\Support\Facades\Facade;

return [

    /**
     * API key for accessing the API
     *
     * @var string
     */
    'key' => env('API_KEY', ''),
    'endpoint' => env('API_END_POINT', 'https://www.zeald.com/developer-tests-api/x_endpoint/allblacks'),
];
