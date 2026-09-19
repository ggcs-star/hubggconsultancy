<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/** Shared "who can a lead/contest be assigned to" list — approved salespersons, alphabetical. */
trait HasApprovedSalespersons
{
    protected function salespersons(): Collection
    {
        return User::where('role', 'user')->where('salesperson_status', 'approved')->orderBy('name')->get();
    }
}
