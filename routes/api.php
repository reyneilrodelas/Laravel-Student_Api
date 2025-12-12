<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\StudentController;


// ============================================
// TEST ROUTE
// ============================================
// Check if the API is working and accessible
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// ============================================
// STUDENT ENDPOINTS
// ============================================
// GET /students - Retrieve a list of all students with their information
Route::get('/students', [StudentController::class, 'index']);

// GET /students/{id} - Retrieve detailed information about a specific student by ID
Route::get('/students/{id}', [StudentController::class, 'show']);

// POST /students - Create a new student record
// Required: first_name, last_name, email, dob, phone_number, address
Route::post('/students', [StudentController::class, 'store']);

// PUT /students/{id} - Update an existing student's information by ID (all or partial fields)
Route::put('/students/{id}', [StudentController::class, 'update']);

// DELETE /students/{id} - Delete a student record by ID
Route::delete('/students/{id}', [StudentController::class, 'destroy']);

// ============================================
// COURSE ENDPOINTS
// ============================================
// GET /courses - Retrieve a list of all available courses
Route::get('/courses', [CourseController::class, 'index']);

// GET /courses/{id} - Retrieve detailed information about a specific course by ID
Route::get('/courses/{id}', [CourseController::class, 'show']);

// POST /courses - Create a new course
// Required: course_name, course_code, credits | Optional: description
Route::post('/courses', [CourseController::class, 'store']);

// PUT /courses/{id} - Update an existing course's information by ID
Route::put('/courses/{id}', [CourseController::class, 'update']);

// DELETE /courses/{id} - Delete a course by ID
Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

// ============================================
// ENROLLMENT ENDPOINTS
// ============================================
// GET /enrollments - Retrieve all enrollment records with student and course relationships
Route::get('/enrollments', [EnrollmentController::class, 'index']);

// GET /enrollments/{id} - Retrieve a specific enrollment record by ID
Route::get('/enrollments/{id}', [EnrollmentController::class, 'show']);

// POST /enrollments - Enroll a student in a course
// Required: student_id, course_id | Optional: enrollment_date (defaults to today), status (defaults to 'enrolled')
Route::post('/enrollments', [EnrollmentController::class, 'store']);

// PUT /enrollments/{id} - Update an enrollment record (enrollment_date and status only)
// Note: Cannot change student_id or course_id
Route::put('/enrollments/{id}', [EnrollmentController::class, 'update']);

// DELETE /enrollments/{id} - Remove an enrollment record by ID
Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy']);

// ============================================
// SPECIAL ENROLLMENT ROUTES
// ============================================
// GET /students/{id}/courses - Get all courses that a specific student is enrolled in
Route::get('/students/{id}/courses', [EnrollmentController::class, 'getStudentCourses']);

// GET /courses/{id}/students - Get all students enrolled in a specific course
Route::get('/courses/{id}/students', [EnrollmentController::class, 'getCourseStudents']);

