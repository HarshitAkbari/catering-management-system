<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payment;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PaymentService extends BaseService
{
    protected PaymentRepository $repository;
    protected OrderRepository $orderRepository;
    protected InvoiceRepository $invoiceRepository;

    public function __construct(
        PaymentRepository $repository,
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
    ) {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Get payments grouped by order number
     */
    public function getGroupedOrders(int $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        // Get all orders
        $allOrders = $this->orderRepository->getByTenant($tenantId, ['customer']);
        
        // Group orders by order_number
        $groupedOrders = $allOrders->groupBy('order_number')->map(function ($orderGroup, $orderNumber) use ($tenantId) {
            $firstOrder = $orderGroup->first();
            
            // Check if invoice exists for any order in this group
            $orderIds = $orderGroup->pluck('id')->toArray();
            $invoice = $this->invoiceRepository->filter([
                'tenant_id' => $tenantId,
            ], ['order'], [], true)
                ->whereIn('order_id', $orderIds)
                ->first();
            
            return [
                'order_number' => $orderNumber,
                'customer' => $firstOrder->customer,
                'total_amount' => $orderGroup->sum('estimated_cost'),
                'payment_status' => $this->getGroupPaymentStatus($orderGroup),
                'orders' => $orderGroup,
                'created_at' => $firstOrder->created_at,
                'invoice' => $invoice,
            ];
        })->values()->sortByDesc('created_at')->values();
        
        // Manual pagination
        $currentPage = request()->get('page', 1);
        $items = $groupedOrders->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $total = $groupedOrders->count();
        
        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Create payment
     */
    public function createPayment(array $data, int $tenantId): array
    {
        try {
            return DB::transaction(function () use ($data, $tenantId) {
                $data['tenant_id'] = $tenantId;
                $payment = $this->repository->create($data);

                return [
                    'status' => true,
                    'message' => 'Payment recorded successfully.',
                    'payment' => $payment,
                ];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to create payment: ' . $e->getMessage()];
        }
    }

    /**
     * Get payments by invoice
     */
    public function getByInvoice(int $invoiceId, int $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getByInvoice($invoiceId, $tenantId);
    }

    /**
     * Get total payments for invoice
     */
    public function getTotalByInvoice(int $invoiceId, int $tenantId): float
    {
        return $this->repository->getTotalByInvoice($invoiceId, $tenantId);
    }

    /**
     * Get group payment status - returns payment status if all orders have same status, otherwise "mixed"
     */
    private function getGroupPaymentStatus($orderGroup): string
    {
        $paymentStatuses = $orderGroup->pluck('payment_status')->unique()->filter();
        return $paymentStatuses->count() === 1 ? $paymentStatuses->first() : 'mixed';
    }
}

