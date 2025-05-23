@extends('layouts.admin_default', ['title' => 'All Job Posts'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Job Posts</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        @can('add_job_post')
                            <div class="action-btn">
                                <a href="{{ route('job-posts.create') }}" class="btn btn-sm btn-primary btn-add">
                                    <i class="la la-plus"></i> Add New Job Post</a>
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
                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center" width="10%">#</th>
                                            <th class="w-20">Title</th>
                                            <th class="text-center">Job Type</th>
                                            <th class="text-center">Location</th>
                                            <th class="text-center">Posted Date</th>
                                            <th class="text-center">Deadline Date</th>
                                            <th class="text-center">Applicants Count</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_job_post')
                                            @if ($job_posts->isNotEmpty())
                                                @foreach ($job_posts as $key => $job)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($job_posts->currentPage() - 1) * $job_posts->perPage() }}
                                                        </td>
                                                        <td>{{ $job->translate('en')->title ?? '-' }}</td>
                                                        <td class="text-center">
                                                            {{ $job->type }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $job->translate('en')->job_location ?? '-' }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $job->job_posted_date ? \Carbon\Carbon::parse($job->job_posted_date)->format('d M Y') : '-' }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $job->deadline_date ? \Carbon\Carbon::parse($job->deadline_date)->format('d M Y') : '-' }}
                                                        </td>

                                                        <td class="text-center">
                                                            @can('edit_news')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $job->id }}" <?php if ($job->status == 1) {
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
                                                                    <a href="{{ route('news.edit', $job->id) }}" title="Edit News">
                                                                        <span data-feather="edit"></span>
                                                                    </a>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center">
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
                                        {{ $job_posts->appends(request()->input())->links('pagination::bootstrap-5') }}
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
    </script>
@endsection
