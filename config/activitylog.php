<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Activity Log Table
    |--------------------------------------------------------------------------
    |
    | The database table used to store activity logs.
    |
    */
    'table' => 'activity_logs',

    /*
    |--------------------------------------------------------------------------
    | Capture Request Data
    |--------------------------------------------------------------------------
    |
    | When enabled, the logger will automatically store the request
    | IP address and user agent (HTTP context only).
    |
    */
    'capture_request' => true,

    /*
    |--------------------------------------------------------------------------
    | Silent Failures
    |--------------------------------------------------------------------------
    |
    | When enabled, logging failures will be logged but won't throw exceptions.
    | When disabled, exceptions will be thrown if logging fails.
    |
    */
    'silent_failures' => true,
];

