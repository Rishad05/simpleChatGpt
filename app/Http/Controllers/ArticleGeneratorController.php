<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class ArticleGeneratorController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->has('title')) {
            return response()->json(['error' => 'Title is required'], 400);
        }

        $title = $request->input('title');

        // Ensure that the OpenAI API key is set in your configuration
        $apiKey = config('app.openai_api_key');
        if (empty($apiKey)) {
            return response()->json(['error' => 'OpenAI API key is not configured'], 500);
        }

        $client = OpenAI::client($apiKey);

        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => "Write a story about $title."],
            ],
        ]);

        $content = trim($result['choices'][0]['message']['content']);

        return view('writer', compact('title', 'content'));
    }
}
