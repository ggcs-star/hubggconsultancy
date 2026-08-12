<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportIssueType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
        'icon',
        'default_priority',
        'status',
        'sort_order',
    ];


    protected $casts = [
        'status' => 'boolean',
    ];


    public function tickets(): HasMany
    {
        return $this->hasMany(
            SupportTicket::class,
            'issue_type_id'
        );
    }
}