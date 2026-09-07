<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'gg_user_id',
        'parent_gg_user_id',
        'level',
        'name',
        'username',
        'purchase_code',
        'joined_at',
        'kyc_verified',
        'team_size',
        'last_synced_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'kyc_verified' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function changes(): HasMany
    {
        return $this->hasMany(TeamMemberChange::class)->orderByDesc('changed_at');
    }
}
