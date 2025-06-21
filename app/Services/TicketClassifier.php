<?php

namespace App\Services;

use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

class TicketClassifier
{
    public function classify(string $subject, string $body): array
    {
        
        if (!config('services.openai.classify_enabled', false)) {
            return [
                'category' => Arr::random(['billing', 'technical', 'account']),
                'explanation' => 'Dummy explanation (OpenAI disabled)',
                'confidence' => round(rand(60, 99) / 100, 2),
            ];
        }

        $prompt = "You are an AI ticket classifier. Given a support ticket's subject and body, analyze the content and return a JSON object with these keys:

            1. category – one of 'billing', 'technical', or 'account' (strictly use one of these).
            2. explanation – a short summary explaining why this category was selected.
            3. confidence – a decimal between 0 and 1 indicating how confident you are in this classification.
            
            Respond only with valid JSON.";
            

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => "Subject: $subject\n\nBody: $body"],
            ],
        ]);

        return json_decode($response->choices[0]->message->content, true);
    }
}
