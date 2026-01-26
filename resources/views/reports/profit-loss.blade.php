@extends('layout.default')

@section('title', 'Profit & Loss Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Profit & Loss Report</h4>
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
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <a href="{{ route('reports.export', ['type' => 'profit-loss', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-block">
                                        <i class="fa fa-download me-2"></i>Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-xl-4 col-lg-6 col-sm-6">
                            <div class="widget-stat card bg-success">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-success text-success">
                                            <i class="flaticon-381-heart"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Revenue</p>
                                            <h4 class="mb-0">₹{{ number_format($revenue, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-sm-6">
                            <div class="widget-stat card bg-danger">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-danger text-danger">
                                            <i class="flaticon-381-calendar-1"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Expenses</p>
                                            <h4 class="mb-0">₹{{ number_format($expenses, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-sm-6">
                            <div class="widget-stat card {{ $profit >= 0 ? 'bg-primary' : 'bg-warning' }}">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 {{ $profit >= 0 ? 'bgl-primary text-primary' : 'bgl-warning text-warning' }}">
                                            <i class="flaticon-381-diamond"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Profit/Loss</p>
                                            <h4 class="mb-0">₹{{ number_format($profit, 2) }}</h4>
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
                                    <h4 class="card-title">Revenue vs Expenses</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueExpensesChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Profit Trend</h4>
                                </div>
                                <div class="card-body">
                                    <div id="profitTrendChart"></div>
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

        // Revenue vs Expenses Comparison Chart (Chart.js)
        if ($('#revenueExpensesChart').length > 0 && chartData.comparison) {
            const comparisonCtx = document.getElementById('revenueExpensesChart').getContext('2d');
            
            // Generate gradients
            const revenueGradient = comparisonCtx.createLinearGradient(0, 0, 0, 400);
            revenueGradient.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
            revenueGradient.addColorStop(1, 'rgba(16, 185, 129, 0.3)');
            
            const expensesGradient = comparisonCtx.createLinearGradient(0, 0, 0, 400);
            expensesGradient.addColorStop(0, 'rgba(239, 68, 68, 0.8)');
            expensesGradient.addColorStop(1, 'rgba(239, 68, 68, 0.3)');

            new Chart(comparisonCtx, {
                type: 'bar',
                data: {
                    labels: chartData.comparison.labels,
                    datasets: [
                        {
                            label: 'Revenue (₹)',
                            data: chartData.comparison.revenue,
                            backgroundColor: revenueGradient,
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses (₹)',
                            data: chartData.comparison.expenses,
                            backgroundColor: expensesGradient,
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

        // Profit Trend Chart (ApexCharts)
        if ($('#profitTrendChart').length > 0 && chartData.profit_trend) {
            const profitData = chartData.profit_trend.data.map((value, index) => {
                return {
                    x: chartData.profit_trend.labels[index],
                    y: value
                };
            });

            const profitColors = profitData.map(item => item.y >= 0 ? '#10b981' : '#ef4444');
            
            var profitOptions = {
                series: [{
                    name: 'Profit/Loss',
                    data: chartData.profit_trend.data
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
                colors: ['var(--primary)'],
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
                    categories: chartData.profit_trend.labels,
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
                        gradientToColors: ['var(--primary)'],
                        inverseColors: false,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            const sign = value >= 0 ? '+' : '';
                            return sign + '₹' + parseFloat(value).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            };

            var profitChart = new ApexCharts(document.querySelector("#profitTrendChart"), profitOptions);
            profitChart.render();
        }
    });
})(jQuery);
</script>
@endsection
