<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('lessons')
                        ->latest()
                        ->get();

        return view('course', compact('courses'));
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
        $request->validate([
            'title'   => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
            'price'   => 'nullable|numeric',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->only(['title','teacher','price']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('courses', 'public');
        }

        Course::create($data);

        return back()->with('success', 'Course created successfully');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title'   => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
            'price'   => 'nullable|numeric',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->only(['title','teacher','price']);

        if ($request->hasFile('image')) {

            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }

            $data['image'] = $request->file('image')
                ->store('courses', 'public');
        }

        $course->update($data);

        return back()->with('success', 'Course updated successfully');
    }
   public function list_lesson($id)
{
    $course = Course::with(['lessons' => function ($query) {
        $query->orderBy('created_at', 'asc');
    }])->findOrFail($id);

    return response()->json([
        'success' => true,
        'course'  => $course,
        'lessons' => $course->lessons,
    ]);
}

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete(); // lessons auto deleted because of cascade

        return back()->with('success', 'Course deleted successfully');
    }
}
