<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'course'])->get();

        if ($enrollments->isEmpty()) {
            return response()->json(['message' => 'No enrollments found'], 404);
        }

        return response()->json($enrollments);
    }

    public function show($id)
    {
        $enrollment = Enrollment::with(['student', 'course'])->find($id);

        if (!$enrollment) {
            return response()->json(['error' => 'Enrollment not found'], 404);
        }

        return response()->json($enrollment);
    }

    // enroll student to course
    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|in:enrolled,completed,dropped'
        ]);

        // check if already enrolled
        $existingEnrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existingEnrollment) {
            return response()->json(['message' => 'Already enrolled', 'data' => $existingEnrollment], 409);
        }

        $enrollment = Enrollment::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'enrollment_date' => $validated['enrollment_date'] ?? now(),
            'status' => $validated['status'] ?? 'enrolled'
        ]);

        return response()->json($enrollment, 201);
    }

    public function store(Request $request)
    {
        return $this->enroll($request);
    }
    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|in:enrolled,completed,dropped'
        ]);

        $enrollment->update($validated);

        return response()->json($enrollment);
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $enrollment->delete();

        return response()->json(null, 204);
    }

    // get student's courses
    public function getStudentCourses($id)
    {
        $student = Student::findOrFail($id);

        $courses = $student->courses()
            ->withPivot('enrollment_date', 'status')
            ->get();

        return response()->json(['student' => $student, 'courses' => $courses]);
    }

    public function getCourseStudents($id)
    {
        $course = Course::findOrFail($id);
        $students = $course->students()->withPivot('enrollment_date', 'status')->get();

        return response()->json(['course' => $course, 'students' => $students]);
    }
}