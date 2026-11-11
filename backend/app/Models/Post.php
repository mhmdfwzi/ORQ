<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'slug',
        'excerpt',
        'excerpt_ar',
        'content',
        'content_ar',
        'image',
        'author_id',
        'status',
        'is_featured',
        'published_at',
        'views_count',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Get the author
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get reading time in minutes
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200));
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Get excerpt or auto-generate from content
     */
    public function getExcerptTextAttribute()
    {
        return $this->excerpt ?: Str::limit(strip_tags($this->content), 150);
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/blog-placeholder.jpg');
    }
}
