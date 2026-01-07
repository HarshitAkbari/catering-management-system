@extends('layouts.app')

@section('title', 'Orders Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Orders Report</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label><input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label><input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div class="flex items-end"><button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button></div>
            <div class="flex items-end">
                <a href="{{ route('reports.export', ['type' => 'orders', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Total Orders</p><p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_orders'] }}</p></div>
            <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Total Amount</p><p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($summary['total_amount'], 2) }}</p></div>
            <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Confirmed</p><p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['confirmed'] }}</p></div>
            <div class="bg-purple-50 dark:bg-purple-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Completed</p><p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['completed'] }}</p></div>
            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Pending</p><p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['pending'] }}</p></div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Order Trends Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order Trends</h3>
                </div>
                <div class="p-6">
                    <canvas id="orderTrendsChart" height="100"></canvas>
                </div>
            </div>

            <!-- Order Status Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order Status Distribution</h3>
                </div>
                <div class="p-6">
                    <canvas id="orderStatusChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Orders by Event Type Chart -->
        @if(isset($chartData['event_types']) && count($chartData['event_types']['labels']) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Orders by Event Type</h3>
            </div>
            <div class="p-6">
                <canvas id="eventTypeChart" height="100"></canvas>
            </div>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Order #</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Event Date</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th></tr></thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($orders as $group)
                        @php
                            $status = $group['status'];
                        @endphp
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $group['order_number'] }}
                                @if($group['orders']->count() > 1)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $group['orders']->count() }} orders)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $group['customer']->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $group['event_date']->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">₹{{ number_format($group['total_amount'], 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $status === 'mixed' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No orders found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData ?? []);
    const isDarkMode = document.documentElement.classList.contains('dark');

    Chart.defaults.color = isDarkMode ? '#9CA3AF' : '#6B7280';
    Chart.defaults.borderColor = isDarkMode ? '#374151' : '#E5E7EB';

    // Order Trends Chart
    const trendsCtx = document.getElementById('orderTrendsChart');
    if (trendsCtx && chartData.trends) {
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: chartData.trends.labels,
                datasets: [{
                    label: 'Orders',
                    data: chartData.trends.data,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Order Status Distribution Chart
    const statusCtx = document.getElementById('orderStatusChart');
    if (statusCtx && chartData.status) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.status.labels,
                datasets: [{
                    data: chartData.status.data,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }

    // Orders by Event Type Chart
    const eventTypeCtx = document.getElementById('eventTypeChart');
    if (eventTypeCtx && chartData.event_types && chartData.event_types.labels.length > 0) {
        new Chart(eventTypeCtx, {
            type: 'bar',
            data: {
                labels: chartData.event_types.labels,
                datasets: [{
                    label: 'Orders',
                    data: chartData.event_types.data,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

