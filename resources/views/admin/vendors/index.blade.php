@extends('layouts.admin_default', ['title' => 'All Law Firms'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Law Firms</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">

                        @can('add_vendor')
                            <div class="action-btn">
                                <a href="{{ route('vendors.create') }}" class="btn btn-sm btn-primary btn-add">
                                    <i class="la la-plus"></i> Add New Law Firm</a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table4  bg-white mb-30">

                            <form method="GET" action="{{ route('vendors.index') }}">
                                <div class="row mb-2">
                                    <div class="col-md-4 input-group  mb-1">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15"
                                            placeholder="Search name, email, phone or reference no.">
                                    </div>
                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="plan_id"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                            <option value="">-- Select Plan --</option>
                                            @foreach ($plans as $plan)
                                                <option value="{{ $plan->id }}"
                                                    {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="status"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15">
                                            <option value="">--Select Status--</option>
                                            <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-1 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('vendors.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">#</th>
                                            <th>Reference No</th>
                                            <th width="25%">Law Firm Name</th>
                                            <th>Owner</th>
                                            <th class="text-center">Plan</th>
                                            <th class="text-center">Start Date</th>
                                            <th class="text-center">End Date</th>
                                            <th class="text-center">Total Members</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_vendor')
                                            @if ($vendors->isNotEmpty())
                                                @foreach ($vendors as $key => $vendor)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($vendors->currentPage() - 1) * $vendors->perPage() }}
                                                        </td>
                                                        <td class="text-center">{{ $vendor->ref_no }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if ($vendor->logo)
                                                                    <img src="{{ asset(getUploadedImage($vendor->logo)) }}"
                                                                        alt="Logo" class="list-avatar">
                                                                @endif
                                                                {{ $vendor->law_firm_name }}
                                                                <i class="fas fa-info-circle text-primary ml-2 popover-toggle"
                                                                    tabindex="0" data-toggle="popover" data-placement="bottom"
                                                                    data-html="true" data-trigger="manual"
                                                                    title='<div class="popover-title">Law Firm Contact Info</div>'
                                                                    data-content='
                                                                        <div class="custom-popover">
                                                                            <div class="popover-item"><i class="fas fa-envelope"></i> {{ $vendor->law_firm_email }}</div>
                                                                            <div class="popover-item"><i class="fas fa-phone"></i> {{ $vendor->law_firm_phone }}</div>
                                                                        </div>
                                                                    '></i>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{ $vendor->owner_name }}
                                                            <i class="fas fa-info-circle text-primary ml-2 popover-toggle"
                                                                tabindex="0" data-toggle="popover" data-placement="bottom"
                                                                data-html="true" data-trigger="manual"
                                                                title='<div class="popover-title">Owner Contact Info</div>'
                                                                data-content='
                                                                        <div class="custom-popover">
                                                                            <div class="popover-item"><i class="fas fa-envelope"></i> {{ $vendor->owner_email }}</div>
                                                                            <div class="popover-item"><i class="fas fa-phone"></i> {{ $vendor->owner_phone }}</div>
                                                                        </div>
                                                                    '></i>
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $vendor->currentSubscription->plan->title ?? 'N/A' }}</td>
                                                        <td class="text-center">
                                                            {{ $vendor->currentSubscription?->subscription_start ? \Carbon\Carbon::parse($vendor->currentSubscription->subscription_start)->format('d M Y') : '-' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $vendor->currentSubscription?->subscription_end ? \Carbon\Carbon::parse($vendor->currentSubscription->subscription_end)->format('d M Y') : '-' }}
                                                        </td>

                                                        <td class="text-center"> 0</td>
                                                        <td class="text-center">
                                                            @can('edit_vendor')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $vendor->user->id }}" <?php if ($vendor->user->banned == 0) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="custom-control-label"
                                                                            for="switch-s1_{{ $key }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endcan
                                                        </td>
                                                        <td class="text-center">
                                                            @can('edit_vendor')
                                                                <div class="table-actions">
                                                                    <a href="{{ route('vendors.edit', $vendor->id) }}"
                                                                        title="Edit Vendor">
                                                                        <span data-feather="edit"></span>
                                                                    </a>
                                                                </div>
                                                            @endcan

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="10" class="text-center">
                                                        <div class="atbd-empty__image">
                                                            <img src="{{ asset('assets/img/svg/1.svg') }}" alt="Empty">
                                                        </div>
                                                        <div class="atbd-empty__text">
                                                            <p class="">No Data Available</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endcan
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    @can('view_staff')
                                        {{ $vendors->appends(request()->input())->links('pagination::bootstrap-5') }}
                                    @endcan
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
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
@endsection

@section('script')
    <script type="text/javascript">
        function update_status(el) {
            if (el.checked) {
                var status = 0;
            } else {
                var status = 1;
            }
            $.post('{{ route('staff.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Staff status updated successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);

                } else {
                    toastr.error('Something went wrong');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                }
            });
        }

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
