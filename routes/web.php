<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;


Route::get('/login', function(){
    return view('login');
});


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/user', function () {
    return view('userform');
});

// Profile management
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

// Settings page
Route::get('/settings', function () {
    return view('settings');
})->name('settings');


Route::get('/courses', [App\Http\Controllers\CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/create', [App\Http\Controllers\CourseController::class, 'create'])->name('courses.create');
Route::post('/courses', [App\Http\Controllers\CourseController::class, 'store'])->name('courses.store');
Route::get('/courses/{id}/edit', [App\Http\Controllers\CourseController::class, 'edit'])->name('courses.edit');
Route::put('/courses/{id}', [App\Http\Controllers\CourseController::class, 'update'])->name('courses.update');
Route::delete('/courses/{id}', [App\Http\Controllers\CourseController::class, 'destroy'])->name('courses.destroy');
Route::get('/payment', [PaymentController::class, 'payment']);

// Reports - payments report view
Route::get('/reports/payments', [PaymentController::class, 'report'])->name('reports.payments');


Route::get('/courses/{course}/lessons', [LessonController::class, 'index'])
    ->name('courses.lessons');

Route::post('/lessons', [LessonController::class, 'store'])
    ->name('lessons.store');

Route::put('/lessons/{id}', [LessonController::class, 'update'])
    ->name('lessons.update');

Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])
    ->name('lessons.destroy');

Route::post('/lessons/upload-chunk', [LessonController::class, 'uploadChunk'])
    ->name('lessons.uploadChunk');
    
