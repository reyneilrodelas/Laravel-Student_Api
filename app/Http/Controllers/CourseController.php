<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
       $course = Course::all();

        if($course->isEmpty())   {
            return response()->json(['message' => 'No courses found'], 404);
        }

        return response()->json($course);
    }

    public function show($id)
    {
        $course = Course::where('id', $id)->first();

        if(!$course)   {
            return response()->json(['error' => 'Course not found'], 404);
        }

        return response()->json($course);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_name' => 'required|string',
            'course_code' => 'required|string',
            'description' => 'nullable|string',
            'credits' => 'required|integer',
        ]);

        $course = Course::create([
            'course_name' => $validatedData['course_name'],
            'course_code' => $validatedData['course_code'],
            'description' => $validatedData['description'] ?? null,
            'credits' => $validatedData['credits'],
        ]);

        return response()->json($course, 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'course_name' => 'required|string',
            'course_code' => 'required|string',
            'description' => 'nullable|string',
            'credits' => 'required|integer',
        ]);

        $course = Course::where('id', $id)->first();
        if(!$course)   {
            return response()->json(['error' => 'Not found'], 404);
        }

        $course->update([
            'course_name' => $validatedData['course_name'],
            'course_code' => $validatedData['course_code'],
            'description' => $validatedData['description'] ?? null,
            'credits' => $validatedData['credits'],
        ]);

        return response()->json($course->fresh());
    }

    public function destroy($id)
    {
        $course = Course::where('id', $id)->first();
        if(!$course)   {
            return response()->json(['error' => 'Not found'], 404);
        }

        $course->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }
}
