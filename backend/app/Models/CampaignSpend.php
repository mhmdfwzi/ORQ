<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignSpend extends Model
{
    protected $fillable = ['day', 'utm_source', 'utm_medium', 'utm_campaign', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];
}
