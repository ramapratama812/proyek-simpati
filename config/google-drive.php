<?php

return [
  'scope' => env('GOOGLE_DRIVE_SCOPE'),
  'redirect_uri' => env('GOOGLE_DRIVE_REDIRECT_URI'),

  'picker' => [
    'api_key' => env('GOOGLE_PICKER_API_KEY'),
    'app_id'  => env('GOOGLE_PICKER_APP_ID'),
  ],
];
