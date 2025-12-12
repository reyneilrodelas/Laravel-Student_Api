<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * List all enrollments
     */
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'course'])->get();

        if ($enrollments->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No enrollments found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Enrollments retrieved successfully',
            'data' => $enrollments
        ], 200);
    }

    /**
     * Show a single enrollment
     */
    public function show($id)
    {
        $enrollment = Enrollment::with(['student', 'course'])->find($id);

        if (!$enrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Enrollment not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Enrollment retrieved successfully',
            'data' => $enrollment
        ], 200);
    }

    /**
     * Enroll a student in a course
     */
    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|in:enrolled,completed,dropped'
        ]);

        // Check if enrollment already exists
        $existingEnrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'Student is already enrolled in this course',
                'enrollment' => $existingEnrollment
            ], 409);
        }

        $enrollment = Enrollment::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'enrollment_date' => $validated['enrollment_date'] ?? now(),
            'status' => $validated['status'] ?? 'enrolled'
        ]);

        return response()->json([
            'message' => 'Student enrolled successfully',
            'enrollment' => $enrollment
        ], 201);
    }

    /**
     * Store an enrollment (alias for enroll)
     */
    public function store(Request $request)
    {
        return $this->enroll($request);
    }

    /**
     * Update an enrollment
     */
    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Enrollment not found'
            ], 404);
        }

        $validated = $request->validate([
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|in:enrolled,completed,dropped'
        ]);

        $enrollment->update([
            'enrollment_date' => $validated['enrollment_date'] ?? $enrollment->enrollment_date,
            'status' => $validated['status'] ?? $enrollment->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Enrollment updated successfully',
            'data' => $enrollment
        ], 200);
    }

    /**
     * Delete an enrollment
     */
    public function destroy($id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Enrollment not found'
            ], 404);
        }

        $enrollment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Enrollment deleted successfully'
        ], 200);
    }

    /**
     * Get all courses a student is enrolled in
     */
    public function getStudentCourses($id)
    {
        $student = Student::findOrFail($id);

        $courses = $student->courses()
            ->withPivot('enrollment_date', 'status')
            ->get();

        return response()->json([
            'student' => $student,
            'courses' => $courses
        ]);
    }

    /**
     * Get all students enrolled in a course
     */
    public function getCourseStudents($id)
    {
        $course = Course::findOrFail($id);

        $students = $course->students()
            ->withPivot('enrollment_date', 'status')
            ->get();

        return response()->json([
            'course' => $course,
            'students' => $students
        ]);
    }
}