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

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = [];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Mobile filter
        if ($request->has('mobile_like') && !empty($request->mobile_like)) {
            $filters['mobile_like'] = $request->mobile_like;
        }
        
        // Payment status filter
        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $filters['payment_status'] = $request->payment_status;
        }
        
        $orders = $this->paymentService->getGroupedOrders($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'mobile_like' => $request->input('mobile_like', ''),
            'payment_status' => $request->input('payment_status', ''),
        ];

        return view('payments.index', compact('orders', 'filterValues'));
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
