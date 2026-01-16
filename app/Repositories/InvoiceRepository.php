<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository extends BaseRepository
{
    protected array $searchable = ['invoice_number'];

    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    /**
     * Get invoices by tenant ID
     */
    public function getByTenant(int $tenantId, array $relations = []): Collection
    {
        $query = $this->model->where('tenant_id', $tenantId);
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get invoice by invoice number and tenant
     */
    public function findByInvoiceNumber(string $invoiceNumber, int $tenantId): ?Invoice
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('invoice_number', $invoiceNumber)
            ->first();
    }

    /**
     * Get invoices by order ID
     */
    public function getByOrder(int $orderId, int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('order_id', $orderId)
            ->get();
    }

    /**
     * Check if invoice number exists for tenant
     */
    public function invoiceNumberExists(string $invoiceNumber, int $tenantId): bool
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('invoice_number', $invoiceNumber)
            ->exists();
    }
}

