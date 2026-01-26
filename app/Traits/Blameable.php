<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait Blameable
{
    public static function bootBlameable(): void
    {
        static::creating(function ($model) {
            $userId = Auth::id();

            if (! $userId) {
                return;
            }

            // Only set 'created_by' if it hasn't been explicitly set already.
            // This allows the user to pass a custom 'created_by' value.
            if ($model->hasColumn('created_by') && (! isset($model->created_by) || is_null($model->created_by))) {
                $model->created_by = $userId;
            }

            // Always set 'updated_by' to the current authenticated user when creating,
            // unless it's explicitly set (though less common for creation).
            if ($model->hasColumn('updated_by') && (! isset($model->updated_by) || is_null($model->updated_by))) {
                $model->updated_by = $userId;
            }
        });

        static::updating(function ($model) {
            $userId = Auth::id();
            if (! $userId) {
                return;
            }

            // Only set 'updated_by' if it hasn't been explicitly set already.
            // This allows the user to pass a custom 'updated_by' value.
            // Simple check: if updated_by is set and not null, don't override it.
            if ($model->hasColumn('updated_by') && (! isset($model->updated_by) || is_null($model->updated_by))) {
                $model->updated_by = $userId;
            }
        });
    }

    /**
     * Get the user who created this record.
     */
    public function creator(): BelongsTo
    {
        if (! $this->hasColumn('created_by')) {
            // Prevent Laravel from crashing if column doesn't exist
            return $this->belongsTo(User::class, 'id')->whereRaw('1 = 0');
        }

        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater(): BelongsTo
    {
        if (! $this->hasColumn('updated_by')) {
            return $this->belongsTo(User::class, 'id')->whereRaw('1 = 0');
        }

        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function hasColumn(string $column): bool
    {
        try {
            return Schema::hasColumn($this->getTable(), $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

