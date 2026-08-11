<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LmsProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'tagline',
        'description',
        'image',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(LmsCategory::class)->ordered();
    }

    public function articles(): HasMany
    {
        return $this->hasMany(LmsArticle::class);
    }

    public function subCategories(): HasManyThrough
    {
        return $this->hasManyThrough(LmsSubCategory::class, LmsCategory::class, 'lms_product_id', 'lms_category_id');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class,
            'lms_client_products'
        )
            ->withPivot(['status', 'assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    public function clientProducts(): HasMany
    {
        return $this->hasMany(LmsClientProduct::class);
    }

    public function purchasedClientsCount(): int
    {
        if (! $this->product_id) {
            return 0;
        }

        return ClientProduct::where('product_id', $this->product_id)->where('status', true)->count();
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
