@extends('layouts.admin_default', ['title' => 'All Job Posts'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">{{ $job->translation('en')->title }}</h4>
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
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">#</th>
                                            <th>Full Name</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Phone</th>
                                            <th class="text-center">Position</th>
                                            <th class="text-center">Resume</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($applications as $key => $application)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $key + 1 + ($applications->currentPage() - 1) * $applications->perPage() }}
                                                </td>
                                                <td>{{ $application->full_name }}</td>
                                                <td class="text-center">{{ $application->email }}</td>
                                                <td class="text-center">{{ $application->phone }}</td>
                                                <td class="text-center">{{ $application->currentPostion?->getTranslatedName('title', 'en') ?? '-'  }}</td>
                                                <td class="text-center">
                                                    @if($application->resume_path)
                                                        <a href="{{ asset($application->resume_path) }}" target="_blank">Download</a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">No applicants yet.</td>
                                            </tr>
                                        @endforelse
                                        @can('view_job_post')
                                           
                                        @endcan
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    @can('view_job_post')
                                        {{ $applications->appends(request()->input())->links('pagination::bootstrap-5') }}
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

    </script>
@endsection
