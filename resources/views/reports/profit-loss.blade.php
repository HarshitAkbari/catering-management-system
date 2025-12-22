@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profit & Loss Report</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label><input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label><input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div class="flex items-end"><button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button></div>
            <div class="flex items-end">
                <a href="{{ route('reports.export', ['type' => 'profit-loss', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-green-50 dark:bg-green-900 rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Revenue</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">₹{{ number_format($revenue, 2) }}</p>
            </div>
            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Expenses</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">₹{{ number_format($expenses, 2) }}</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Profit/Loss</p>
                <p class="text-3xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($profit, 2) }}</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Revenue vs Expenses Comparison -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revenue vs Expenses</h3>
                </div>
                <div class="p-6">
                    <canvas id="revenueExpensesChart" height="100"></canvas>
                </div>
            </div>

            <!-- Profit Trend Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Profit Trend</h3>
                </div>
                <div class="p-6">
                    <canvas id="profitTrendChart" height="100"></canvas>
                </div>
            </div>
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

    // Revenue vs Expenses Comparison Chart
    const comparisonCtx = document.getElementById('revenueExpensesChart');
    if (comparisonCtx && chartData.comparison) {
        new Chart(comparisonCtx, {
            type: 'bar',
            data: {
                labels: chartData.comparison.labels,
                datasets: [
                    {
                        label: 'Revenue (₹)',
                        data: chartData.comparison.revenue,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    },
                    {
                        label: 'Expenses (₹)',
                        data: chartData.comparison.expenses,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₹' + parseFloat(context.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
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

    // Profit Trend Chart
    const profitCtx = document.getElementById('profitTrendChart');
    if (profitCtx && chartData.profit_trend) {
        new Chart(profitCtx, {
            type: 'line',
            data: {
                labels: chartData.profit_trend.labels,
                datasets: [{
                    label: 'Profit/Loss (₹)',
                    data: chartData.profit_trend.data,
                    borderColor: function(context) {
                        const value = context.parsed.y;
                        return value >= 0 ? 'rgb(16, 185, 129)' : 'rgb(239, 68, 68)';
                    },
                    backgroundColor: function(context) {
                        const value = context.parsed.y;
                        return value >= 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';
                    },
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
                                const value = context.parsed.y;
                                const sign = value >= 0 ? '+' : '';
                                return sign + '₹' + parseFloat(value).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
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

