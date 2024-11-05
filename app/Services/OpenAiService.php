<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAiService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.openai.url', env('OPENAI_API_URL'));
        $this->apiKey = env('OPENAI_API_KEY');
    }

    // Handle Text Completion
    public function generateText(string $prompt, string $model = 'gpt-3.5-turbo', int $maxTokens = 1500)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->post("{$this->apiUrl}/chat/completions", [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $maxTokens,
        ]);

        return $response->json();
    }

    // Handle Image Generation
    public function generateImage(string $prompt, int $n = 1, string $size = '1024x1024')
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->post("{$this->apiUrl}/images/generations", [
            'prompt' => $prompt,
            'n' => $n,
            'size' => $size,
        ]);

        return $response->json();
    }
}
