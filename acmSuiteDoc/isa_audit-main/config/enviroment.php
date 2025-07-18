<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Get global settings
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default all settings for use in system
    |
    */
    'domain_frontend' => env('DOMAIN_FRONTEND', ''),
    
    'production_storage' => env('PRODUCTION_STORAGE', false),

    'view_image_production_in_dev' => env('VIEW_IMAGE_PRODUCTION_IN_DEV', false),

    'aws_url_view' => env('AWS_URL_VIEW', ''),

    'time_zone_carbon' => env('TIME_ZONE_CARBON', 'America/Mexico_City')
];