<?php

namespace App\Services;

use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

class TicketClassifier
{
    public function classify(string $subject, string $body): array
    {
        if (!config('openai.classify_enabled', false)) {
            return [
                'category' => Arr::random(['billing', 'technical', 'account']),
                'explanation' => 'Dummy explanation (OpenAI disabled)',
                'confidence' => round(rand(60, 99) / 100, 2),
            ];
        }

        $prompt = "Classify this support ticket and return JSON with keys: category, explanation, confidence.";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => "Subject: $subject\n\nBody: $body"],
            ],
        ]);

        return json_decode($response->choices[0]->message->content, true);
    }
}
