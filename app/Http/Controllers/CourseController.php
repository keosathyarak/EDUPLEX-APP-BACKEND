<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('lessons')
                        ->latest()
                        ->paginate(10);

        return view('course', compact('courses'));
    }

    public function create()
    {
        return redirect()->route('courses.index');
    }

    public function edit($id)
    {
        return redirect()->route('courses.index')->with('success', 'Use the edit button on the course list to update courses.');
    }

    public function list_courses()
    {
        $courses = Course::withCount('lessons')
                        ->latest()
                        ->get();

        return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Course Store Method Called', ['request' => $request->all()]);
        try {
            $request->validate([
                'title'   => 'required|max:255',
                'teacher' => 'required|max:255',
                'description' => 'nullable|string',
                'price'   => 'nullable|numeric|min:0',
                'image'   => 'nullable|image|max:2048'
            ]);

            $data = [
                'title'   => $request->title,
                'teacher' => $request->teacher,
                'description' => $request->description,
                'price'   => $request->filled('price') ? $request->price : null,
            ];

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('courses', 'public');
            }

            Course::create($data);

            return back()->with('success', 'Course created successfully');

        } catch (ValidationException $e) {
            Log::warning('Course Store Validation Failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Course Store Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'System Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Course Update Method Called', ['id' => $id, 'request' => $request->all()]);
        try {
            $course = Course::findOrFail($id);

            $request->validate([
                'title'   => 'required|max:255',
                'teacher' => 'required|max:255',
                'description' => 'nullable|string',
                'price'   => 'nullable|numeric|min:0',
                'image'   => 'nullable|image|max:2048'
            ]);

            $data = [
                'title'   => $request->title,
                'teacher' => $request->teacher,
                'description' => $request->description,
                'price'   => $request->filled('price') ? $request->price : null,
            ];

            if ($request->hasFile('image')) {
                if ($course->image) {
                    Storage::disk('public')->delete($course->image);
                }
                $data['image'] = $request->file('image')->store('courses', 'public');
            }

            $course->update($data);

            return back()->with('success', 'Course updated successfully');

        } catch (ValidationException $e) {
            Log::warning('Course Update Validation Failed', ['id' => $id, 'errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Course Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'System Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function list_lesson($id)
    {
        try {
            $course = Course::with(['lessons' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'course'  => $course,
                'lessons' => $course->lessons,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroy($id)
    {
        Log::info('Course Destroy Method Called', ['id' => $id]);
        try {
            $course = Course::findOrFail($id);

            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }

            $course->delete();

            return back()->with('success', 'Course deleted successfully');
        } catch (\Exception $e) {
            Log::error('Course Destroy Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete course: ' . $e->getMessage());
        }
    }
}
