<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContestTargetType extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
    ];

    public function contests(): HasMany
    {
        return $this->hasMany(Contest::class, 'target_type_id');
    }
}
