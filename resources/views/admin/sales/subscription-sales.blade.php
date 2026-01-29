@extends('layouts.admin_default', ['title' => 'Subscription Sales Report'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Subscription Sales Report</h4>
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
                                $exportPermissionName = null;
                            @endphp

                            <form method="GET" action="{{ route('admin.subscription-sales') }}" autocomplete="off">
                                <div class="row mb-2">

                                    <div class="col-md-3 input-group  mb-1">
                                        <input type="text"
                                            class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker"
                                            name="daterange" placeholder="From Date - To Date"
                                            value="{{ request('daterange') }}">
                                    </div>

                                    <div class="col-md-2 input-group  mb-1">
                                        <select name="plan_id" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default">
                                            <option value="">-- Select Plan --</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->title }}
                                                </option>
                                            @endforeach
                                        </select>   
                                    </div>
                                    <div class="col-md-4 input-group  mb-1">
                                        <select name="vendor_id" class="select2 form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default" data-placeholder="-- Select Law Firm --">
                                            <option value="">-- Select Law Firm --</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                    {{ $vendor->law_firm_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-1">
                                        <select name="status" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default">
                                            <option value="">-- Select Status --</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-1  d-flex flex-wrap align-items-end mt-1">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('admin.subscription-sales') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>

                                        <a href="{{ route('subscription-sales.export', request()->all()) }}"
                                            class="btn btn-warning btn-sm ml-2">
                                            Export
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive table4">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">#</th>
                                            <th class="text-center" style="width: 30%">Law Firm</th>
                                            <th class="text-center">Plan</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">StartDate</th>
                                            <th class="text-center">EndDate</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $statusClass = [
                                                'cancelled' => 'badge-gray',
                                                'pending' => 'badge-warning',
                                                'active' => 'badge-success',
                                                'expired' => 'badge-danger',
                                            ];
                                        @endphp
                                        @forelse($subscriptionSales as $key => $subReq)
                                            <tr>
                                                <td class="text-center">{{ $subscriptionSales->firstItem() + $key }}</td>
                                                <td>
                                                    {{ $subReq->vendor?->law_firm_name ?? '—' }}

                                                    <i class="fas fa-info-circle text-primary ml-2 popover-toggle"
                                                        tabindex="0" data-toggle="popover" data-placement="bottom"
                                                        data-html="true" data-trigger="manual"
                                                        title='<div class="popover-title">Law Firm Contact Info</div>'
                                                        data-content='
                                                                <div class="custom-popover">
                                                                    <div class="popover-item"><i class="fas fa-envelope"></i> {{ $subReq->vendor->law_firm_email }}</div>
                                                                    <div class="popover-item"><i class="fas fa-phone"></i> {{ $subReq->vendor->law_firm_phone }}</div>

                                                                    <hr>

                                                                    <div class="popover-title">
                                                                        Owner Contact Info
                                                                    </div>
                                                                        <hr>
                                                                    <div class="popover-item"><i class="fas fa-user"></i> {{ $subReq->vendor->owner_name }}</div>
                                                                    <div class="popover-item"><i class="fas fa-envelope"></i> {{ $subReq->vendor->owner_email }}</div>
                                                                    <div class="popover-item"><i class="fas fa-phone"></i> {{ $subReq->vendor->owner_phone }}</div>
                                                                </div>
                                                            '></i>
                                                </td>
                                                <td class="text-center">
                                                    {{ $subReq->plan->title ?? 'N/A' }}
                                                   
                                                </td>
                                                <td class="text-center"><small>AED </small>{{ number_format($subReq->amount, 2) }}</td>

                                                <td class="text-center" >
                                                    {{ $subReq->subscription_start ? \Carbon\Carbon::parse($subReq->subscription_start)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td class="text-center" >
                                                    {{ $subReq->subscription_end ? \Carbon\Carbon::parse($subReq->subscription_end)->format('d-m-Y') : '-' }}
                                                </td>

                                                <td class="text-center">
                                                    @if($subReq->status == 'pending')
                                                        <span class="badge badge-warning">Payment Pending</span>
                                                    @elseif($subReq->status == 'cancelled')
                                                        <span class="badge badge-danger">Cancelled</span>
                                                    @elseif($subReq->status == 'active')
                                                        <span class="badge badge-success">Active</span>
                                                    @elseif($subReq->status == 'expired')
                                                        <span class="badge badge-danger">Expired</span>
                                                    @endif
                                                </td>
                                               
                                                <td class="text-center">
                                                    {{ date('d, M Y h:i A', strtotime($subReq->created_at)) }}</td>
                                              
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No data found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="mt-4">
                                    {{ $subscriptionSales->appends(request()->input())->links('pagination::bootstrap-5') }}
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
