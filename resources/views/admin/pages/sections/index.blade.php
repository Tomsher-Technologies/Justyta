@extends('layouts.admin_default', ['title' => 'Manage Page Sections - ' . $page->name])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Manage Page Sections: {{ $page->name }}</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <div class="action-btn d-flex" style="gap: 10px;">
                            <a href="{{ route('pages.index') }}" class="btn btn-sm btn-secondary btn-add">
                                <i class="la la-arrow-left"></i> Back to Pages</a>
                            <a href="{{ route('pages.sections.create', $page) }}" class="btn btn-sm btn-primary btn-add">
                                <i class="la la-plus"></i> Add New Section</a>
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
                                            <th class="text-center" width="10%">#</th>
                                            <th>Section Key</th>
                                            <th>Type</th>
                                            <th>Title (EN)</th>
                                            <th class="text-center">Order</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($sections->isNotEmpty())
                                            @foreach ($sections as $key => $section)
                                                <tr>
                                                    <td class="text-center">{{ $key + 1 }}</td>
                                                    <td>{{ $section->section_key }}</td>
                                                    <td><span class="badge badge-info">{{ $section->section_type }}</span></td>
                                                    <td>{{ $section->translation('en')->title ?? '-' }}</td>
                                                    <td class="text-center">{{ $section->order }}</td>
                                                    <td class="text-center">
                                                        <div class="atbd-switch-wrap">
                                                            <div class="custom-control custom-switch switch-secondary switch-sm ">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="switch-s1_{{ $key }}"
                                                                    onchange="update_status(this)"
                                                                    value="{{ $section->id }}" @if ($section->status == 1) checked @endif>
                                                                <label class="custom-control-label"
                                                                    for="switch-s1_{{ $key }}"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="table-actions">
                                                            <a href="{{ route('pages.sections.edit', [$page, $section]) }}" title="Edit Section">
                                                                <span data-feather="edit"></span>
                                                            </a>
                                                            <form id="delete-form-{{ $section->id }}"
                                                                action="{{ route('pages.sections.destroy', [$page, $section]) }}"
                                                                method="POST" style="display:none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                            <a href="javascript:void(0)" onclick="confirmDelete({{ $section->id }})"
                                                                title="Delete Section">
                                                                <span data-feather="trash-2"></span>
                                                            </a>
                                                        </div>
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
                                                        <p class="">No Sections Available</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
@endsection

@section('script')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('page-sections.update-status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Section status updated successfully');
                } else {
                    toastr.error('Something went wrong');
                }
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
