@extends('layouts.admin_default', ['title' => 'Service Sales Report'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Service Sales Report</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table4  bg-white mb-30">
                            @php
                                $serviceSlug = request('service_id'); 
                                $exportPermissionName = $serviceSlug ? "export-$serviceSlug" : null;
                            @endphp

                            <form method="GET" action="{{ route('admin.service-sales') }}" autocomplete="off">
                                <div class="row mb-2">

                                    <div class="col-md-4 input-group  mb-1">
                                        <select name="service_id" class="select2 form-control ih-small ip-gray radius-xs b-deep px-15"  data-placeholder="Select Service" >
                                            <option value="">--Select Service--</option>
                                             @foreach($services as $serv)
                                               <option value="{{ $serv->slug }}"  {{ request('service_id') == $serv->slug ? 'selected' : '' }}>
                                                    {{ $serv->name ?? '---' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4 input-group  mb-1">
                                        <input type="text" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker" name="daterange" placeholder="From Date - To Date" value="{{ request('daterange') }}">
                                    </div>

                                  
                                    <div class="col-md-3 mb-1  d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('admin.service-sales') }}" class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>

                                        @if(request('service_id'))
                                            {{-- @if($exportPermissionName && auth()->user()->can($exportPermissionName)) --}}
                                                <a href="{{ route('service-sales.export', request()->all()) }}"
                                                    class="btn btn-warning btn-sm ml-2">
                                                    Export
                                                </a>
                                            {{-- @endcan --}}
                                        @endif
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive table4">
                                @if($selectedService == 'online-live-consultancy')
                                    @include('admin.include.consultation-sales', ['consultations' => $consultations])
                                @elseif(!empty($selectedService))
                                    @include('admin.include.service-sales', ['serviceRequests' => $serviceRequests])
                                @else
                                    <div class="text-center text-muted py-5">Please select a service to view sales data.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
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
@endsection

@section('script_first')
    <script src="{{ asset('assets/js/bootstrap/popper.js') }}"></script>
@endsection

@section('script')
    <script type="text/javascript">
        $(function() {
            
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
