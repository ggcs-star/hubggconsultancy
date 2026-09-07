<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMemberChange extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'team_member_id',
        'field',
        'old_value',
        'new_value',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class);
    }
}
