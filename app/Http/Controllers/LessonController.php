<?php

namespace App\Http\Controllers;

use App\Models\VideoCourse as Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    // ================= SHOW LESSON LIST =================
    public function index($courseId)
    {
        $course = Course::with('lessons')
            ->findOrFail($courseId);

        return view('lessons', compact('course'));
    }

    // ================= STORE LESSON =================
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'video'     => 'required|mimes:mp4,mov,avi'
        ]);

        $videoPath = $request->file('video')
            ->store('lesson_videos', 'public');

        Lesson::create([
            'course_id' => $request->course_id,
            'title'     => $request->title,
            'video'     => $videoPath
        ]);

        return back()->with('success', 'Lesson Added Successfully');
    }

    // ================= UPDATE LESSON =================
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'nullable|mimes:mp4,mov,avi|max:20480'
        ]);

        $data = [
            'title' => $request->title
        ];

        if ($request->hasFile('video')) {

            // Delete old video if exists
            if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
                Storage::disk('public')->delete($lesson->video);
            }

            $data['video'] = $request->file('video')
                ->store('lesson_videos', 'public');
        }

        $lesson->update($data);

        return back()->with('success', 'Lesson Updated Successfully');
    }

    // ================= DELETE LESSON =================
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
            Storage::disk('public')->delete($lesson->video);
        }

        $lesson->delete();

        return back()->with('success', 'Lesson Deleted Successfully');
    }
}
