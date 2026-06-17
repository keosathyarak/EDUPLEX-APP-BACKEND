<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CourseController;
use App\Models\Payment;







/*
|--------------------------------------------------------------------------
| CORS Preflight (IMPORTANT FOR FLUTTER WEB)
|--------------------------------------------------------------------------
*/
Route::options('/{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/adminregister', [AuthController::class, 'adminregister']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AuthController::class, 'index']);
    Route::get('/users/{id}', [AuthController::class, 'show']);
    Route::put('/users/{id}', [AuthController::class, 'update']);
    Route::delete('/users/{id}', [AuthController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:sanctum')->get('/check-purchase/{courseId}', function ($courseId) {

    $user = auth()->user();

    $purchased = Payment::all()->where('user_id', $user->id)
        ->where('course_id', $courseId)
        ->where('status', 'paid')
        ->count() > 0;
       

    return response()->json([
        'purchased' => $purchased
    ]);
});

Route::get('/courses', [CourseController::class, 'list_courses']);
Route::get('/courses/{id}', [CourseController::class, 'list_lesson']);


Route::middleware('auth:sanctum')->group(function () {
     Route::put('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/profile/upload-image', [AuthController::class, 'updateProfilePicture']);
    Route::post('/generate', [PaymentController::class, 'generate'])->name('generate');
    Route::post('/check', [PaymentController::class, 'check'])->name('check');

    // Admin notifications: recent successful payments for dashboard dropdown
    Route::get('/admin/recent-payments', [PaymentController::class, 'recentPayments'])->name('admin.recentPayments');

});
