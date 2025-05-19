<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsAppAutoReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'response',
        'is_regex',
        'is_active'
    ];

    protected $casts = [
        'is_regex' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Check if a message matches this auto-reply
     */
    public function matches(string $message): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->is_regex) {
            return preg_match($this->keyword, $message) > 0;
        }
        
        return stripos($message, $this->keyword) !== false;
    }

    /**
     * Get the formatted response with variables replaced
     */
    public function getFormattedResponse(array $variables = []): string
    {
        return strtr($this->response, array_merge([
            '{store_name}' => config('whatsapp.store_name'),
            '{store_phone}' => config('whatsapp.store_phone'),
            '{cs_hours}' => config('whatsapp.customer_service_hours'),
        ], $variables));
    }

    /**
     * Scope a query to only include active auto-replies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
