@extends('layouts.admin_default',['title' => 'All Roles'])

@section('content')

 <div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"> Roles & Permissions</h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    
                    <div class="action-btn">
                        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary btn-add">
                            <i class="la la-plus"></i> Add New Role</a>
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
                        <div class="table-responsive">
                            <table class="table table-bordered table-basic mb-0">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th width="10%" class="text-center">#</th>
                                        <th>Role Name</th>
                                        <th class="text-center" width="">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @can('view_role')
                                        @foreach($roles as $key => $role)
                                            <tr>
                                                <td class="text-center">{{ ($key+1) + ($roles->currentPage() - 1)*$roles->perPage() }}</td>
                                                <td>{{ $role->name}}</td>
                                                <td class="text-center">
                                                    @can('edit_role')
                                                        <div class="table-actions">
                                                            <a href="{{route('roles.edit', ['id'=>$role->id] )}}" title="Edit Role">
                                                                <span data-feather="edit"></span>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endcan
                                </tbody>
                            </table>
                            <div class="aiz-pagination mt-4">
                                @can('view_role')
                                    {{ $roles->appends(request()->input())->links('pagination::bootstrap-5') }}
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

