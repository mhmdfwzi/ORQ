<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'position',
        'image',
        'bio',
        'social_links',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_active' => 'boolean',
    ];
}
