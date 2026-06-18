<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Show payment page and course selector.
     */
    public function payment()
    {
        $courses = Course::latest()->get();

        return view('payment', compact('courses'));
    }

    /**
     * Generate KHQR and store pending payment
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'in:USD,KHR'],
            'billNumber' => ['nullable', 'string', 'max:50'],
        ]);

        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $baseUrl = config('services.khqr.base_url');
        $apiKey = config('services.khqr.key');

        if (!$baseUrl || !$apiKey) {
            \Log::error('KHQR Configuration missing');
            return response()->json([
                'success' => false,
                'message' => 'Payment service configuration missing',
            ], 500);
        }

        // Prepare data for KHQR API - exclude internal course_id
        $khqrData = [
            'currency' => $data['currency'],
            'billNumber' => $data['billNumber'] ?? ('INV-' . strtoupper(uniqid())),
        ];

        if (!empty($data['amount'])) {
            $khqrData['amount'] = (float) $data['amount'];
        }

        // Call KHQR generate API
        $res = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(rtrim($baseUrl, '/') . '/api/generate-qr', $khqrData);

        if (!$res->ok()) {
            \Log::error('KHQR Generation Failed', [
                'status' => $res->status(),
                'body' => $res->body(),
                'data_sent' => $khqrData
            ]);
            
            $errorMsg = 'QR generation failed';
            try {
                $errJson = $res->json();
                if (isset($errJson['message'])) {
                    $errorMsg .= ': ' . $errJson['message'];
                }
            } catch (\Exception $e) {}

            return response()->json([
                'success' => false,
                'message' => $errorMsg,
            ], 500);
        }

        $responseData = $res->json();

        if (!($responseData['success'] ?? false) || !isset($responseData['data']['md5Hash'])) {
            \Log::error('Invalid KHQR Response', ['response' => $responseData]);
            return response()->json([
                'success' => false,
                'message' => $responseData['message'] ?? 'Invalid QR response',
            ], 500);
        }

        // Store payment
        Payment::create([
            'user_id' => $userId,
            'course_id' => $data['course_id'],
            'md5' => $responseData['data']['md5Hash'],
            'amount' => $data['amount'] ?? null,
            'currency' => $data['currency'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'data' => $responseData['data'],
        ]);
    }

    /**
     * Securely validate payment
     */
    public function check(Request $request)
    {
        $payload = $request->validate([
            'md5' => ['required', 'string'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Verify ownership
        $payment = Payment::where('md5', $payload['md5'])
            ->where('user_id', $userId)
            ->where('course_id', $payload['course_id'])
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment reference'
            ], 403);
        }

        // If already paid
        if ($payment->status === 'paid') {
            return response()->json([
                'success' => true,
                'data' => [
                    'paid' => true,
                ]
            ]);
        }

        $baseUrl = config('services.khqr.base_url');
        $apiKey = config('services.khqr.key');

        if (!$baseUrl || !$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Payment service configuration missing'
            ], 500);
        }

        // Call KHQR check API
        $res = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(rtrim($baseUrl, '/') . '/api/check-payment', [
            'md5' => $payload['md5'],
        ]);

        if (!$res->ok()) {
            \Log::error('KHQR Check Payment Failed', [
                'status' => $res->status(),
                'body' => $res->body(),
                'md5' => $payload['md5']
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment'
            ], 500);
        }

        $paymentData = $res->json();

        $isPaid = isset($paymentData['success']) &&
          isset($paymentData['data']['paid']) &&
          $paymentData['success'] === true &&
          $paymentData['data']['paid'] === true;
        if ($isPaid) {
            $transaction = $paymentData['data']['transaction'] ?? null;

            DB::transaction(function () use ($payment) {
                $payment->update(['status' => 'paid']);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'paid' => true,
                    'transaction' => $transaction,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'paid' => false,
            ]
        ]);
    }

    /**
     * Show report of all successful payments.
     */
    public function report(Request $request)
    {
        $query = Payment::with(['user', 'course'])
            ->where('status', 'paid');

        // Apply Date Filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $totalAmount = (clone $query)->sum('amount');
        
        $payments = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('reports.payments', compact('payments', 'totalAmount'));
    }

    /**
     * Get recent successful payments for notification dropdown.
     */
    public function recentPayments()
    {
        $payments = Payment::with(['user', 'course'])
            ->where('status', 'paid')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }
}
