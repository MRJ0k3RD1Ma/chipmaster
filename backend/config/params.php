<?php

return [
    'adminEmail' => 'admin@example.com',

    // JWT sozlamalari
    'jwt' => [
        'secret' => 'your-secret-key-here-change-in-production',
        'algorithm' => 'HS256',
        'accessTokenExpire' => 3600,       // 1 soat
        'refreshTokenExpire' => 2592000,   // 30 kun
    ],
];
