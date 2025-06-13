@extends('layouts.admin_default', ['title' => 'All Staffs'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Staffs</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">

                        <div class="action-btn">
                            <a href="{{ route('staffs.create') }}" class="btn btn-sm btn-primary btn-add">
                                <i class="la la-plus"></i> Add New Staff</a>
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
                                <div class="col-md-3 input-group  mb-1">
                                    <input type="text" class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        id="search"
                                        name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                                        placeholder="Type name,email or phone">
                                </div>

                                <div class="col-md-3 input-group  mb-1">
                                    <select name="role_id"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                        <option value="">All Roles</option>
                                        @foreach (Spatie\Permission\Models\Role::where('is_active', 1)->get() as $role)
                                            <option value="{{ $role->name }}"
                                                {{ $role_id == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 input-group  mb-1">
                                    <select name="status" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                        <option value="">--Select Status--</option>
                                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end">
                                    <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                    <a href="{{ route('staffs.index') }}"
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
                                            <th>Role</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_staff')
                                            @if ($users->isNotEmpty())
                                                @foreach ($users as $key => $staff)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                                        <td>{{ $staff->name }}</td>
                                                        <td>{{ $staff->email }}</td>
                                                        <td>{{ $staff->phone }}</td>
                                                        <td>
                                                            {{ $staff->roles->pluck('name')->join(', ') }}
                                                        </td>
                                                        <td class="text-center">
                                                            @can('edit_staff')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $staff->id }}" <?php if ($staff->banned == 0) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="custom-control-label"
                                                                            for="switch-s1_{{ $key }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endcan
                                                        </td>
                                                        <td class="text-center">
                                                            @can('edit_staff')
                                                                <div class="table-actions">
                                                                    <a href="{{ route('staffs.edit', encrypt($staff->id)) }}"
                                                                        title="Edit Staff">
                                                                        <span data-feather="edit"></span>
                                                                    </a>
                                                                </div>
                                                            @endcan

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">
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
