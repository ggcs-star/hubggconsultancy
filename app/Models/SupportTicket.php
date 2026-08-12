<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'product_id',
        'issue_type_id',
        'priority',
        'status',
        'description',
        'attachment',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];


    /**
     * Ticket owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    /**
     * SaaS Product
     *
     * support_tickets.product_id
     *        ↓
     * saas_products.id
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            SaasProduct::class,
            'product_id'
        );
    }


    /**
     * Support Issue Type
     */
    public function issueType(): BelongsTo
    {
        return $this->belongsTo(
            SupportIssueType::class,
            'issue_type_id'
        );
    }


    /**
     * Ticket Messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(
            SupportTicketMessage::class,
            'ticket_id'
        );
    }
}