<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the blogs for the category.
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    /**
     * Get the active blogs for the category.
     */
    public function activeBlogs()
    {
        return $this->hasMany(Blog::class, 'category_id')->where('status', 1);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get the active blogs count for the category.
     */
    public function getActiveBlogsCountAttribute()
    {
        return $this->activeBlogs()->count();
    }
}
