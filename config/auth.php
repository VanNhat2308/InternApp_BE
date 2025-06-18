<?php

return [

    'defaults' => [
        'guard' => 'api_sinhvien', // Guard mặc định (có thể đổi thành api_admin nếu muốn)
        'passwords' => 'sinhviens',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'sinhviens',
        ],

        'api_sinhvien' => [
            'driver' => 'jwt',
            'provider' => 'sinhviens',
        ],

        'api_admin' => [
            'driver' => 'jwt',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'sinhviens' => [
            'driver' => 'eloquent',
            'model' => App\Models\SinhVien::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'sinhviens' => [
            'provider' => 'sinhviens',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
