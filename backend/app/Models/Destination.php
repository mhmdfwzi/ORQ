<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = ['parent_id', 'name', 'rank', 'price'];

    /**
     * Parent destination
     */
    public function parent()
    {
        return $this->belongsTo(Destination::class, 'parent_id');
    }

    /**
     * Child destinations
     */
    public function children()
    {
        return $this->hasMany(Destination::class, 'parent_id');
    }

    /**
     * Scope for governorates (rank=1)
     */
    public function scopeGovernorates($query)
    {
        return $query->where('rank', '1')->orderBy('name');
    }

    /**
     * Scope for cities/districts under a parent
     */
    public function scopeCitiesOf($query, $parentId)
    {
        return $query->where('parent_id', $parentId)
                     ->whereIn('rank', ['2', '3'])
                     ->orderBy('name');
    }
}
