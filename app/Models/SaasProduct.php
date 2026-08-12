<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SaasProduct extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'emi_available',
        'description',
        'logo',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'emi_available' => 'boolean',
        'active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saas_product_user')->withTimestamps();
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
