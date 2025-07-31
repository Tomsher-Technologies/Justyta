@extends('layouts.admin_default', ['title' => 'All News'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All News</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        @can('add_news')
                            <div class="action-btn">
                                <a href="{{ route('news.create') }}" class="btn btn-sm btn-primary btn-add">
                                    <i class="la la-plus"></i> Add New News</a>
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
                            <form method="GET" action="{{ route('news.index') }}" autocomplete="off">
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
                                        <a href="{{ route('news.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center" width="10%">#</th>
                                            <th class="w-45">Title</th>
                                            <th class="text-center">Image</th>
                                            <th class="text-center">News Date</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_news')
                                            @if ($news->isNotEmpty())
                                                @foreach ($news as $key => $nw)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($news->currentPage() - 1) * $news->perPage() }}
                                                        </td>
                                                        <td>{{ $nw->translate('en')->title ?? '-' }}</td>
                                                        <td class="text-center"><img src="{{ asset(getUploadedImage($nw->image)) }}"
                                                                width="150"></td>
                                                        <td class="text-center">
                                                            {{ $nw->news_date ? \Carbon\Carbon::parse($nw->news_date)->format('d M Y') : '-' }}
                                                        </td>

                                                        <td class="text-center">
                                                            @can('edit_news')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $nw->id }}" <?php if ($nw->status == 1) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="custom-control-label"
                                                                            for="switch-s1_{{ $key }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endcan
                                                        </td>
                                                        <td class="text-center">

                                                            <div class="table-actions">
                                                                @can('edit_news')
                                                                    <a href="{{ route('news.edit', $nw->id) }}" title="Edit News">
                                                                        <span data-feather="edit"></span>
                                                                    </a>
                                                                @endcan

                                                                @can('delete_news')
                                                                    <form id="delete-form-{{ $nw->id }}"
                                                                        action="{{ route('news.destroy', $nw->id) }}"
                                                                        method="POST" style="display:none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>

                                                                    <a href="javascript:void(0)"
                                                                        onclick="confirmDelete({{ $nw->id }})"
                                                                        title="Delete News">
                                                                        <span data-feather="trash-2"></span>
                                                                    </a>
                                                                @endcan
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">
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
                                    @can('view_news')
                                        {{ $news->appends(request()->input())->links('pagination::bootstrap-5') }}
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
            $.post('{{ route('news.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('News status updated successfully');
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
                    // Submit the hidden form to delete the news
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
