<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $orders = Order::where('tenant_id', auth()->user()->tenant_id)
            ->whereIn('payment_status', ['pending', 'partial'])
            ->with('customer', 'invoice.payments')
            ->orderBy('event_date')
            ->paginate(15);

        return view('payments.index', compact('orders'));
    }
}
