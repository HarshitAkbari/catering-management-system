@extends('layout.default')

@section('title', 'Expenses Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Expenses Report</h4>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-danger btn-block">Filter</button>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <x-export-button 
                                        module="reports" 
                                        route="reports.export" 
                                        :params="['type' => 'expenses', 'start_date' => $startDate, 'end_date' => $endDate]"
                                        label="Export Excel"
                                        class="btn btn-success btn-block"
                                    />
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="widget-stat card bg-danger">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-danger text-danger">
                                            <i class="flaticon-381-calendar-1"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Total Purchases</p>
                                            <h4 class="mb-0">{{ $summary['total_purchases'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="widget-stat card bg-danger">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-danger text-danger">
                                            <i class="flaticon-381-heart"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Total Amount</p>
                                            <h4 class="mb-0">₹{{ number_format($summary['total_amount'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="row mt-4">
                        <div class="col-xl-6 col-lg-12 col-sm-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Expense Trends</h4>
                                </div>
                                <div class="card-body">
                                    <div id="expenseTrendsChart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Monthly Comparison</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyExpenseChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses by Vendor Chart -->
                    @if(isset($chartData['vendors']) && count($chartData['vendors']['labels']) > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Expenses by Vendor</h4>
                                </div>
                                <div class="card-body">
                                    <div id="vendorExpenseChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Expenses Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Expense Details</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="datatable table table-responsive-md">
                                            <thead>
                                                <tr>
                                                    <th><strong>Date</strong></th>
                                                    <th><strong>Item</strong></th>
                                                    <th><strong>Quantity</strong></th>
                                                    <th><strong>Vendor</strong></th>
                                                    <th><strong>Price</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($stockPurchases as $purchase)
                                                    <tr>
                                                        <td>{{ $purchase->created_at->format('M d, Y') }}</td>
                                                        <td>{{ $purchase->inventoryItem->name }}</td>
                                                        <td>{{ number_format($purchase->quantity, 2) }}</td>
                                                        <td>{{ $purchase->vendor->name ?? '-' }}</td>
                                                        <td>₹{{ number_format($purchase->price ?? 0, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No expenses found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function($) {
    "use strict";
    
    $(document).ready(function() {
        const chartData = @json($chartData ?? []);
        const isDarkMode = $('body').hasClass('dark-mode') || $('html').hasClass('dark');

        // Chart.js defaults
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = isDarkMode ? '#9CA3AF' : '#6B7280';
            Chart.defaults.borderColor = isDarkMode ? '#374151' : '#E5E7EB';
            Chart.defaults.defaultFontFamily = 'Poppins';
        }

        // Expense Trends Chart (ApexCharts Line Chart)
        if ($('#expenseTrendsChart').length > 0 && chartData.trends) {
            var trendsOptions = {
                series: [{
                    name: 'Expense Amount',
                    data: chartData.trends.data
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#ef4444'],
                stroke: {
                    width: 3,
                    curve: 'smooth',
                    lineCap: 'round'
                },
                markers: {
                    size: 6,
                    strokeWidth: 3,
                    strokeColors: '#fff',
                    hover: {
                        size: 8
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: isDarkMode ? '#374151' : '#E5E7EB',
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    }
                },
                xaxis: {
                    categories: chartData.trends.labels,
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        },
                        formatter: function(value) {
                            return '₹' + value.toLocaleString('en-IN');
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.3,
                        gradientToColors: ['#ef4444'],
                        inverseColors: false,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return '₹' + parseFloat(value).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            };

            var trendsChart = new ApexCharts(document.querySelector("#expenseTrendsChart"), trendsOptions);
            trendsChart.render();
        }

        // Monthly Expense Comparison Chart (Chart.js Bar Chart)
        if ($('#monthlyExpenseChart').length > 0 && chartData.monthly_comparison) {
            const monthlyCtx = document.getElementById('monthlyExpenseChart').getContext('2d');
            
            // Generate gradients
            const previousGradient = monthlyCtx.createLinearGradient(0, 0, 0, 400);
            previousGradient.addColorStop(0, 'rgba(156, 163, 175, 0.8)');
            previousGradient.addColorStop(1, 'rgba(156, 163, 175, 0.3)');
            
            const currentGradient = monthlyCtx.createLinearGradient(0, 0, 0, 400);
            currentGradient.addColorStop(0, 'rgba(239, 68, 68, 0.8)');
            currentGradient.addColorStop(1, 'rgba(239, 68, 68, 0.3)');

            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: chartData.monthly_comparison.labels,
                    datasets: [{
                        label: 'Expense (₹)',
                        data: [chartData.monthly_comparison.previous, chartData.monthly_comparison.current],
                        backgroundColor: [previousGradient, currentGradient],
                        borderColor: ['rgb(156, 163, 175)', 'rgb(239, 68, 68)'],
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

        // Expenses by Vendor Chart (ApexCharts Horizontal Bar Chart)
        if ($('#vendorExpenseChart').length > 0 && chartData.vendors && chartData.vendors.labels.length > 0) {
            var vendorOptions = {
                series: [{
                    name: 'Expense',
                    data: chartData.vendors.data
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#8b5cf6'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '60%',
                        endingShape: 'rounded',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: isDarkMode ? '#374151' : '#E5E7EB',
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        },
                        formatter: function(value) {
                            return '₹' + value.toLocaleString('en-IN');
                        }
                    }
                },
                yaxis: {
                    categories: chartData.vendors.labels,
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return '₹' + parseFloat(value).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            };

            var vendorChart = new ApexCharts(document.querySelector("#vendorExpenseChart"), vendorOptions);
            vendorChart.render();
        }
    });
})(jQuery);
</script>
@endsection
