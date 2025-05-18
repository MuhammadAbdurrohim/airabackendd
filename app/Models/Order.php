<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'shipping_address',
        'payment_method',
        'status',
        'tracking_number',
        'shipping_courier',
        'shipping_proof_path',
        'fcm_token',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    protected $appends = [
        'status_icon',
        'status_color',
        'formatted_total',
        'shipping_proof_url',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentProof()
    {
        return $this->hasOne(PaymentProof::class);
    }

    public function complaints()
    {
        return $this->hasMany(OrderComplaint::class);
    }

    // Status List
    public static function getStatusList()
    {
        return [
            'Menunggu Pembayaran' => 'Menunggu Pembayaran',
            'Menunggu Konfirmasi' => 'Menunggu Konfirmasi',
            'Diproses' => 'Diproses',
            'Dikirim' => 'Dikirim',
            'Selesai' => 'Selesai',
            'Dibatalkan' => 'Dibatalkan',
        ];
    }

    // Accessors
    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'Menunggu Pembayaran' => '🕒',
            'Menunggu Konfirmasi' => '🕒',
            'Diproses' => '🔄',
            'Dikirim' => '🚚',
            'Selesai' => '✅',
            'Dibatalkan' => '❌',
            default => '📦'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Menunggu Pembayaran', 'Menunggu Konfirmasi' => '#f59e0b',
            'Diproses' => '#3b82f6',
            'Dikirim' => '#4f46e5',
            'Selesai' => '#10b981',
            'Dibatalkan' => '#ef4444',
            default => '#6b7280'
        };
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getShippingProofUrlAttribute()
    {
        if (!$this->shipping_proof_path) {
            return null;
        }

        return Storage::disk('public')->url($this->shipping_proof_path);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['Menunggu Pembayaran', 'Menunggu Konfirmasi']);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'Diproses');
    }

    public function scopeShipping($query)
    {
        return $query->where('status', 'Dikirim');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Selesai');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Dibatalkan');
    }

    // Methods
    public function canBeCancelled()
    {
        return in_array($this->status, ['Menunggu Pembayaran', 'Menunggu Konfirmasi']);
    }

    public function canBeProcessed()
    {
        return $this->status === 'Menunggu Konfirmasi' && 
               $this->paymentProof && 
               $this->paymentProof->is_verified;
    }

    public function canBeShipped()
    {
        return $this->status === 'Diproses';
    }

    public function canBeCompleted()
    {
        return $this->status === 'Dikirim' && !$this->complaints()->where('status', 'Pending')->exists();
    }

    public function complete()
    {
        if (!$this->canBeCompleted()) {
            throw new \Exception('Order cannot be completed');
        }

        $this->update(['status' => 'Selesai']);

        // Send notification
        if ($this->fcm_token) {
            app(FCMService::class)->sendNotification(
                $this->fcm_token,
                "✅",
                "Pesanan #{$this->id} telah selesai. Terima kasih telah berbelanja!",
                ['order_id' => $this->id]
            );
        }
    }

    public function updateStatus($status)
    {
        if (!array_key_exists($status, self::getStatusList())) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->update(['status' => $status]);
    }

    public function calculateTotal()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    protected static function booted()
    {
        static::deleting(function ($order) {
            // Delete related files
            if ($order->shipping_proof_path) {
                Storage::disk('public')->delete($order->shipping_proof_path);
            }

            // Delete related models
            $order->orderItems()->delete();
            if ($order->paymentProof) {
                Storage::disk('public')->delete($order->paymentProof->path);
                $order->paymentProof->delete();
            }
        });
    }
}
