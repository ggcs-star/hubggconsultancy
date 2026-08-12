<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsClientProduct extends Model
{
    protected $fillable = [
        'client_id',
        'lms_product_id',
        'status',
        'assigned_by',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function product()
    {
        return $this->belongsTo(LmsProduct::class, 'lms_product_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
