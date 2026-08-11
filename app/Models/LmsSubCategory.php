<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsSubCategory extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'lms_category_id',
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'lms_category_id';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LmsCategory::class, 'lms_category_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(LmsArticle::class)->ordered();
    }
}
