<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_type',
        'name',
        'account_number',
        'account_name',
        'description',
        'is_active',
        'logo_path',
        'instructions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'instructions' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_method_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
