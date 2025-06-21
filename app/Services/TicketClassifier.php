<?php

namespace App\Services;

use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

class TicketClassifier
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('openai.api_key');

        if (!$this->apiKey) {
            \Log::info('Classification failed: error form constructor].');
        }

        $this->enabled = config('openai.classify_enabled', false);
    }

    public function classify(string $subject, string $body): array
    {
        if (!$this->enabled) {
            \Log::info('OpenAI classification disabled, using dummy data');
            return [
                'category' => Arr::random(['billing', 'technical', 'account']),
                'explanation' => 'Dummy explanation (OpenAI disabled)',
                'confidence' => round(rand(60, 99) / 100, 2),
            ];
        }

        if (!config('openai.api_key')) {
            \Log::error('OpenAI API key not configured');
            throw new \Exception('OpenAI API key not configured');
        }

        try {
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
                'temperature' => 0.1,
                'max_tokens' => 200,
            ]);

            $content = $response->choices[0]->message->content;
            $result = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from OpenAI: ' . json_last_error_msg());
            }

            if (!isset($result['category']) || !in_array($result['category'], ['billing', 'technical', 'account'])) {
                throw new \Exception('Invalid category returned from OpenAI: ' . ($result['category'] ?? 'null'));
            }

            if (!isset($result['confidence']) || !is_numeric($result['confidence'])) {
                $result['confidence'] = 0.8;
            }

            if (!isset($result['explanation'])) {
                $result['explanation'] = 'AI classification completed';
            }

            \Log::info('OpenAI classification successful', [
                'category' => $result['category'],
                'confidence' => $result['confidence']
            ]);

            return $result;

        } catch (\Exception $e) {
            \Log::error('OpenAI classification failed', [
                'error' => $e->getMessage(),
                'subject' => $subject,
                'body_length' => strlen($body)
            ]);

            throw new \Exception('Classification failed: ' . $e->getMessage());
        }
    }
}
