<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly OrderService $orderService
    ) {}

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $orders = $this->paymentService->getGroupedOrders($tenantId);

        return view('payments.index', compact('orders'));
    }
    
    public function updateGroupPaymentStatus(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'payment_status' => 'required|in:pending,partial,paid',
        ]);
        
        $tenantId = auth()->user()->tenant_id;
        
        $result = $this->orderService->updateGroupPaymentStatus(
            $validated['order_number'],
            $validated['payment_status'],
            $tenantId
        );
        
        if (!$result['status']) {
            return redirect()->route('payments.index')
                ->with('error', $result['message']);
        }
        
        return redirect()->route('payments.index')
            ->with('success', $result['message']);
    }
}
