<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have some users and courses
        if (User::count() == 0) {
            User::factory(5)->create();
        }
        
        if (Course::count() == 0) {
            Course::create([
                'title' => 'Laravel Mastery',
                'teacher' => 'John Doe',
                'description' => 'Master Laravel in 30 days.',
                'price' => 49.99
            ]);
            Course::create([
                'title' => 'Flutter for Beginners',
                'teacher' => 'Jane Smith',
                'description' => 'Build cross-platform apps.',
                'price' => 29.99
            ]);
        }

        $users = User::all();
        $courses = Course::all();

        for ($i = 0; $i < 40; $i++) {
            $course = $courses->random();
            $user = $users->random();
            
            // Generate a random date within the last 6 months
            $date = Carbon::now()->subDays(rand(0, 180));

            Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'md5' => Str::random(32),
                'amount' => $course->price ?? rand(10, 100),
                'currency' => 'USD',
                'status' => 'paid',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
