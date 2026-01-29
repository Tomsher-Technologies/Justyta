@extends('layouts.admin_default', ['title' => 'All Demo Users'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Users ({{ $totalUsers }})</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">

                        <div class="action-btn">
                         
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

                            <form class="row mb-2" id="sort_brands" action="" method="GET" autocomplete="off">
                                <div class="col-md-4 input-group  mb-1">
                                    <input type="text" class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        id="search"
                                        name="search" value="{{ request('search') }}" placeholder="Type name,email or phone">
                                </div>

                                {{-- <div class="col-md-3 input-group  mb-1">
                                    <select name="status" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                        <option value="">--Select Status--</option>
                                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div> --}}

                                {{-- <div class="col-md-3 input-group  mb-1">
                                    <input type="text"
                                        class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker"
                                        name="daterange" placeholder="From Date - To Date"
                                        value="{{ request('daterange') }}">
                                </div> --}}

                                <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end">
                                    <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                    <a href="{{ route('demo-users.index') }}"
                                        class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center" width="10%">#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            {{-- <th class="text-center">Status</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_users')
                                            @if ($users->isNotEmpty())
                                                @foreach ($users as $key => $us)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                                        <td>{{ $us->name }}</td>
                                                        <td>{{ $us->email }}</td>
                                                        <td>{{ $us->phone }}</td>
                                                        {{-- <td>
                                                            {{ $us->created_at->format('d M, Y h:i A') }}
                                                        </td> --}}
                                                        {{-- <td class="text-center">
                                                            <div class="atbd-switch-wrap">
                                                                <div
                                                                    class="custom-control custom-switch switch-secondary switch-sm ">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="switch-s1_{{ $key }}"
                                                                        onchange="update_status(this)"
                                                                        value="{{ $us->id }}" <?php if ($us->banned == 0) {
                                                                            echo 'checked';
                                                                        } ?>>
                                                                    <label class="custom-control-label"
                                                                        for="switch-s1_{{ $key }}"></label>
                                                                </div>
                                                            </div>
                                                        </td> --}}
                                                       
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center">
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
                                    @can('view_users')
                                        {{ $users->appends(request()->input())->links('pagination::bootstrap-5') }}
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
            $.post('{{ route('user.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('User status updated successfully');
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
