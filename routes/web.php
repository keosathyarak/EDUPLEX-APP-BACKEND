<?php
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;


Route::get('/login', function(){
    return view('login');
});


Route::get('/', function () {
    return view('dashboard');
});
Route::get('/user', function () {
    return view('userform');
});


Route::get('/courses', [App\Http\Controllers\CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/create', [App\Http\Controllers\CourseController::class, 'create'])->name('courses.create');
Route::post('/courses', [App\Http\Controllers\CourseController::class, 'store'])->name('courses.store');
Route::get('/courses/{id}/edit', [App\Http\Controllers\CourseController::class, 'edit'])->name('courses.edit');
Route::put('/courses/{id}', [App\Http\Controllers\CourseController::class, 'update'])->name('courses.update');
Route::delete('/courses/{id}', [App\Http\Controllers\CourseController::class, 'destroy'])->name('courses.destroy');
Route::get('/payment', [PaymentController::class, 'payment']);


Route::get('/courses/{course}/lessons', [LessonController::class, 'index'])
    ->name('courses.lessons');

Route::post('/lessons', [LessonController::class, 'store'])
    ->name('lessons.store');

Route::put('/lessons/{id}', [LessonController::class, 'update'])
    ->name('lessons.update');

Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])
    ->name('lessons.destroy');
    
