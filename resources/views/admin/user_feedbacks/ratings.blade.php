@extends('layouts.admin_default', ['title' => 'User Ratings'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">User Ratings</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table4  bg-white mb-20">

                            <form method="GET" action="{{ route('user-ratings.feedback') }}" autocomplete="off">
                                <div class="row mb-2">
                                    <div class="col-md-4 input-group  mt-2 mb-1">
                                        <input type="text" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker" name="daterange" placeholder="From Date - To Date" value="{{ request('daterange') }}">
                                    </div>

                                    <div class="col-md-3 mb-1 mt-2 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('user-ratings.feedback') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>

                                        @can('export_user_ratings')
                                            <a href="{{ route('user-ratings.export', request()->query()) }}"
                                                class="btn btn-warning btn-sm ml-2">
                                                Export
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead class="userDatatable-header">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="">User Info</th>
                                            {{-- <th class="text-center">Email</th>
                                            <th class="text-center">Phone</th> --}}
                                            <th class="text-center">Rating</th>
                                            <th class="text-start w-40">Comment</th>
                                            <th class="text-center">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @forelse($ratings as $key => $rat)
                                            
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    {{ $rat->user?->name ?? ''}}

                                                    <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual"
                                                    title='<div class="popover-title">User Info</div>'
                                                    data-content='
                                                            <div class="custom-popover">
                                                                {{-- <div class="popover-item"><i class="fas fa-user"></i> {{ $rat->user?->name }}</div> --}}
                                                                <div class="popover-item"><i class="fas fa-envelope"></i> {{ $rat->user?->email }}</div>
                                                                <div class="popover-item"><i class="fas fa-phone"></i> {{ $rat->user?->phone }}</div>
                                                            </div>
                                                        '></i>
                                                </td>

                                                <td class="text-center">
                                                    {{ $rat->rating }}
                                                </td>

                                                <td>
                                                    {{ $rat->comment }}
                                                </td>

                                                <td class="text-center">{{ date('d, M Y h:i A', strtotime($rat->created_at)) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No data found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    {{ $ratings->appends(request()->input())->links('pagination::bootstrap-5') }}
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
    <link href="{{ asset('assets/css/lightbox.min.css') }}" rel="stylesheet">
    <style>

        
        .popover-header {
            background-color: var(--secondary);
            /*#e2d8bf*/
            font-size: 13px;
        }

        .popover {
            background-color: #ffffff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            min-width: 200px;
        }

        .custom-popover {
            font-size: 14px;
            color: #333;
        }

        .popover-title {
            font-weight: 700;
            /* margin-bottom: 8px; */
            color: var(--primary);
            /* border-bottom: 1px solid #e9ecef;
             padding-bottom: 4px; */
        }

        .custom-popover .popover-item i {
            color: var(--primary);
            margin-right: 8px;
        }
    </style>
@endsection

@section('script_first')
    <script src="{{ asset('assets/js/bootstrap/popper.js') }}"></script>
@endsection


@section('script')
    <script type="text/javascript">
        $(function() {
            
            $('.popover-toggle').popover();

            // Show on hover/focus
            $('.popover-toggle').on('mouseenter focus', function() {
                $('.popover-toggle').not(this).popover('hide'); // hide others
                $(this).popover('show');
            });

            // Hide on mouseleave or blur only if not hovering popover
            $('.popover-toggle').on('mouseleave blur', function() {
                let _this = this;
                setTimeout(function() {
                    if (!$('.popover:hover').length) {
                        $(_this).popover('hide');
                    }
                }, 200);
            });

            // Keep popover open on hover
            $(document).on('mouseenter', '.popover', function() {
                clearTimeout(window._popoverTimeout);
            });

            $(document).on('mouseleave', '.popover', function() {
                $('[data-toggle="popover"]').popover('hide');
            });

            // Re-render Feather if used
            $(document).on('shown.bs.popover', function() {
                if (window.feather) feather.replace();
            });
        });
    </script>
@endsection
