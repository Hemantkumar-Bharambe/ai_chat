<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class ChatController extends Controller
{
    private const DEFAULT_GROQ_MODEL = 'llama-3.3-70b-versatile';

    public function index()
    {
        return view('chat');
    }

    public function getCurrentModel()
    {
        return response()->json([
            'provider' => 'groq',
            'model' => self::DEFAULT_GROQ_MODEL,
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
        ]);

        $messages = $request->messages;

        try 
        {
            return $this->generateWithGroq($messages);
        }
        catch (\Throwable $e)
        {
            return response()->json([
                'content' => "❌ Server Error: " . $e->getMessage()
            ], 500);
        }
    }

    private function generateWithGroq($messages)
    {
        $apiKeys = $this->getGroqApiKeys();
        $model = self::DEFAULT_GROQ_MODEL;

        if (empty($apiKeys)) 
        {
            return response()->json([
                'content' => "Groq API Key not configured. Please add at least one key in settings.",
            ], 500);
        }

        $lastError = null;

        foreach ($apiKeys as $apiKey) 
        {
            $response = Http::withToken($apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.6,
                    'max_tokens' => 500,
                ]);

            if ($response->successful()) 
            {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;

                if ($content) 
                {
                    return response()->json([
                        'content' => $content
                    ]);
                }

                $lastError = "No content returned from Groq: " . json_encode($data);
                continue;
            }

            $lastError = "Groq API Error (key rotated): " . $response->body();
            
        }

        return response()->json([
            'content' => $lastError ?? 'All Groq keys failed. Please check your keys or limits.',
        ], 500);
    }

   
    private function getGroqApiKeys(): array
    {
        $keys = Setting::where('key', 'LIKE', 'GROQ_API_KEY%')
            ->orderBy('key')
            ->pluck('value')
            ->filter()
            ->values()
            ->toArray();

        
        $envKey = env('GROQ_API_KEY');
        if ($envKey) 
        {
            $keys[] = $envKey;
        }

        return array_values(array_unique($keys));
    }

    

}
