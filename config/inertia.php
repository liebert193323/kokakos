<?php

return [
    // ...existing code...
    
    'middleware' => ['web'],
    
    'ssr' => [
        'enabled' => false,
    ],

    // Set the root template that's loaded on the first page visit
    'template' => 'app'
];
