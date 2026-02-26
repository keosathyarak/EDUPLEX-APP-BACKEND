<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class PaymentController extends Controller
{
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

        // Remove empty amount
        if (empty($data['amount'])) {
            unset($data['amount']);
        }

        // Call KHQR generate API
        $res = Http::withHeaders([
            'x-api-key' => config('services.khqr.key'),
            'Content-Type' => 'application/json',
        ])->post(config('services.khqr.base_url') . '/api/generate-qr', $data);

        if (!$res->ok()) {
            return response()->json([
                'success' => false,
                'message' => 'QR generation failed',
            ], 500);
        }

        $responseData = $res->json();

        if (!isset($responseData['data']['md5Hash'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR response',
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
                'paid' => true
            ]);
        }

        // Call KHQR check API
        $res = Http::withHeaders([
            'x-api-key' => config('services.khqr.key'),
            'Content-Type' => 'application/json',
        ])->post(config('services.khqr.base_url') . '/api/check-payment', [
            'md5' => $payload['md5'],
        ]);

        if (!$res->ok()) {
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

            DB::transaction(function () use ($payment, $payload, $userId) {

                $payment->update([
                    'status' => 'paid'
                ]);

                Payment::firstOrCreate([
                    'user_id' => $userId,
                    'course_id' => $payload['course_id'],
                ], [
                    'payment_status' => 'paid',
                ]);
            });
        }

        return response()->json([
            'success' => true,
            'paid' => $isPaid
        ]);
    }
}
