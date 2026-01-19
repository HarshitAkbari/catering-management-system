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

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = ['tenant_id' => $tenantId];
        
        // Name filter
        if ($request->has('name_like') && !empty($request->name_like)) {
            $filters['name_like'] = $request->name_like;
        }
        
        // Email filter
        if ($request->has('email_like') && !empty($request->email_like)) {
            $filters['email_like'] = $request->email_like;
        }
        
        // Mobile filter
        if ($request->has('mobile_like') && !empty($request->mobile_like)) {
            $filters['mobile_like'] = $request->mobile_like;
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $customers = $this->customerService->getByTenant($tenantId, 15, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'name_like' => $request->input('name_like', ''),
            'email_like' => $request->input('email_like', ''),
            'mobile_like' => $request->input('mobile_like', ''),
        ];
        
        $page_title = 'Customers';
        $subtitle = 'Manage your customer database';
        
        return view('customers.index', compact('customers', 'filterValues', 'page_title', 'subtitle'));
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
