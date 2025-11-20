@extends('layouts.admin_default')

@section('content')
    <div class="container-fluid">
        <div class="row ">
            <div class="col-lg-12">

                @if(auth()->user()->user_type == 'admin')
                    <div class="dashboard-card g-4 mt-4">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 mb-2 d-flex justify-content-between align-items-center">
                                    <h2 class="fw-semibold mb-0">Dashboard</h2>
                                    <div class="w-22 mb-1 d-flex">
                                        <form action="{{ route('admin.dashboard') }}" method="get" id="dashboardCommonForm" autocomplete="off">
                                            <input type="text" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker"  id="dashboard_datepicker"  name="daterangeCommon" placeholder="From Date - To Date" value="{{ request('daterangeCommon') }}" style="width:105% !important;">
                                        </form>

                                        <a href="{{ route('admin.dashboard') }}" class="m-auto" id="resetDashboardDate" title="Reset Filter">
                                            <i class="fas fa-sync-alt ml-3" style="font-size: 20px; color:#08683d;"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Total Users -->
                                <div class="col-xl-2 col-md-4 col-sm-6 mt-2">
                                    <div class="card shadow-sm border-0 p-3 text-center" style="background:#e0f7fa;">
                                        <a href="{{ route('admin.service-sales') }}" class="">
                                            <div class="icon mb-2">
                                                <i class="las la-credit-card fs-2 text-primary"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1">Total Sales</h6>
                                            <h4 class="text-primary">AED {{ $totalSales ?? 0 }}</h4>
                                        </a>
                                    </div>
                                </div>

                                <!-- Total Translators -->
                                <div class="col-xl-2 col-md-4 col-sm-6 mt-2">
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
                                <div class="col-xl-2 col-md-4 col-sm-6 mt-2">
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
                                <div class="col-xl-2 col-md-4 col-sm-6 mt-2">
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
                                <div class="col-xl-2 col-md-4 col-sm-6 mt-2">
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
                                <div class="col-xl-2 col-md-4 col-sm-6 mt-2">
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
                        </div>
                    </div>
                @endif
                
                <div class="dashboard-card g-4 mt-4">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 mb-2 d-flex justify-content-between align-items-center">
                            <h4 class="fw-semibold mb-0">Total Service Requests</h4>
                            <div class="w-22 mb-1 d-flex">
                                <form action="{{ route('admin.dashboard') }}" method="get" id="dashboardServiceForm" autocomplete="off">
                                    <input type="text" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker"  id="dashboard_service_datepicker"  name="daterangeService" placeholder="From Date - To Date" value="{{ request('daterangeService') }}" style="width:105% !important;">
                                </form>

                                <a href="{{ route('admin.dashboard') }}" class="m-auto" id="resetDashboardDate" title="Reset Filter">
                                    <i class="fas fa-sync-alt ml-3" style="font-size: 20px; color:#08683d;"></i>
                                </a>
                            </div>
                        </div>
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
                                            <h6 class="service-title">{{ $service->name ?? '—' }}</h6>
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


                <div class="row g-4 mt-4 mb-4">
                    <div class="col-lg-12">
                        <div class="dashboard-card">
                            <h4 class="fw-semibold mb-3">Service Requests</h4>
                            <div id="serviceChart" style="height:500px;"></div>
                        </div>
                    </div>
                </div>

                {{-- <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h4 class="fw-semibold mb-3">Consultations Trend</h4>
                            <div id="consultationChart" style="height:320px;"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h4 class="fw-semibold mb-3">Lawyer Activity</h4>
                            <div id="lawyerChart" style="height:320px;"></div>
                        </div>
                    </div>
                </div> --}}
                @if(auth()->user()->user_type == 'admin')
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <div class="dashboard-card">
                                <div class="col-lg-12 d-flex">
                                    <div class="d-flex">
                                        <select id="filterService" class="form-control w-auto mr-2" style="border : 1px solid #8c8c8c;">
                                            <option value="all">All</option>
                                            @foreach ($paymentServices as $key => $serv)
                                                <option value="{{ $serv['slug'] }}">{{ $serv['name'] }}</option>
                                            @endforeach
                                        </select>

                                        <select id="filterType" class="form-control w-auto mr-2" style="border : 1px solid #8c8c8c;">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly" selected>Monthly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    
                                        @php
                                            $currentYear = now()->year;
                                            $minYear = 2024; 
                                            $endYear = max($currentYear - 9, $minYear);
                                        @endphp

                                        <select id="filterYear" class="form-control w-auto" style="border : 1px solid #8c8c8c;">
                                            @for ($y = $currentYear; $y >= $endYear; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>

                                        <select id="filterMonth" style="display:none;border : 1px solid #8c8c8c;" class="form-control w-auto ml-2">
                                            @foreach (range(1,12) as $m)
                                                <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <div class="d-flex ml-auto">
                                        <a href="{{ route('admin.service-sales') }}" class="btn btn-primary mr-2" id="btn-filter">Service Sales</a>
                                    </div>
                                </div>
                                <div class="col-lg-12 ">
                                    
                                    <div id="sales-chart" class="mt-4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Recent Requests -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="dashboard-card">
                            <h4 class="fw-semibold mb-3">Recent Service Requests</h4>
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
                                                            $paymentStatus = '<span class="badge badge-pill badge-danger">Unpaid</span><br><small>AED '.($serviceReq->amount ?? 0) .'</small>';
                                                        }elseif($serviceReq->payment_status === 'success'){
                                                            $paymentStatus = '<span class="badge badge-pill badge-success">Paid</span><br><small>AED '.($serviceReq->amount ?? 0).'</small>';
                                                        }elseif($serviceReq->payment_status === 'partial'){
                                                            $paymentStatus = '<span class="badge badge-pill badge-warning">Partially Paid</span><br><small>AED '.($serviceReq->amount ?? 0).'</small>';
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
                                                    @can('view-'.$serviceReq->service_slug)
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
            background: #f1f0ec;
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

            $('#dashboard_datepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(
                    picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD')
                );
                $('#dashboardCommonForm').submit();
            });

            $('#dashboard_service_datepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(
                    picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD')
                );
                $('#dashboardServiceForm').submit();
            });
           
            function loadChart() {
                const filter = $('#filterType').val();
                const service = $('#filterService').val();
                let year = '';
                if (filter != 'yearly') {
                    year = $('#filterYear').val();
                }
                const month = $('#filterMonth').val();

                $.ajax({
                    url: '{{ route('sales.report.data') }}',
                    data: {service, filter, year, month },
                    success: function (response) {
                        Highcharts.chart('sales-chart', {
                            chart: { 
                                type: 'column',
                                height: 600 
                            },
                            title: { 
                                text: response.title +' Sales Overview',
                                align: 'center',    
                                margin: 40,  
                                style: {
                                    color: '#08683d', 
                                    fontWeight: 'bold', 
                                    fontSize: '20px',   
                                    fontFamily: '"Inter", sans-serif' 
                                }
                            },
                            xAxis: {
                                categories: response.labels,
                                labels: { 
                                    // rotation: -45,
                                    style: {
                                        fontSize: '12px',
                                        fontFamily: '"Inter", sans-serif',
                                        color: '#00000'
                                    } 
                                }
                            },
                            yAxis: { 
                                title: { 
                                    text: 'Total Sales (AED)' 
                                } 
                            },
                            series: [{
                                name: 'Sales',
                                data: response.data,
                                color: '#08683d'
                            }],
                            legend: {
                                enabled: true,
                                itemStyle: {
                                    fontWeight: 'bold',
                                    fontSize: '14px'
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            exporting: {
                                enabled: false 
                            }
                        });
                    }
                });
            }

            const $filterType  = $('#filterType');
            const $filterService  = $('#filterService');
            const $filterYear  = $('#filterYear');
            const $filterMonth = $('#filterMonth');

            $filterMonth.on('change', function () {
                $(this).data('userSelected', true);
                loadChart();
            });

            $filterService.on('change', function () {
                loadChart();
            });

            $filterYear.on('change', function () {
                loadChart();
            });

            $filterType.on('change', function () {
                applyFilterChange(false);
            });

            applyFilterChange(true);

            function applyFilterChange(isInitialLoad) {
                const filter = $filterType.val();

                if (filter === 'daily') {
                    $filterMonth.show();

                    const userSelected = $filterMonth.data('userSelected');
                    if (!userSelected) {
                        const currentMonth = String(new Date().getMonth() + 1);
                        if ($filterMonth.val() !== currentMonth) {
                            $filterMonth.val(currentMonth);
                        }
                    }
                } else {
                    $filterMonth.hide();
                }

                if(filter !== 'yearly'){
                    $filterYear.show();
                }else{
                    $filterYear.hide();
                }
                loadChart();
            }

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
