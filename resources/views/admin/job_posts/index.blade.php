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
                            <form method="GET" action="{{ route('job-posts.index') }}" autocomplete="off">
                                <div class="row mb-2">

                                    <div class="col-md-3 mb-1">
                                        <div class="form-group position-relative">
                                            <label class="form-label">Search Keyword</label><br>
                                            <input type="text" name="keyword" value="{{ request('keyword') }}"
                                                class="col-md-12 form-control ih-small ip-gray radius-xs b-light px-15"
                                                placeholder="Search with title or reference no.">
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <div class="form-group position-relative">
                                            <label for="daterange" class="form-label">Posted Date</label>
                                            <input type="text"
                                                name="posted_date"
                                                id="daterange"
                                                value="{{ request('posted_date') }}"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 daterange"
                                                placeholder="YYYY-MM-DD - YYYY-MM-DD">
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <div class="form-group position-relative">
                                            <label class="form-label">Status</label>
                                            <select name="status"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15">
                                                <option value="">--Select Status--</option>
                                                <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <div class="form-group position-relative">
                                            <label class="form-label">Job Post owner</label>
                                            <select name="user_id" class="form-control  ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                                <option value="">-- Select Owner --</option>
                                                @foreach ($users as $us)
                                                    <option value="{{ $us->id }}"
                                                        {{ request('user_id') == $us->id ? 'selected' : '' }}>
                                                        {{ $us->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                     
                                    <div class="col-md-3 mb-1">
                                        <div class="form-group position-relative">
                                            <label class="form-label" for="daterange1">Deadline Date</label>
                                            <input type="text" name="deadline_date" id="daterange1" value="{{ request('deadline_date') }}"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 daterange"
                                                placeholder="YYYY-MM-DD - YYYY-MM-DD">
                                        </div>
                                    </div>

                                    <div class="col-md-3 ">
                                        <div class="form-group mt-4 d-flex flex-wrap align-items-end">
                                            <button class="btn btn-primary btn-sm mt-1" type="submit">Filter</button>
                                            <a href="{{ route('job-posts.index') }}"
                                                class="btn btn-secondary btn-square btn-sm ml-2 mt-1">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">#</th>
                                            <th class="text-center">Ref No.</th>
                                            <th class="w-20">Title</th>
                                            {{-- <th class="text-center">Job Type</th>
                                            <th class="text-center">Location</th> --}}
                                            <th class="text-center">Posted Date</th>
                                            <th class="text-center">Deadline Date</th>
                                            <th class="text-center">Applicants Count</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Post Owner</th>
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
                                                        <td class="text-center">{{ $job->ref_no ?? '-' }}</td>
                                                        <td>{{ $job->translation('en')->title ?? '-' }}</td>
                                                        {{-- <td class="text-center">
                                                            {{ ucfirst(str_replace('_', ' ', $job->type)) }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ optional($job->location)->getTranslation('name') ?? '-' }}
                                                        </td> --}}

                                                        <td class="text-center">
                                                            {{ $job->job_posted_date ? \Carbon\Carbon::parse($job->job_posted_date)->format('d M Y') : '-' }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $job->deadline_date ? \Carbon\Carbon::parse($job->deadline_date)->format('d M Y') : '-' }}
                                                        </td>

                                                        <td class="text-center">
                                                            <a href="{{ route('job-applications', ['id' => base64_encode($job->id)]) }}">
                                                                {{ $job->applications?->count() ?? 0 }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            @can('edit_job_post')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $job->id }}" <?php if ($job->status == 1) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="custom-control-label p-0"
                                                                            for="switch-s1_{{ $key }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endcan
                                                        </td>

                                                        <td class="text-center">
                                                            {{ optional($job->post_owner)->name ?? '-'  }}
                                                        </td>

                                                        <td class="text-center">
                                                            <div class="table-actions">
                                                                @can('edit_job_post')
                                                                    <a href="{{ route('job-posts.edit', $job) }}" title="Edit Job">
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
                                    @can('view_job_post')
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
        $('.daterange').daterangepicker({
            opens: 'right',      // or 'left', 'center'
            drops: 'down',
            parentEl: 'body',
            autoUpdateInput: false, // Prevents auto-filling
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        // Update the input only after a date is selected
        $('.daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        // Clear the input when cancel is clicked
        $('.daterange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#daterange1').on('show.daterangepicker', function(ev, picker) {
            // For example, force top to 50px or some calculated value
            picker.container.css('top', '50px');
        });

        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('job-posts.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Job post status updated successfully');
                    // setTimeout(function() {
                    //     window.location.reload();
                    // }, 2000);

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
