<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Student Management API',
        'status' => 'active',
        'version' => '1.0',
        'endpoints' => [
            'test' => '/api/test',
            'students' => '/api/students',
            'courses' => '/api/courses',
            'enrollments' => '/api/enrollments',
        ],
        'documentation' => 'See API routes for complete endpoint list'
    ]);
});
