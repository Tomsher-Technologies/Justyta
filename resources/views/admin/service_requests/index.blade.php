@extends('layouts.admin_default', ['title' => 'Service Requests'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Service Requests</h4>
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

                            <form method="GET" action="{{ route('service-requests.index') }}" autocomplete="off">
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

                                    <div class="col-md-4 input-group  mb-1">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                                            class="form-control ih-small ip-gray radius-xs b-deep px-15"
                                            placeholder="Search with Reference Code">
                                    </div>

                                    <div class="col-md-3 input-group mt-2  mb-1">
                                        <select name="status"
                                            class="form-control ih-small ip-gray radius-xs b-deep px-15">
                                            <option value="">Select Request Status</option>
                                            <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="ongoing" {{ request()->status == 'ongoing' ? 'selected' : '' }}>Ongoing
                                            </option>
                                            <option value="completed" {{ request()->status == 'completed' ? 'selected' : '' }}>Completed
                                            </option>
                                            <option value="rejected" {{ request()->status == 'rejected' ? 'selected' : '' }}>Rejected
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group mt-2  mb-1">
                                        <select name="payment_status"
                                            class="form-control ih-small ip-gray radius-xs b-deep px-15">
                                            <option value="">Select Payment Status</option>
                                            <option value="pending" {{ request()->payment_status == 'pending' ? 'selected' : '' }}>Unpaid
                                            </option>
                                            <option value="success" {{ request()->payment_status == 'success' ? 'selected' : '' }}>Paid
                                            </option>
                                            <option value="partial" {{ request()->payment_status == 'partial' ? 'selected' : '' }}>Partially Paid
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-1 mt-2 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('service-requests.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>

                                        @if(request('service_id'))
                                            @can('export_service_requests')
                                                <a href="{{ route('service-requests.export', ['service_id' => request('service_id')] + request()->all()) }}"
                                                    class="btn btn-warning btn-sm ml-2">
                                                    Export
                                                </a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead class="userDatatable-header">
                                        <tr>
                                            <th class="text-center">#</th>
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
                                        @foreach ($serviceRequests as $key => $serviceReq)
                                            @php
                                                $statusClass = [
                                                    'pending' => 'badge-gray',
                                                    'ongoing' => 'badge-warning',
                                                    'completed' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                ];
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">
                                                    {{ $serviceReq->reference_code ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $serviceReq->service?->name ?? '—' }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $serviceReq->user?->name ?? '—' }}
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
                                        @endforeach
                                        @if ($serviceRequests->isEmpty())
                                            <tr>
                                                <td colspan="8" class="text-center">No service requests found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    {{ $serviceRequests->appends(request()->input())->links('pagination::bootstrap-5') }}
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
        
    </script>
@endsection
