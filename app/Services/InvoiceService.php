<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Services\InvoiceNumberService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceService extends BaseService
{
    protected InvoiceRepository $repository;
    protected OrderRepository $orderRepository;

    public function __construct(InvoiceRepository $repository, OrderRepository $orderRepository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get invoices by tenant with pagination
     */
    public function getByTenant(int $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->filterAndPaginate(
            ['tenant_id' => $tenantId],
            ['order.customer'],
            [],
            $perPage
        );
    }

    /**
     * Generate invoice for orders with given order number
     */
    public function generateInvoice(string $orderNumber, int $tenantId): array
    {
        try {
            return DB::transaction(function () use ($orderNumber, $tenantId) {
                // Get all orders with this order_number
                $orders = $this->orderRepository->getByOrderNumber($orderNumber, $tenantId);

                if ($orders->isEmpty()) {
                    return ['status' => false, 'message' => 'Orders not found.'];
                }

                // Check if invoice already exists for any of these orders
                $orderIds = $orders->pluck('id')->toArray();
                $existingInvoice = $this->repository->filter([
                    'tenant_id' => $tenantId,
                ], ['order'], [], true)
                    ->whereIn('order_id', $orderIds)
                    ->first();

                if ($existingInvoice) {
                    return [
                        'status' => false,
                        'message' => 'Invoice already exists for this order.',
                        'invoice' => $existingInvoice,
                    ];
                }

                // Get the first order (we'll use it as the primary order for the invoice)
                $primaryOrder = $orders->first();

                // Calculate totals
                $totalAmount = $orders->sum('estimated_cost');
                $tax = 0; // Can be configured later
                $discount = 0; // Can be configured later
                $finalAmount = $totalAmount + $tax - $discount;

                // Generate invoice number
                $invoiceNumber = InvoiceNumberService::generate($tenantId);

                // Create invoice
                $invoice = $this->repository->create([
                    'tenant_id' => $tenantId,
                    'order_id' => $primaryOrder->id,
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $totalAmount,
                    'tax' => $tax,
                    'discount' => $discount,
                    'final_amount' => $finalAmount,
                    'status' => 'sent',
                ]);

                return [
                    'status' => true,
                    'message' => 'Invoice generated successfully.',
                    'invoice' => $invoice,
                ];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to generate invoice: ' . $e->getMessage()];
        }
    }

    /**
     * Get invoice with related orders
     */
    public function getInvoiceWithOrders(int $invoiceId, int $tenantId): array
    {
        $invoice = $this->repository->find($invoiceId, ['order.customer', 'tenant', 'payments']);

        if (!$invoice || $invoice->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Invoice not found or unauthorized'];
        }

        // Get all orders with the same order_number
        $orders = $this->orderRepository->getByOrderNumber($invoice->order->order_number, $tenantId);

        return [
            'status' => true,
            'invoice' => $invoice,
            'orders' => $orders,
        ];
    }

    /**
     * Get invoice settings (for PDF generation)
     */
    public function getInvoiceSettings(): array
    {
        return [
            'invoice_logo' => null,
            'invoice_footer_text' => null,
            'invoice_terms' => null,
        ];
    }
}

