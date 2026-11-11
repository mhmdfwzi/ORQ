<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'full_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'education_level',
        'university',
        'graduation_year',
        'major',
        'certifications',
        'years_of_experience',
        'current_job',
        'previous_experience',
        'skills',
        'cover_letter',
        'cv_file',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'years_of_experience' => 'integer',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobListing::class, 'job_id');
    }

    /**
     * Status badge colors for Filament
     */
    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'reviewed' => 'info',
            'shortlisted' => 'primary',
            'accepted' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}
