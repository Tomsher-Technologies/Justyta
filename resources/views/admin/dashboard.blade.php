@extends('layouts.admin_default')

@section('content')
    <div class="container-fluid">
        <div class="row ">
            <div class="col-lg-12">

                <h2 class="row fw-bold mb-4">Admin Dashboard</h2>

                <div class="row g-4">

                        <!-- Total Users -->
                        {{-- <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                            <div class="card shadow-sm border-0 p-3 text-center" style="background:#e0f7fa;">
                                <a href="{{ route('admin.users.index') }}">
                                    <div class="icon mb-2">
                                        <i class="las la-users fs-2 text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Total Users</h6>
                                    <h4 class="text-primary">{{ $userCounts['user'] ?? 0 }}</h4>
                                </a>
                            </div>
                        </div> --}}

                        <!-- Total Translators -->
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                            <div class="card shadow-sm border-0 p-3 text-center" style="background:#f1f8e9;">
                                <a href="{{ route('translators.index') }}">
                                    <div class="icon mb-2">
                                        <i class="las la-language fs-2 text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Total Translators</h6>
                                    <h4 class="text-primary">{{ $userCounts['translator'] ?? 0 }}</h4>
                                </a>
                            </div>
                        </div>

                        <!-- Total Law Firms -->
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                            <div class="card shadow-sm border-0 p-3 text-center" style="background:#fff3e0;">
                                <a href="{{ route('vendors.index') }}">
                                    <div class="icon mb-2">
                                        <i class="las la-gavel fs-2 text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Total Law Firms</h6>
                                    <h4 class="text-primary">{{ $userCounts['vendor'] ?? 0 }}</h4>
                                </a>
                            </div>
                        </div>

                        <!-- Total Lawyers -->
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                            <div class="card shadow-sm border-0 p-3 text-center" style="background:#fce4ec;">
                                <a href="{{ route('lawyers.index') }}">
                                    <div class="icon mb-2">
                                        <i class="las la-user-tie fs-2 text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Total Lawyers</h6>
                                    <h4 class="text-primary">{{ $userCounts['lawyer'] ??  0}}</h4>
                                </a>
                            </div>
                        </div>

                        <!-- Total Job Posts -->
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                            <div class="card shadow-sm border-0 p-3 text-center" style="background:#ede7f6;">
                                <a href="{{ route('job-posts.index') }}">
                                    <div class="icon mb-2">
                                        <i class="las la-briefcase fs-2 text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Total Job Posts</h6>
                                    <h4 class="text-primary">{{ $totalJobs ?? 0 }}</h4>
                                </a>
                            </div>
                        </div>

                        <!-- Total Training Requests -->
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                            <div class="card shadow-sm border-0 p-3 text-center" style="background:#e8f5e9;">
                                <a href="{{ route('training-requests.index') }}">
                                    <div class="icon mb-2">
                                        <i class="las la-chalkboard-teacher fs-2 text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Total Training Requests</h6>
                                    <h4 class="text-primary">{{ $totalTrainings ?? 0 }}</h4>
                                </a>
                            </div>
                        </div>

                    </div>

                <div class="row">
                    <h5 class="fw-semibold">Service Requests</h5>
                    <div class="row g-4 mb-4">
                        @foreach ($services as $key => $service)
                            @if ($service->slug != 'law-firm-services')
                                <div class="col-md-2 col-sm-3 mt-4">
                                    <div class="service-card">
                                        @if($service->slug === 'legal-translation')
                                            <a href="{{ route('legal-translation-requests.index') }}" style="color: inherit;">
                                        @else
                                            <a href="{{ route('service-requests.index', ['service_id' => $service->slug]) }}" style="color: inherit;">
                                        @endif
                                        
                                            <div class="icon">
                                                <img src="{{ asset(getUploadedImage($service->icon)) }}" class="card-img-top"
                                                    style="height: 45px; object-fit: contain;" alt="{{ $service->name }}" />
                                            </div>
                                            <span class="service-title">{{ $service->name ?? '—' }}</span>
                                            <h5 class="service-count">{{ $serviceCounts[$service->id] ?? 0 }}</h5>
                                            {{-- <div class="service-status">
                                                <span class="status pending">Pending: 25</span>
                                                <span class="status ongoing">Ongoing: 40</span>
                                                <span class="status completed">Completed: 170</span>
                                                <span class="status rejected">Rejected: 10</span>
                                            </div> --}}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>


                <!-- Charts Section -->
                <div class="row g-4 mb-4">
                    {{-- <div class="col-lg-8">
                        <div class="dashboard-card">
                            <h5 class="fw-semibold mb-3">Monthly Revenue Overview</h5>
                            <div id="revenueChart" style="height:320px;"></div>
                        </div>
                    </div> --}}

                    <div class="col-lg-12">
                        <div class="dashboard-card">
                            <h5 class="fw-semibold mb-3">Service Requests</h5>
                            <div id="serviceChart" style="height:500px;"></div>
                        </div>
                    </div>
                </div>

                {{-- <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="fw-semibold mb-3">Consultations Trend</h5>
                            <div id="consultationChart" style="height:320px;"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="fw-semibold mb-3">Lawyer Activity</h5>
                            <div id="lawyerChart" style="height:320px;"></div>
                        </div>
                    </div>
                </div> --}}

                <!-- Recent Requests -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="dashboard-card">
                            <h5 class="fw-semibold mb-3">Recent Service Requests</h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th class="text-center">Reference Code</th>
                                            <th class="text-left" width="20%">Service</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Payment Status</th>
                                            <th class="text-center">Request Status</th>
                                            <th class="text-center">Request Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $statusClass = [
                                                'pending' => 'badge-gray',
                                                'ongoing' => 'badge-warning',
                                                'completed' => 'badge-success',
                                                'rejected' => 'badge-danger',
                                            ];
                                        @endphp
                                        @forelse ($recentRequests as $key => $serviceReq)
                                            <tr>
                                                <td class="text-center">{{ $key+1 }}</td>
                                                <td class="text-center">
                                                    {{ $serviceReq->reference_code ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $serviceReq->service?->name ?? '—' }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $serviceReq->user?->name ?? '—' }}

                                                    <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual"
                                                    title='<div class="popover-title">User Info</div>'
                                                    data-content='
                                                            <div class="custom-popover">
                                                                <div class="popover-item"><i class="fas fa-user"></i> {{ $serviceReq->user?->name }}</div>
                                                                <div class="popover-item"><i class="fas fa-envelope"></i> {{ $serviceReq->user?->email }}</div>
                                                                <div class="popover-item"><i class="fas fa-phone"></i> {{ $serviceReq->user?->phone }}</div>
                                                            </div>
                                                        '></i>

                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $paymentStatus = '-';
                                                        if(in_array($serviceReq->payment_status, ['pending','failed'])){
                                                            $paymentStatus = '<span class="badge badge-pill badge-danger">Unpaid</span>';
                                                        }elseif($serviceReq->payment_status === 'success'){
                                                            $paymentStatus = '<span class="badge badge-pill badge-success">Paid</span>';
                                                        }elseif($serviceReq->payment_status === 'partial'){
                                                            $paymentStatus = '<span class="badge badge-pill badge-warning">Partially Paid</span>';
                                                        }
                                                    @endphp     
                                                    {!! $paymentStatus !!}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-pill {{ $statusClass[$serviceReq->status] ?? 'badge-secondary' }}">
                                                        {{ ucfirst($serviceReq->status) }}
                                                    </span>
                                                </td>

                                                <td class="text-center">{{ date('d, M Y h:i A', strtotime($serviceReq->submitted_at)) }}</td>
                                                <td class="text-center">
                                                    @can('view_service_requests')
                                                        <div class="table-actions">
                                                            <a href="{{ route('service-request-details', base64_encode($serviceReq->id)) }}"
                                                                title="View Service Request">
                                                                <span data-feather="eye"></span>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No recent requests found.</td>
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
        <!-- ends: .row -->
    </div>
@endsection


@section('style')
    <style>
        body {
            background-color: #f8f9fb;
            font-family: 'Poppins', sans-serif;
        }

        .dashboard-card {
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 24px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
        }

        .stat-label {
            color: #6c757d;
        }

        .color-success {
            color: #28a745;
        }

        .color-danger {
            color: #dc3545;
        }

        .color-primary {
            color: #007bff;
        }

        .highcharts-figure,
        .highcharts-data-table table {
            width: 100%;
            margin: 1em auto;
        }



        .service-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            margin: 0 auto 15px auto;
            font-size: 1.5rem;
        }

        .service-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .service-count {
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .service-status {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .status {
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 8px;
            color: white;
        }

        .status.pending {
            background: #5a5f7d;
        }

        .status.ongoing {
            background: #ffa33a;
        }

        .status.completed {
            background: #038f09;
        }

        .status.rejected {
            background: #e3171a;
        }
        .popover-header {
            background-color: var(--secondary);
            /*#e2d8bf*/
            font-size: 13px;
        }

        .popover {
            background-color: #ffffff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            min-width: 200px;
        }

        .custom-popover {
            font-size: 14px;
            color: #333;
        }

        .popover-title {
            font-weight: 700;
            /* margin-bottom: 8px; */
            color: var(--primary);
            /* border-bottom: 1px solid #e9ecef;
             padding-bottom: 4px; */
        }

        .custom-popover .popover-item i {
            color: var(--primary);
            margin-right: 8px;
        }
    </style>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
@endsection

@section('script_first')
    <script src="{{ asset('assets/js/bootstrap/popper.js') }}"></script>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            // Monthly Revenue Chart
            // Highcharts.chart('revenueChart', {
            //     chart: {
            //         type: 'column'
            //     },
            //     title: {
            //         text: ''
            //     },
            //     xAxis: {
            //         categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct']
            //     },
            //     yAxis: {
            //         title: {
            //             text: 'Revenue ($)'
            //         }
            //     },
            //     series: [{
            //             name: 'Revenue',
            //             data: [4200, 4800, 5200, 6800, 7500, 8000, 9500, 10300, 9700, 11000],
            //             color: '#007bff'
            //         },
            //         {
            //             name: 'Expenses',
            //             data: [2000, 2500, 2700, 3100, 3300, 3600, 4000, 4500, 4200, 5000],
            //             color: '#dc3545'
            //         }
            //     ],
            //     credits: {
            //         enabled: false
            //     }
            // });

            // Service Category Chart
            Highcharts.chart('serviceChart', {
                chart: {
                    type: 'pie',
                    height: 500
                },
                title: {
                    text: ''
                },
                legend: {
                    enabled: true,
                    align: 'center',        // center horizontally
                    verticalAlign: 'bottom', // place at bottom
                    layout: 'horizontal',   // horizontal layout
                    itemStyle: {
                        fontWeight: 'normal',
                        fontSize: '13px'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}: {point.y}'
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Requests',
                    colorByPoint: true,
                    data: @json($chartData)
                }],
                credits: {
                    enabled: false
                },
                exporting: {
                    enabled: false 
                }
            });


            // Consultation Trend
            // Highcharts.chart('consultationChart', {
            //     chart: {
            //         type: 'spline'
            //     },
            //     title: {
            //         text: ''
            //     },
            //     xAxis: {
            //         categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
            //     },
            //     yAxis: {
            //         title: {
            //             text: 'Consultations'
            //         }
            //     },
            //     series: [{
            //         name: 'Completed',
            //         data: [20, 25, 30, 28, 40, 35, 32],
            //         color: '#28a745'
            //     }],
            //     credits: {
            //         enabled: false
            //     }
            // });

            // // Lawyer Activity
            // Highcharts.chart('lawyerChart', {
            //     chart: {
            //         type: 'areaspline'
            //     },
            //     title: {
            //         text: ''
            //     },
            //     xAxis: {
            //         categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct']
            //     },
            //     yAxis: {
            //         title: {
            //             text: 'Active Lawyers'
            //         }
            //     },
            //     series: [{
            //         name: 'Active',
            //         data: [200, 220, 250, 280, 320, 360, 400, 420, 460, 500],
            //         color: '#17a2b8',
            //         fillOpacity: 0.3
            //     }],
            //     credits: {
            //         enabled: false
            //     }
            // });


            $('.popover-toggle').popover();

            // Show on hover/focus
            $('.popover-toggle').on('mouseenter focus', function() {
                $('.popover-toggle').not(this).popover('hide'); // hide others
                $(this).popover('show');
            });

            // Hide on mouseleave or blur only if not hovering popover
            $('.popover-toggle').on('mouseleave blur', function() {
                let _this = this;
                setTimeout(function() {
                    if (!$('.popover:hover').length) {
                        $(_this).popover('hide');
                    }
                }, 200);
            });

            // Keep popover open on hover
            $(document).on('mouseenter', '.popover', function() {
                clearTimeout(window._popoverTimeout);
            });

            $(document).on('mouseleave', '.popover', function() {
                $('[data-toggle="popover"]').popover('hide');
            });

            // Re-render Feather if used
            $(document).on('shown.bs.popover', function() {
                if (window.feather) feather.replace();
            });
        });
    </script>
@endsection
