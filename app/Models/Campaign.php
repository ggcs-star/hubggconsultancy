<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'description',
        'starts_at',
        'ends_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // This app's tables are MyISAM (no FK support), so ON DELETE CASCADE in the
        // migrations is not actually enforced by the database — cascade manually.
        // A campaign's leads are kept, just detached, since they're real records.
        static::deleting(function (self $campaign) {
            $campaign->leads()->update(['campaign_id' => null]);
        });
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function metrics(): object
    {
        $leads = $this->leads;

        $contacted = $leads->whereNotIn('status', ['new'])->count();
        $qualified = $leads->whereIn('status', ['qualified', 'proposal', 'negotiation', 'won'])->count();
        $won = $leads->where('status', 'won')->count();
        $revenue = $leads->where('status', 'won')->sum('expected_value');

        return (object) [
            'total' => $leads->count(),
            'contacted' => $contacted,
            'qualified' => $qualified,
            'won' => $won,
            'revenue' => (float) $revenue,
        ];
    }
}
