<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {}

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $customers = $this->customerService->getByTenant($tenantId);

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($customer->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->customerService->getCustomerWithGroupedOrders($customer->id, $tenantId);
        
        if (!$result['status']) {
            abort(404, $result['message']);
        }

        return view('customers.show', [
            'customer' => $result['customer'],
            'groupedOrdersList' => $result['groupedOrders'],
        ]);
    }
}
