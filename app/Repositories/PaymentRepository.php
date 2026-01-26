<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository extends BaseRepository
{
    protected array $searchable = ['reference_number', 'notes'];

    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    /**
     * Get payments by tenant ID
     */
    public function getByTenant(int $tenantId, array $relations = []): Collection
    {
        $query = $this->model->where('tenant_id', $tenantId);
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->orderBy('payment_date', 'desc')->get();
    }

    /**
     * Get payments by invoice ID
     */
    public function getByInvoice(int $invoiceId, int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('invoice_id', $invoiceId)
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Get total payments for an invoice
     */
    public function getTotalByInvoice(int $invoiceId, int $tenantId): float
    {
        return (float) $this->model
            ->where('tenant_id', $tenantId)
            ->where('invoice_id', $invoiceId)
            ->sum('amount');
    }
}

