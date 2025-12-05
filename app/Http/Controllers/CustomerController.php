<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('tenant_id', auth()->user()->tenant_id)
            ->withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load('orders.invoice.payments');
        return view('customers.show', compact('customer'));
    }
}
