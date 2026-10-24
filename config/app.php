<?php
return [
    'name' => $_ENV['APP_NAME'] ?? 'DormFlow',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => $_ENV['APP_DEBUG'] ?? false,
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'locale' => 'vi',
];