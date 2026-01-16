<?php

declare(strict_types=1);

namespace App\Traits;

use Carbon\Carbon;

/**
 * Trait for parsing date strings with proper time boundaries
 * Supports both m/d/Y (from date picker) and Y-m-d (programmatic) formats
 */
trait ParsesDateRanges
{
    /**
     * Convert date string to Carbon instance with start of day
     * Supports both m/d/Y (from date picker) and Y-m-d (programmatic) formats
     */
    protected function parseDateWithStartOfDay(string $dateString): Carbon
    {
        try {
            $date = Carbon::createFromFormat('m/d/Y', $dateString);
            if ($date === false) {
                $date = Carbon::createFromFormat('Y-m-d', $dateString);
            }
            if ($date === false) {
                throw new \InvalidArgumentException("Unable to parse date: {$dateString}");
            }

            return $date->startOfDay();
        } catch (\Throwable $e) {
            // Fallback to Y-m-d format if m/d/Y fails
            $date = Carbon::createFromFormat('Y-m-d', $dateString);
            if ($date === false) {
                throw new \InvalidArgumentException("Unable to parse date: {$dateString}", 0, $e);
            }

            return $date->startOfDay();
        }
    }

    /**
     * Convert date string to Carbon instance with end of day
     * Supports both m/d/Y (from date picker) and Y-m-d (programmatic) formats
     */
    protected function parseDateWithEndOfDay(string $dateString): Carbon
    {
        try {
            $date = Carbon::createFromFormat('m/d/Y', $dateString);
            if ($date === false) {
                $date = Carbon::createFromFormat('Y-m-d', $dateString);
            }
            if ($date === false) {
                throw new \InvalidArgumentException("Unable to parse date: {$dateString}");
            }

            return $date->endOfDay();
        } catch (\Throwable $e) {
            // Fallback to Y-m-d format if m/d/Y fails
            $date = Carbon::createFromFormat('Y-m-d', $dateString);
            if ($date === false) {
                throw new \InvalidArgumentException("Unable to parse date: {$dateString}", 0, $e);
            }

            return $date->endOfDay();
        }
    }
}

