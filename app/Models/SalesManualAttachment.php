<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesManualAttachment extends Model
{
    protected $fillable = [
        'sales_manual_id',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'sort_order',
    ];

    public function salesManual(): BelongsTo
    {
        return $this->belongsTo(
            SalesManual::class
        );
    }
}