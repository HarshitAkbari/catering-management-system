@extends('layouts.app')

@section('title', 'Expenses Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Expenses Report</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label><input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label><input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div class="flex items-end"><button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button></div>
            <div class="flex items-end">
                <a href="{{ route('reports.export', ['type' => 'expenses', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Total Purchases</p><p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_purchases'] }}</p></div>
            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Total Amount</p><p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($summary['total_amount'], 2) }}</p></div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Expense Trends Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Expense Trends</h3>
                </div>
                <div class="p-6">
                    <canvas id="expenseTrendsChart" height="100"></canvas>
                </div>
            </div>

            <!-- Monthly Expense Comparison -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Monthly Comparison</h3>
                </div>
                <div class="p-6">
                    <canvas id="monthlyExpenseChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Expenses by Vendor Chart -->
        @if(isset($chartData['vendors']) && count($chartData['vendors']['labels']) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Expenses by Vendor</h3>
            </div>
            <div class="p-6">
                <canvas id="vendorExpenseChart" height="100"></canvas>
            </div>
        </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Vendor</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th></tr></thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($stockPurchases as $purchase)
                        <tr><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $purchase->created_at->format('M d, Y') }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $purchase->inventoryItem->name }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ number_format($purchase->quantity, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $purchase->vendor->name ?? '-' }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">₹{{ number_format($purchase->price ?? 0, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No expenses found</td></tr>
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

    // Expense Trends Chart
    const trendsCtx = document.getElementById('expenseTrendsChart');
    if (trendsCtx && chartData.trends) {
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: chartData.trends.labels,
                datasets: [{
                    label: 'Expense Amount (₹)',
                    data: chartData.trends.data,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + parseFloat(context.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }

    // Monthly Expense Comparison Chart
    const monthlyCtx = document.getElementById('monthlyExpenseChart');
    if (monthlyCtx && chartData.monthly_comparison) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: chartData.monthly_comparison.labels,
                datasets: [{
                    label: 'Expense (₹)',
                    data: [chartData.monthly_comparison.previous, chartData.monthly_comparison.current],
                    backgroundColor: [
                        'rgba(156, 163, 175, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                    borderColor: [
                        'rgb(156, 163, 175)',
                        'rgb(239, 68, 68)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + parseFloat(context.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }

    // Expenses by Vendor Chart
    const vendorCtx = document.getElementById('vendorExpenseChart');
    if (vendorCtx && chartData.vendors && chartData.vendors.labels.length > 0) {
        new Chart(vendorCtx, {
            type: 'bar',
            data: {
                labels: chartData.vendors.labels,
                datasets: [{
                    label: 'Expense (₹)',
                    data: chartData.vendors.data,
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderColor: 'rgb(139, 92, 246)',
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + parseFloat(context.parsed.x).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
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

