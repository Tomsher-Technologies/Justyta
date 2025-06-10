@extends('layouts.admin_default',['title' => 'All Law Firms'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title">All Law Firms</h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    
                    <div class="action-btn">
                        <a href="{{ route('vendors.create') }}" class="btn btn-sm btn-primary btn-add">
                            <i class="la la-plus"></i> Add New Law Firm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table4  bg-white mb-30">

                        <form method="GET" action="{{ route('vendors.index') }}" >
                            <div class="row mb-2">
                                <div class="col-md-3 input-group  mb-1">
                                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control ih-small ip-gray radius-xs b-light px-15" placeholder="Search name, email or phone">
                                </div>
                                <div class="col-md-3 input-group  mb-1">
                                    <select name="plan_id" class="form-control ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                        <option value="">-- Select Plan --</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end">
                                    <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                    <a href="{{ route('vendors.index') }}" class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-basic mb-0">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th  class="text-center" >#</th>
                                        <th width="25%">Name</th>
                                        <th>Contact Info</th>
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
                                            @foreach($vendors as $key => $vendor)
                                                <tr>
                                                    <td class="text-center">{{ ($key+1) + ($vendors->currentPage() - 1)*$vendors->perPage() }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($vendor->logo)
                                                                <img src="{{ asset(getUploadedImage($vendor->logo)) }}" alt="Logo" class="list-avatar">
                                                            @endif
                                                            {{ $vendor->user->name }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <i data-feather="mail" style="color: #007bff; width: 14px; height: 14px;" class="me-1"></i>
                                                            {{ $vendor->user->email }}
                                                        </div>
                                                        <div>
                                                            <i data-feather="phone" style="color: #28a745; width: 14px; height: 14px;" class="me-1"></i>
                                                            {{ $vendor->user->phone }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ $vendor->currentSubscription->plan->title ?? 'N/A' }}</td>
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
                                                                <div class="custom-control custom-switch switch-secondary switch-sm ">
                                                                    <input type="checkbox" class="custom-control-input" id="switch-s1_{{$key}}" onchange="update_status(this)" value="{{ $vendor->user->id }}" <?php if ($vendor->user->banned == 0) {
                                                                        echo 'checked';
                                                                    } ?>>
                                                                    <label class="custom-control-label" for="switch-s1_{{$key}}"></label>
                                                                </div>
                                                            </div>

                                                        @endcan  
                                                    </td>
                                                    <td class="text-center">
                                                        @can('edit_vendor')
                                                            <div class="table-actions">
                                                                <a href="{{route('vendors.edit', $vendor->id)}}" title="Edit Vendor">
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
    </script>
@endsection