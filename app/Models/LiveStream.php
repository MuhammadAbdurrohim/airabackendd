<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveStream extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'stream_id',
        'stream_token',
        'user_id',
        'viewer_count',
        'pinned_product_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'live_stream_product');
    }

    public function comments()
    {
        return $this->hasMany(LiveComment::class);
    }

    public function pinnedProduct()
    {
        return $this->belongsTo(Product::class, 'pinned_product_id');
    }
}
