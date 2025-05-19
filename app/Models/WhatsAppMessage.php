<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'phone_number',
        'message',
        'status',
        'direction',
        'user_id',
        'order_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user associated with the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the message.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope a query to only include outbound messages.
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }

    /**
     * Scope a query to only include inbound messages.
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }

    /**
     * Scope a query to only include messages with specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include messages for a specific phone number.
     */
    public function scopeForPhone($query, $phoneNumber)
    {
        return $query->where('phone_number', $phoneNumber);
    }

    /**
     * Get the formatted phone number.
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->phone_number;
        if (strlen($phone) > 11) {
            return substr($phone, 0, 4) . ' ' . 
                   substr($phone, 4, 4) . ' ' . 
                   substr($phone, 8);
        }
        return $phone;
    }

    /**
     * Get the message type from metadata.
     */
    public function getTypeAttribute()
    {
        return $this->metadata['type'] ?? 'general';
    }

    /**
     * Get the message status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'sent' => 'info',
            'delivered' => 'primary',
            'read' => 'success',
            'failed' => 'danger',
            'received' => 'success',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        return "<span class='badge badge-{$color}'>{$this->status}</span>";
    }

    /**
     * Get the direction badge HTML.
     */
    public function getDirectionBadgeAttribute()
    {
        $color = $this->direction === 'inbound' ? 'success' : 'primary';
        $icon = $this->direction === 'inbound' ? 'arrow-left' : 'arrow-right';
        return "<span class='badge badge-{$color}'><i class='fas fa-{$icon} mr-1'></i>{$this->direction}</span>";
    }

    /**
     * Check if the message is outbound.
     */
    public function isOutbound()
    {
        return $this->direction === 'outbound';
    }

    /**
     * Check if the message is inbound.
     */
    public function isInbound()
    {
        return $this->direction === 'inbound';
    }

    /**
     * Check if the message was successful.
     */
    public function isSuccessful()
    {
        return in_array($this->status, ['sent', 'delivered', 'read', 'received']);
    }
}