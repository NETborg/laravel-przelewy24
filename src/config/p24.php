<?php

return [
    'merchant_id' => env('P24_MERCHANT_ID', 0),
    'pos_id' => env('P24_POS_ID', 0),
    'crc' => env('P24_CRC', null),
    'api_key' => env('P24_API_KEY', null),
    'mode' => env('P24_MODE', 'sandbox'),   // `sandbox` || `live`
    'route_return' => null,                             // provide route name where Client should be redirected on transaction cancellation
];
