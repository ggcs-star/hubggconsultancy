<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSortOrder
{
    public static function bootHasSortOrder(): void
    {
        static::creating(function ($model) {
            if (! empty($model->sort_order)) {
                return;
            }

            $model->sort_order = $model->nextSortOrder();
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    public function sortOrderScopeColumn(): ?string
    {
        return null;
    }

    protected function nextSortOrder(): int
    {
        $query = static::query();

        if ($column = $this->sortOrderScopeColumn()) {
            $query->where($column, $this->{$column});
        }

        return ((int) $query->max('sort_order')) + 1;
    }
}
