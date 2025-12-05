<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $stats = [
            'total_orders' => Order::where('tenant_id', $tenantId)->where('status', 'confirmed')->count(),
            'upcoming_events' => Order::where('tenant_id', $tenantId)
                ->where('event_date', '>=', today())
                ->where('status', 'confirmed')
                ->count(),
            'pending_payments' => Order::where('tenant_id', $tenantId)
                ->whereIn('payment_status', ['pending', 'partial'])
                ->count(),
            'completed_events' => Order::where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->count(),
        ];

        $upcomingEvents = Order::where('tenant_id', $tenantId)
            ->where('event_date', '>=', today())
            ->where('status', 'confirmed')
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'upcomingEvents'));
    }
}
