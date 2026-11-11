<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobListing extends Model
{
    protected $table = 'jobs_listing';

    protected $fillable = [
        'title',
        'title_ar',
        'slug',
        'description',
        'description_ar',
        'requirements',
        'requirements_ar',
        'location',
        'type',
        'department',
        'is_active',
        'deadline',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deadline' => 'date',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title);
                $count = static::where('slug', 'like', $job->slug . '%')->count();
                if ($count > 0) {
                    $job->slug .= '-' . ($count + 1);
                }
            }
        });
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('deadline')
              ->orWhere('deadline', '>=', now()->toDateString());
        });
    }

    /**
     * Get localized title
     */
    public function getLocalizedTitleAttribute(): string
    {
        if (app()->getLocale() === 'ar' && $this->title_ar) {
            return $this->title_ar;
        }
        return $this->title;
    }

    /**
     * Get localized description
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        if (app()->getLocale() === 'ar' && $this->description_ar) {
            return $this->description_ar;
        }
        return $this->description;
    }

    /**
     * Get localized requirements
     */
    public function getLocalizedRequirementsAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && $this->requirements_ar) {
            return $this->requirements_ar;
        }
        return $this->requirements;
    }
}
