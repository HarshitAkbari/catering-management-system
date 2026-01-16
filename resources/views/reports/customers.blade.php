@extends('layout.default')

@section('title', 'Customers Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Customers Report</h4>
                </div>
                <div class="card-body">
                    <!-- Returning Customers Section -->
                    <div class="mb-4">
                        <h4 class="card-title mb-3">Returning Customers ({{ $returningCustomers->count() }})</h4>
                        <div class="row">
                            @foreach($returningCustomers->take(6) as $customer)
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                    <div class="widget-stat card bg-primary">
                                        <div class="card-body p-4">
                                            <div class="media ai-icon">
                                                <span class="me-3 bgl-primary text-primary">
                                                    <i class="flaticon-381-user-7"></i>
                                                </span>
                                                <div class="media-body">
                                                    <p class="mb-1 font-weight-bold">{{ $customer->name }}</p>
                                                    <p class="mb-0"><small>{{ $customer->orders_count }} orders</small></p>
                                                    <p class="mb-0"><small>Total: ₹{{ number_format($customer->orders_sum_estimated_cost ?? 0, 2) }}</small></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="row mt-4">
                        <div class="col-xl-6 col-lg-12 col-sm-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Top Customers by Order Count</h4>
                                </div>
                                <div class="card-body">
                                    <div id="topCustomersChart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Order Frequency Distribution</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="frequencyChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customers Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">All Customers</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="datatable table table-responsive-md">
                                            <thead>
                                                <tr>
                                                    <th><strong>Customer</strong></th>
                                                    <th><strong>Phone</strong></th>
                                                    <th><strong>Orders</strong></th>
                                                    <th><strong>Total Value</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($customers as $customer)
                                                    <tr>
                                                        <td>{{ $customer->name }}</td>
                                                        <td>{{ $customer->mobile }}</td>
                                                        <td><span class="badge light badge-primary">{{ $customer->orders_count }}</span></td>
                                                        <td>₹{{ number_format($customer->orders_sum_estimated_cost ?? 0, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No customers found</td>
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

        // Top Customers Chart (ApexCharts Horizontal Bar Chart)
        if ($('#topCustomersChart').length > 0 && chartData.top_customers && chartData.top_customers.labels.length > 0) {
            var topCustomersOptions = {
                series: [{
                    name: 'Order Count',
                    data: chartData.top_customers.data
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['var(--primary)'],
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
                        }
                    },
                    forceNiceScale: true
                },
                yaxis: {
                    categories: chartData.top_customers.labels,
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
                            return value + ' orders';
                        }
                    }
                }
            };

            var topCustomersChart = new ApexCharts(document.querySelector("#topCustomersChart"), topCustomersOptions);
            topCustomersChart.render();
        }

        // Order Frequency Distribution Chart (Chart.js Doughnut Chart)
        if ($('#frequencyChart').length > 0 && chartData.frequency) {
            const frequencyCtx = document.getElementById('frequencyChart').getContext('2d');
            
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
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
})(jQuery);
</script>
@endsection
