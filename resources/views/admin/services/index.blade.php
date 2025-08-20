@extends('layouts.admin_default', ['title' => 'All Services'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Services</h4>
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

                            <form method="GET" action="{{ route('services.index') }}" autocomplete="off">
                                <div class="row mb-2">

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

                                    <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('services.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead class="userDatatable-header">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Icon</th>
                                            <th>Title</th>
                                            <th class="text-center">Parent Sevice</th>
                                            <th class="text-center">Sort Order</th>
                                            <th class="text-center">Status</th>

                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($services as $key => $service)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">
                                                    <img src="{{ asset(getUploadedImage($service->icon)) }}"
                                                        class="card-img-top" style="height: 45px; object-fit: contain;"
                                                        alt="{{ $service->name }}" />
                                                </td>
                                                <td>
                                                    {{ $service->name ?? '—' }} {!! ($service->payment_active == 1) ? '<small class="text-primary"> &nbsp;&nbsp; (Paid)</small>' : '' !!}
                                                </td>

                                                <td class="text-center">
                                                    {{ $service->parent?->name ?? '—' }}
                                                </td>
                                                <td class="text-center">{{ $service->sort_order }}</td>
                                                <td class="text-center">
                                                    @can('edit_service')
                                                        <div class="atbd-switch-wrap">
                                                            <div
                                                                class="custom-control custom-switch switch-secondary switch-sm ">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="switch-s1_{{ $key }}"
                                                                    onchange="update_status(this)" value="{{ $service->id }}"
                                                                    <?php if ($service->status == 1) {
                                                                        echo 'checked';
                                                                    } ?>>
                                                                <label class="custom-control-label"
                                                                    for="switch-s1_{{ $key }}"></label>
                                                            </div>
                                                        </div>
                                                    @endcan
                                                </td>

                                                <td class="text-center">
                                                    @can('edit_service')
                                                        <div class="table-actions">
                                                            <a href="{{ route('services.edit', $service->id) }}"
                                                                title="Edit Service">
                                                                <span data-feather="edit"></span>
                                                            </a>
                                                            @if($service->slug === 'expert-report' )
                                                                <a href="{{ route('expert-pricing.index') }}" title="Edit Export Report Pricing">
                                                                    <span data-feather="credit-card"></span>
                                                                </a>
                                                            @elseif ($service->slug === 'request-submission')
                                                                <a href="{{ route('request-pricing.index') }}" title="Edit Request Submission Pricing">
                                                                    <span data-feather="credit-card"></span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($services->isEmpty())
                                            <tr>
                                                <td colspan="7" class="text-center">No services found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">

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
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('services.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Service status updated successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);

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
