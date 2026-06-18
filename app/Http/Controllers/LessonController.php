<?php

namespace App\Http\Controllers;

use App\Models\VideoCourse as Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        try {
            if ($request->has('video_path')) {
                // Handle chunked upload finalization
                $videoPath = $request->video_path;
            } else {
                $request->validate([
                    'video' => 'required|file|mimes:mp4,mov,avi,mkv,webm'
                ]);
                $videoPath = $request->file('video')
                    ->store('lesson_videos', 'public');
            }

            Lesson::create([
                'course_id' => $request->course_id,
                'title'     => $request->title,
                'video'     => $videoPath
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Lesson Added Successfully']);
            }
            return back()->with('success', 'Lesson Added Successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Failed to add lesson: ' . $e->getMessage()])->withInput();
        }
    }

    // ================= UPDATE LESSON =================
    public function update(Request $request, $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $data = [
                'title' => $request->title
            ];

            if ($request->has('video_path')) {
                 // Delete old video if exists
                 if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
                    Storage::disk('public')->delete($lesson->video);
                }
                $data['video'] = $request->video_path;
            } elseif ($request->hasFile('video')) {
                $request->validate([
                    'video' => 'nullable|mimes:mp4,mov,avi|max:2048000'
                ]);

                // Delete old video if exists
                if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
                    Storage::disk('public')->delete($lesson->video);
                }

                $data['video'] = $request->file('video')
                    ->store('lesson_videos', 'public');
            }

            $lesson->update($data);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Lesson Updated Successfully']);
            }
            return back()->with('success', 'Lesson Updated Successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Failed to update lesson: ' . $e->getMessage()])->withInput();
        }
    }

    // ================= CHUNK UPLOAD =================
    public function uploadChunk(Request $request)
    {
        try {
            $file = $request->file('file');
            $chunkIndex = $request->input('chunkIndex');
            $totalChunks = $request->input('totalChunks');
            $identifier = $request->input('identifier'); 

            $tempPath = "chunks/{$identifier}";
            $chunkName = "chunk_{$chunkIndex}";

            // local disk is storage/app/private
            Storage::disk('local')->putFileAs($tempPath, $file, $chunkName);

            if ($chunkIndex == $totalChunks - 1) {
                Log::info('Chunk upload complete, merging...', ['identifier' => $identifier]);
                $finalPath = $this->mergeChunks($identifier, $totalChunks, $file->getClientOriginalExtension());
                return response()->json([
                    'completed' => true,
                    'path' => $finalPath
                ]);
            }

            return response()->json([
                'completed' => false,
                'chunkIndex' => $chunkIndex
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'completed' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function mergeChunks($identifier, $totalChunks, $extension)
    {
        $fileName = "lesson_videos/" . $identifier . "." . $extension;
        $finalPath = storage_path("app/public/" . $fileName);
        
        if (!file_exists(dirname($finalPath))) {
            mkdir(dirname($finalPath), 0755, true);
        }

        $fileHandle = fopen($finalPath, 'ab');

        try {
            for ($i = 0; $i < $totalChunks; $i++) {
                // Correct path based on local disk root (storage/app/private)
                $chunkPath = storage_path("app/private/chunks/{$identifier}/chunk_{$i}");
                
                if (!file_exists($chunkPath)) {
                    throw new \Exception("Chunk {$i} missing at {$chunkPath}");
                }

                $chunkContent = file_get_contents($chunkPath);
                fwrite($fileHandle, $chunkContent);
                unlink($chunkPath); 
            }
        } finally {
            fclose($fileHandle);
        }
        
        $tempDir = storage_path("app/private/chunks/{$identifier}");
        if (is_dir($tempDir)) {
            rmdir($tempDir);
        }

        return $fileName;
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
