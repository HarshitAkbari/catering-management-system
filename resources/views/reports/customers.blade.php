@extends('layouts.app')

@section('title', 'Customers Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Customers Report</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Returning Customers ({{ $returningCustomers->count() }})</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($returningCustomers->take(6) as $customer)
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $customer->orders_count }} orders</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total: ₹{{ number_format($customer->orders_sum_estimated_cost ?? 0, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Top Customers by Order Count -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Customers by Order Count</h3>
                </div>
                <div class="p-6">
                    <canvas id="topCustomersChart" height="100"></canvas>
                </div>
            </div>

            <!-- Customer Order Frequency Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order Frequency Distribution</h3>
                </div>
                <div class="p-6">
                    <canvas id="frequencyChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Phone</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Orders</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Value</th></tr></thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($customers as $customer)
                        <tr><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $customer->name }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $customer->mobile }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $customer->orders_count }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">₹{{ number_format($customer->orders_sum_estimated_cost ?? 0, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No customers found</td></tr>
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

    // Top Customers Chart
    const topCustomersCtx = document.getElementById('topCustomersChart');
    if (topCustomersCtx && chartData.top_customers && chartData.top_customers.labels.length > 0) {
        new Chart(topCustomersCtx, {
            type: 'bar',
            data: {
                labels: chartData.top_customers.labels,
                datasets: [{
                    label: 'Order Count',
                    data: chartData.top_customers.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Order Frequency Distribution Chart
    const frequencyCtx = document.getElementById('frequencyChart');
    if (frequencyCtx && chartData.frequency) {
        new Chart(frequencyCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.frequency.labels,
                datasets: [{
                    data: chartData.frequency.data,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
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
});
</script>
@endpush
@endsection

