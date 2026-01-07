<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;

class InvoiceNumberService
{
    /**
     * Generate a unique invoice number for the given tenant.
     *
     * Format: INV-YYYYMMDD-XXXX (where XXXX is a 4-digit sequential number)
     *
     * @param int $tenantId
     * @return string
     */
    public static function generate(int $tenantId): string
    {
        $datePrefix = now()->format('Ymd');
        $baseNumber = "INV-{$datePrefix}-";

        // Get the last invoice number for today for this tenant
        $lastInvoice = Invoice::where('tenant_id', $tenantId)
            ->where('invoice_number', 'like', $baseNumber . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract the sequence number from the last invoice
            $lastSequence = (int) substr($lastInvoice->invoice_number, -4);
            $nextSequence = $lastSequence + 1;
        } else {
            // First invoice for today
            $nextSequence = 1;
        }

        // Format sequence as 4-digit number with leading zeros
        $sequence = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
        $invoiceNumber = $baseNumber . $sequence;

        // Double-check uniqueness (in case of race condition)
        $exists = Invoice::where('tenant_id', $tenantId)
            ->where('invoice_number', $invoiceNumber)
            ->exists();

        if ($exists) {
            // If exists, increment and try again
            $nextSequence++;
            $sequence = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
            $invoiceNumber = $baseNumber . $sequence;
        }

        return $invoiceNumber;
    }
}

