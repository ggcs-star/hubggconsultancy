<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqSection extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'name',
        'sort_order',
    ];

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }
}
