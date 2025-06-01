<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
        return response()->json(['message' => '✅ Hello from Laravel 12 API!']);
    });
