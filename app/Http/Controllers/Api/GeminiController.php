<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'memory' => 'nullable|array',
        ]);

        $contents = [];

        foreach ($request->memory ?? [] as $m) {
            if (!empty($m['text'])) {
                $contents[] = [
                    'parts' => [
                        ['text' => $m['text']]
                    ]
                ];
            }
        }

        $contents[] = [
            'parts' => [
                ['text' => $request->message]
            ]
        ];

        $apiKey = env('GEMINI_API_KEY');

        $response = Http::timeout(60)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
            [
                'contents' => $contents
            ]
        );

        if (!$response->successful()) {
            return response()->json([
                'message' => 'AI service unavailable'
            ], 500);
        }

        $data = $response->json();

        $text = $data['candidates'][0]['content']['parts'][0]['text']
            ?? 'I could not generate a response.';

        return response()->json([
            'reply' => trim($text)
        ]);
    }
}