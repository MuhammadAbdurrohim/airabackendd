<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'path',
        'verified_at',
        'verified_by',
        'rejected_at',
        'rejected_by',
        'rejection_notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'is_verified',
        'is_rejected',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(Admin::class, 'rejected_by');
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at);
    }

    public function getIsRejectedAttribute()
    {
        return !is_null($this->rejected_at);
    }

    // Methods
    public function verify($admin)
    {
        if ($this->is_verified) {
            throw new \Exception('Payment proof already verified');
        }

        if ($this->is_rejected) {
            throw new \Exception('Payment proof was rejected');
        }

        $this->update([
            'verified_at' => now(),
            'verified_by' => $admin->id,
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_notes' => null,
        ]);

        // Update order status
        $this->order->update(['status' => 'Diproses']);

        // Send notification
        if ($this->order->fcm_token) {
            app(FCMService::class)->sendNotification(
                $this->order->fcm_token,
                "✅",
                "Pembayaran untuk pesanan #{$this->order->id} telah diverifikasi",
                ['order_id' => $this->order->id]
            );
        }
    }

    public function reject($admin, $notes)
    {
        if ($this->is_verified) {
            throw new \Exception('Payment proof already verified');
        }

        if ($this->is_rejected) {
            throw new \Exception('Payment proof already rejected');
        }

        $this->update([
            'rejected_at' => now(),
            'rejected_by' => $admin->id,
            'rejection_notes' => $notes,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        // Update order status
        $this->order->update(['status' => 'Menunggu Pembayaran']);

        // Send notification
        if ($this->order->fcm_token) {
            app(FCMService::class)->sendNotification(
                $this->order->fcm_token,
                "❌",
                "Pembayaran untuk pesanan #{$this->order->id} ditolak: {$notes}",
                ['order_id' => $this->order->id]
            );
        }
    }

    protected static function booted()
    {
        static::deleting(function ($paymentProof) {
            // Delete file from storage
            Storage::disk('public')->delete($paymentProof->path);
        });
    }
}
