<?php

namespace App\Http\Controllers;

use App\Services\OpenAiService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatGPTController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAiService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    // Handle Text Request
    public function generateText(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        $response = $this->openAIService->generateText($request->input('prompt'));
        return response()->json($response);
    }

    // Handle Image Request
    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'size' => 'nullable|string|in:256x256,512x512,1024x1024',
        ]);

        $size = $request->input('size', '1024x1024');
        $response = $this->openAIService->generateImage($request->input('prompt'), 1, $size);
        return response()->json($response);
    }
}
