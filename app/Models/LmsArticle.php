<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsArticle extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'lms_product_id',
        'lms_category_id',
        'lms_sub_category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'lms_category_id';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(LmsProduct::class, 'lms_product_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LmsCategory::class, 'lms_category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(LmsSubCategory::class, 'lms_sub_category_id');
    }
}
