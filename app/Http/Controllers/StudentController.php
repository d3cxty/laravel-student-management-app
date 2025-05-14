<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index()
    {
        try {
            $students = Student::all();
            return view('student', compact('students')); // Updated to 'student'
        } catch (\Exception $e) {
            \Log::error('Failed to load students or view: ' . $e->getMessage());
            return response()->view('errors.missing-view', [
                'error' => 'Failed to load student view. Please contact support.',
            ], 500);
        }
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'class' => 'required|string|max:50',
            'marks' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $student = Student::create($request->only(['name', 'class', 'marks']));
            return response()->json([
                'success' => true,
                'student' => $student,
                'message' => 'Student created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student',
            ], 500);
        }
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'class' => 'required|string|max:50',
            'marks' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $student->update($request->only(['name', 'class', 'marks']));
            return response()->json([
                'success' => true,
                'student' => $student,
                'message' => 'Student updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student',
            ], 500);
        }
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student): JsonResponse
    {
        try {
            $student->delete();
            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
            ], 500);
        }
    }
}