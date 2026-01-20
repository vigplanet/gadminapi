<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProductRequest extends Model
{
    use HasFactory;

    protected $table = 'user_product_requests';
    protected $hidden = ['image'];

    protected $fillable = [
        'customer_id',
        'description',
        'image',
        'status',
        'product_id',
        'admin_notes'
    ];

    protected $appends = ['image_url'];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}