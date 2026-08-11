<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsCategory extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'lms_product_id',
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'lms_product_id';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(LmsProduct::class, 'lms_product_id');
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(LmsSubCategory::class)->ordered();
    }

    public function articles(): HasMany
    {
        return $this->hasMany(LmsArticle::class)->whereNull('lms_sub_category_id')->ordered();
    }
}
