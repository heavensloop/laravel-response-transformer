<?php

return [
    'location' => 'app/Transformers',
    'input_type' => env('TRANSFORM_INPUT_TYPE', 'route'), // route, query-string, header
    'parameter_name' => env('TRANSFORM_INPUT_NAME', 'transformation')
];
