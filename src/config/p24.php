<?php

return [
    'merchant_id' => env('P24_MERCHANT_ID', 0),
    'pos_id' => env('P24_POS_ID', 0),
    'crc' => env('P24_CRC', null),
    'mode' => env('P24_MODE', 'live'),          // `sandbox` || `live`
    'route_return' => null,                     // provide route name where Client should be redirected on transaction cancellation
];
