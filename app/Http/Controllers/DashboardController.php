<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // General Totals
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalEarnings = Payment::where('status', 'paid')->sum('amount');

        // Period Earnings
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $earningsThisWeek = Payment::where('status', 'paid')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->sum('amount');

        $earningsThisMonth = Payment::where('status', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $earningsThisYear = Payment::where('status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Chart Data (Last 6 Months Earnings)
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $monthFormat = $isSqlite ? "strftime('%m', created_at)" : "DATE_FORMAT(created_at, '%m')";
        $monthLabelFormat = $isSqlite ? "strftime('%b', created_at)" : "DATE_FORMAT(created_at, '%b')";

        // Note: strftime %b is not supported in all sqlite versions consistently, 
        // using month number and mapping might be safer but let's try the common way or use PHP mapping.
        
        $chartData = Payment::select(
                DB::raw('SUM(amount) as total'),
                DB::raw($isSqlite ? "strftime('%m', created_at) as month_num" : "DATE_FORMAT(created_at, '%m') as month_num")
            )
            ->where('status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month_num')
            ->orderBy('month_num', 'ASC')
            ->get();

        $labels = $chartData->map(function($item) {
            return Carbon::createFromFormat('m', $item->month_num)->format('b');
        });
        
        // Actually, let's use a simpler approach that works everywhere: Fetch all and group in PHP
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $payments = Payment::where('status', 'paid')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->get();

        $chartData = $payments->groupBy(function($payment) {
            return $payment->created_at->format('M');
        })->map(function($monthPayments) {
            return $monthPayments->sum('amount');
        });

        // Ensure we have all last 6 months even if no sales
        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('M');
            $labels[] = $month;
            $data[] = $chartData->get($month, 0);
        }

        // Recent Payments
        $recentPayments = Payment::with(['user', 'course'])
            ->where('status', 'paid')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalUsers', 
            'totalCourses', 
            'totalEarnings', 
            'earningsThisWeek', 
            'earningsThisMonth', 
            'earningsThisYear',
            'labels',
            'data',
            'recentPayments'
        ));
    }
}
