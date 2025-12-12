<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        
        return response()->json($students);
    }

    public function show($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json($student);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:students,email',
            'dob' => 'required|string',
            'phone_number' => 'required|string',
            'address' => 'required|string',
        ]);

        $student = Student::create($validated);

        return response()->json($student, 201);
    }

    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:students,email,' . $student->id,
            'dob' => 'sometimes|required|string',
            'phone_number' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
        ]);

        $student->update($validated);

        return response()->json($student);
    }

    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $student->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }
}
