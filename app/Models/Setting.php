<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * Determine if a key should be encrypted at rest.
     */
    private static function isSensitive(string $key): bool
    {
        return preg_match('/^(GROQ_API_KEY(.*)?|OPENAI_API_KEY|COHERE_API_KEY|HUGGINGFACE_API_KEY|GEMINI_API_KEY)$/', $key) === 1;
    }

    /**
     * Encrypt sensitive values before storing.
     */
    public function setValueAttribute($value): void
    {
        $key = $this->attributes['key'] ?? $this->key ?? null;
        if ($key && self::isSensitive($key)) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Decrypt sensitive values on read. If decryption fails, return raw value.
     */
    public function getValueAttribute($value)
    {
        $key = $this->attributes['key'] ?? $this->key ?? null;
        if ($key && self::isSensitive($key)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Throwable $e) {
                return $value; // handle legacy plaintext
            }
        }
        return $value;
    }

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $description = null)
    {
        // Ensure encryption is applied even if mutator isn't triggered
        $toStore = self::isSensitive($key) ? Crypt::encryptString($value) : $value;
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $toStore, 'description' => $description]
        );
    }
}
