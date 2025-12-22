<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('settings', compact('settings'));
    }

    public function update(Request $request)
    {
        
        if ($request->isJson()) 
        {
            $data = $request->all();
            
            
            foreach ($data as $key => $value) 
            {
                Setting::set($key, $value);
            }

            return response()->json(['success' => true, 'message' => 'Settings updated successfully!']);
        }

        
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        Setting::set($request->key, $request->value, $request->description);

        return redirect()->back()->with('success', 'Setting updated successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'required|string',
        ]);

        Setting::create([
            'key' => $request->key,
            'value' => $request->value,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Setting created successfully!');
    }

    public function destroy($id)
    {
        Setting::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Setting deleted successfully!');
    }

    
    public function testGroqKey(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer|exists:settings,id',
            'key' => 'nullable|string'
        ]);

        $apiKey = null;
        if ($request->filled('id')) 
        {
            $setting = Setting::find($request->id);
            if (!$setting || !str_starts_with($setting->key, 'GROQ_API_KEY')) 
            {
                return response()->json(['ok' => false, 'status' => 400, 'message' => 'Not a Groq key setting'], 400);
            }
            $apiKey = $setting->value;
        } 
        else 
        {
            $apiKey = $request->key;
        }

        if (!$apiKey) 
        {
            return response()->json(['ok' => false, 'status' => 400, 'message' => 'API key missing'], 400);
        }

        try 
        {
            $res = Http::withToken($apiKey)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => 'ping']],
                'max_tokens' => 10,
            ]);

            $status = $res->status();
            $ok = $res->successful();

            $msg = match ($status) 
            {
                200 => 'Key works',
                401, 403 => 'Invalid or unauthorized key',
                429 => 'Rate limited',
                default => 'Groq returned status ' . $status,
            };

            return response()->json(['ok' => $ok, 'status' => $status, 'message' => $msg]);
        } 
        catch (\Throwable $e) 
        {
            return response()->json(['ok' => false, 'status' => 500, 'message' => 'Network or server error'], 500);
        }
    }
}
