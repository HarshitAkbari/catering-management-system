@extends('layout.default')

@section('title', 'Attendance Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Attendance Report</h4>
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
                                        :params="['type' => 'attendance', 'start_date' => $startDate, 'end_date' => $endDate]"
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
                            <div class="widget-stat card bg-primary">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-primary text-primary">
                                            <i class="flaticon-381-user-7"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Total Records</p>
                                            <h4 class="mb-0">{{ $summary['total_records'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="widget-stat card bg-success">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-success text-success">
                                            <i class="flaticon-381-heart"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Present</p>
                                            <h4 class="mb-0">{{ $summary['present'] }}</h4>
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
                                            <i class="flaticon-381-calendar-1"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Absent</p>
                                            <h4 class="mb-0">{{ $summary['absent'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="widget-stat card bg-warning">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-warning text-warning">
                                            <i class="flaticon-381-diamond"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Late</p>
                                            <h4 class="mb-0">{{ $summary['late'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="widget-stat card bg-info">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-info text-info">
                                            <i class="flaticon-381-settings-2"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Half Day</p>
                                            <h4 class="mb-0">{{ $summary['half_day'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="widget-stat card bg-secondary">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-secondary text-secondary">
                                            <i class="flaticon-381-star"></i>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Attendance Rate</p>
                                            <h4 class="mb-0">{{ $summary['attendance_rate'] }}%</h4>
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
                                    <h4 class="card-title">Attendance Trends</h4>
                                </div>
                                <div class="card-body">
                                    <div id="attendanceTrendsChart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Status Distribution</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance by Staff Chart -->
                    @if(isset($chartData['staff']) && count($chartData['staff']['labels']) > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Attendance Rate by Staff</h4>
                                </div>
                                <div class="card-body">
                                    <div id="staffChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Attendance Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title">Attendance Details</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="datatable table table-responsive-md">
                                            <thead>
                                                <tr>
                                                    <th><strong>Staff Name</strong></th>
                                                    <th><strong>Date</strong></th>
                                                    <th><strong>Status</strong></th>
                                                    <th><strong>Check-in</strong></th>
                                                    <th><strong>Check-out</strong></th>
                                                    <th><strong>Notes</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($attendances as $attendance)
                                                    @php
                                                        $status = $attendance->status;
                                                        $statusClass = '';
                                                        if ($status === 'present') {
                                                            $statusClass = 'badge-success';
                                                        } elseif ($status === 'absent') {
                                                            $statusClass = 'badge-danger';
                                                        } elseif ($status === 'late') {
                                                            $statusClass = 'badge-warning';
                                                        } elseif ($status === 'half_day') {
                                                            $statusClass = 'badge-info';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $attendance->staff->name ?? '-' }}</td>
                                                        <td>{{ $attendance->date->format('M d, Y') }}</td>
                                                        <td>
                                                            <span class="badge light {{ $statusClass }}">
                                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $attendance->check_in_time ?? '-' }}</td>
                                                        <td>{{ $attendance->check_out_time ?? '-' }}</td>
                                                        <td>{{ $attendance->notes ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No attendance records found</td>
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

        // Attendance Trends Chart (ApexCharts Line Chart)
        if ($('#attendanceTrendsChart').length > 0 && chartData.trends) {
            var trendsOptions = {
                series: [{
                    name: 'Attendance Records',
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
                        }
                    },
                    forceNiceScale: true
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
                            return value + ' records';
                        }
                    }
                }
            };

            var trendsChart = new ApexCharts(document.querySelector("#attendanceTrendsChart"), trendsOptions);
            trendsChart.render();
        }

        // Status Distribution Chart (Chart.js Doughnut Chart)
        if ($('#statusChart').length > 0 && chartData.status) {
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            
            const statusColors = {
                'Present': ['rgba(16, 185, 129, 0.8)', 'rgb(16, 185, 129)'],
                'Absent': ['rgba(239, 68, 68, 0.8)', 'rgb(239, 68, 68)'],
                'Late': ['rgba(245, 158, 11, 0.8)', 'rgb(245, 158, 11)'],
                'Half Day': ['rgba(59, 130, 246, 0.8)', 'rgb(59, 130, 246)'],
            };

            const backgroundColor = [];
            const borderColor = [];
            
            chartData.status.labels.forEach(function(label) {
                const colors = statusColors[label] || ['rgba(139, 92, 246, 0.8)', 'rgb(139, 92, 246)'];
                backgroundColor.push(colors[0]);
                borderColor.push(colors[1]);
            });
            
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: chartData.status.labels,
                    datasets: [{
                        data: chartData.status.data,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
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

        // Attendance by Staff Chart (ApexCharts Bar Chart)
        if ($('#staffChart').length > 0 && chartData.staff && chartData.staff.labels.length > 0) {
            var staffOptions = {
                series: [{
                    name: 'Attendance Rate (%)',
                    data: chartData.staff.data
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
                        horizontal: false,
                        columnWidth: '55%',
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
                    categories: chartData.staff.labels,
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        },
                        rotate: -45,
                        rotateAlways: true
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                            fontSize: '14px',
                            fontFamily: 'Poppins'
                        }
                    },
                    max: 100,
                    forceNiceScale: true
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + '%';
                        }
                    }
                }
            };

            var staffChart = new ApexCharts(document.querySelector("#staffChart"), staffOptions);
            staffChart.render();
        }
    });
})(jQuery);
</script>
@endsection

