<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportIssueType extends Model
{
    protected $fillable = [
        'saas_product_id',
        'name',
        'slug',
        'module',
        'default_priority',
        'icon',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            SaasProduct::class,
            'saas_product_id'
        );
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(
            SupportTicket::class,
            'issue_type_id'
        );
    }
}