<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'image',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'views_count',
        'status'
    ];

    protected $hidden = ['updated_at'];

    protected $appends = ['image_url', 'read_time', 'views_count'];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function views()
    {
        return $this->hasMany(BlogView::class);
    }

    public function getReadTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->description));
        $readTime = ceil($wordCount / 200); // Using 200 words per minute
        return max(1, $readTime); // Minimum 1 minute
    }

    public function getViewsCountAttribute()
    {
        return $this->views()->count();
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
}
