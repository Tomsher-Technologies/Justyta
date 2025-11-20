@extends('layouts.admin_default', ['title' => 'Online Consultation Requests'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Online Consultation Requests</h4>
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
                                $exportPermissionName = "export_consultation_requests";

                                $statusClass = [
                                    // 'reserved' => ['bg' => '#808080', 'text' => '#ffffff'],
                                    'waiting_lawyer' => ['bg' => '#ADD8E6', 'text' => '#000000'],
                                    // 'assigned' => ['bg' => '#FFF44F', 'text' => '#000000'],
                                    'accepted' => ['bg' => '#90EE90', 'text' => '#000000'],
                                    'rejected' => ['bg' => '#FF0000', 'text' => '#ffffff'],
                                    'completed' => ['bg' => '#008000', 'text' => '#ffffff'],
                                    'cancelled' => ['bg' => '#A52A2A', 'text' => '#ffffff'],
                                    'no_lawyer_available' => ['bg' => '#FFA07A', 'text' => '#000000'],
                                    'in_progress' => ['bg' => '#FFD580', 'text' => '#000000'],
                                    'on_hold' => ['bg' => '#FFA500', 'text' => '#000000'],
                                ];
                            @endphp

                            <form method="GET" action="{{ route('consultations.index') }}" autocomplete="off">
                                <div class="row mb-2">

                                    <div class="col-md-3 input-group  mb-1">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control ih-small ip-gray radius-xs b-deep px-15" placeholder="Search with Reference Code">
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="status" class=" form-control ih-small ip-gray radius-xs b-deep px-15"  data-placeholder="Select Status" >
                                            <option value="">Select Status</option>
                                            @foreach($statusClass as $status => $colors)
                                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <input type="text" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker" name="daterange" placeholder="From Date - To Date" value="{{ request('daterange') }}">
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="lawyer_id" class="select2 form-control ih-small ip-gray radius-xs b-deep px-15" data-placeholder="Select Lawyer">
                                            <option value="">All Lawyers</option>
                                            @foreach($lawyers as $lawyer)
                                                <option value="{{ $lawyer->id }}" {{ request('lawyer_id') == $lawyer->id ? 'selected' : '' }}>
                                                    {{ $lawyer->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group mt-2  mb-1">
                                        <select name="consultation_type" class="form-control ih-small ip-gray radius-xs b-deep px-15"  data-placeholder="Select Consultation Type">
                                            <option value="">All Consultation Types</option>
                                            <option value="normal" {{ request()->consultation_type == 'normal' ? 'selected' : '' }}>Normal</option>
                                            <option value="vip" {{ request()->consultation_type == 'vip' ? 'selected' : '' }}>VIP</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group mt-2 mb-1">
                                        <select name="specialities" id="select-tag" class="form-control select2 ip-gray radius-xs b-light px-15"  data-placeholder="Select Case Type">
                                            <option value="">All Case Type</option>
                                            @foreach($dropdowns['specialities']->options as $option)
                                                <option value="{{ $option->id }}" {{ request()->specialities == $option->id ? 'selected' : '' }}>
                                                    {{ $option->translations->first()->name ?? 'Unnamed' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group mt-2 mb-1">
                                        <select name="language" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" id="select-tag2"  data-placeholder="Select Language">
                                            <option value="">All Languages</option>
                                            @foreach($dropdowns['languages']->options as $option)
                                                <option value="{{ $option->id }}"  {{ request()->language == $option->id ? 'selected' : '' }}>
                                                    {{ $option->translations->first()->name ?? 'Unnamed' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-3 mb-1 mt-2 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">
                                            {{-- <i class="fas fa-filter"></i> --}}
                                            Filter
                                        </button>
                                        <a href="{{ route('consultations.index') }}" class="btn btn-secondary btn-square btn-sm ml-2">
                                            {{-- <i class="fas fa-sync-alt"></i> --}}
                                            Reset
                                        </a>

                                        @if($exportPermissionName && auth()->user()->can($exportPermissionName))
                                            <a href="{{ route('consultations.export',request()->all()) }}" class="btn btn-warning btn-sm ml-2">
                                                
                                                Export
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <div class="d-flex flex-wrap mt-3">
                                    @foreach($statusClass as $status => $colors)
                                        <div class="d-flex align-items-center mr-4 mb-2">
                                            <div style="width:17px;
                                                        height:14px;
                                                        border-radius:15%;
                                                        background-color: {{ $colors['bg'] }};
                                                        border:1px solid #999;
                                                        margin-right:6px;"></div>
                                            <span style="font-size:13px; color:#444;">
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                <table class="table table-bordered table-basic mb-0">
                                    <thead class="userDatatable-header">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Reference Code</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Lawyer</th>
                                            <th class="text-center">Consultation Type</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Total Duration</th>
                                            <th class="text-center">Total Amount</th>
                                            <th class="text-center">Consultation Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($consultations as $key => $consultation)
                                            
                                            <tr>
                                                <td class="text-center">{{ $consultations->firstItem() + $key }}</td>
                                                <td class="text-center">
                                                    {{ $consultation->ref_code ?? '-' }}
                                                </td>
                                                
                                                <td class="text-center">
                                                    {{ $consultation->user?->name ?? '—' }}

                                                    <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual"
                                                    title='<div class="popover-title">User Info</div>'
                                                    data-content='
                                                            <div class="custom-popover">
                                                                <div class="popover-item"><i class="fas fa-user"></i> {{ $consultation->user?->name }}</div>
                                                                <div class="popover-item"><i class="fas fa-envelope"></i> {{ $consultation->user?->email }}</div>
                                                                <div class="popover-item"><i class="fas fa-phone"></i> {{ $consultation->user?->phone }}</div>
                                                            </div>
                                                        '></i>

                                                </td>
                                                <td class="text-center">
                                                    {{ $consultation->lawyer?->full_name ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ ucfirst($consultation->consultant_type) ?? '-' }}
                                                </td>
                                                 <td class="text-center">
                                                    @php
                                                        $status = $consultation->status ?? '';
                                                        $bgColor = $statusClass[$status]['bg'] ?? '#e0e0e0';
                                                        $textColor = $statusClass[$status]['text'] ?? '#000000';
                                                    @endphp
                                                    <span class="badge " style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                                        {{ ucwords(str_replace('_', ' ', $status)) ?? ucwords($status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $consultation->duration ?? 0 }} <small>Mins</small></td>
                                                <td class="text-center"><small>AED</small> {{ number_format($consultation->amount, 2) }}</td>

                                                <td class="text-center">{{ date('d, M Y h:i A', strtotime($consultation->created_at)) }}</td>

                                                <td class="text-center">
                                                    @can('view_consultation_requests')
                                                        <div class="table-actions">
                                                            <a href="{{ route('consultations.show', $consultation->id) }}" class="">
                                                                <span data-feather="eye"></span>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($consultations->isEmpty())
                                            <tr>
                                                <td colspan="8" class="text-center">No service requests found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    {{ $consultations->appends(request()->input())->links('pagination::bootstrap-5') }}
                                </div>
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
