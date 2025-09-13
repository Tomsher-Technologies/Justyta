@extends('layouts.admin_default', ['title' => 'Training Requests'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Training Requests</h4>
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

                            <form method="GET" action="{{ route('training-requests.index') }}" autocomplete="off">
                                <div class="row mb-2">

                                    <div class="col-md-3 input-group  mt-2 mb-1">
                                        <select name="emirate_id" class="select2 form-control ih-small ip-gray radius-xs b-deep px-15"  data-placeholder="Select Emirate" >
                                            <option value="">--Select Emirate--</option>
                                             @foreach($response['emirates'] as $res)
                                                <option value="{{ $res['id'] }}"  {{ request('emirate_id') == $res['id'] ? 'selected' : '' }}>
                                                    {{ $res['value'] ?? '---' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                        
                                    <div class="col-md-3 input-group mt-2  mb-1">
                                        <select name="position" class="select2 form-control ih-small ip-gray radius-xs b-deep px-15"  data-placeholder="Select Position" >
                                            <option value="">--Select Position--</option>
                                             @foreach($response['training_positions'] as $res)
                                                <option value="{{ $res['id'] }}"  {{ request('position') == $res['id'] ? 'selected' : '' }}>
                                                    {{ $res['value'] ?? '---' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group mt-2  mb-1">
                                        <select name="residency_status" class="select2 form-control ih-small ip-gray radius-xs b-deep px-15"  data-placeholder="Select Residency Status" >
                                            <option value="">--Select Residency Status--</option>
                                             @foreach($response['residency_status'] as $res)
                                                <option value="{{ $res['id'] }}"  {{ request('residency_status') == $res['id'] ? 'selected' : '' }}>
                                                    {{ $res['value'] ?? '---' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-1 mt-2 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('training-requests.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>

                                        @if(request('service_id'))
                                            <a href="{{ route('training-requests.export', request()->all()) }}"
                                                class="btn btn-warning btn-sm ml-2">
                                                Export
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead class="userDatatable-header">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">User Name</th>
                                            <th class="text-center">Emirate</th>
                                            <th class="text-center">Position</th>
                                            <th class="text-center">Residency Status</th>
                                            <th class="text-center">Starting Date</th>
                                            <th class="text-center w-20">Documents</th>
                                            {{-- <th class="text-center">Status</th> --}}
                                            <th class="text-center">Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 0;
                                        @endphp
                                        @forelse($requests as $key => $req)
                                            @php
                                                $statusClass = [
                                                    'pending' => 'badge-gray',
                                                    'selected' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                ];
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">
                                                    {{ $req->user?->name ?? ''}}

                                                    <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual"
                                                    title='<div class="popover-title">User Info</div>'
                                                    data-content='
                                                            <div class="custom-popover">
                                                                <div class="popover-item"><i class="fas fa-user"></i> {{ $req->user?->name }}</div>
                                                                <div class="popover-item"><i class="fas fa-envelope"></i> {{ $req->user?->email }}</div>
                                                                <div class="popover-item"><i class="fas fa-phone"></i> {{ $req->user?->phone }}</div>
                                                            </div>
                                                        '></i>

                                                </td>
                                                <td>
                                                    {{ $req->emirate?->name }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $req->positionOption?->getTranslation('name'); }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $req->residencyStatusOption?->getTranslation('name'); }}
                                                </td>

                                                <td class="text-center">
                                                    {{ date('d, M Y', strtotime($req->start_date)) }}
                                                </td>
                                                
                                                 <td>
                                                    @php
                                                        $documents = $req->documents;
                                                    @endphp
                                                    @foreach($documents as $file)
                                                        @php
                                                            $i++;
                                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                        @endphp

                                                        @if(in_array($ext, ['png', 'jpg', 'jpeg', 'webp']))
                                                            <a href="{{ asset(getUploadedImage($file)) }}" data-lightbox="image{{ $i }}">
                                                                <img src="{{ asset(getUploadedImage($file)) }}"  alt="doc" width="50" class="mb-1" />
                                                            </a>
                                                        @elseif($ext == 'pdf')
                                                            <a href="{{ asset(getUploadedImage($file)) }}" target="_blank" class="d-inline-block text-danger mr-2">
                                                                <i class="fas fa-file-pdf fa-2x"></i>
                                                            </a>
                                                        @elseif(in_array($ext, ['doc', 'docx']))
                                                            <a href="{{ asset(getUploadedImage($file)) }}" target="_blank" class="d-inline-block text-primary mr-2">
                                                                <i class="fas fa-file-word fa-2x"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ asset(getUploadedImage($file)) }}" target="_blank" class="d-inline-block text-secondary mr-2">
                                                                <i class="fas fa-file fa-2x"></i>
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                
                                                {{-- <td class="text-center">
                                                    <span class="badge badge-pill {{ $statusClass[$req->status] ?? 'badge-secondary' }}">
                                                        {{ ucfirst($req->status) }}
                                                    </span>
                                                </td> --}}

                                                <td class="text-center">{{ date('d, M Y h:i A', strtotime($req->created_at)) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No training requests found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    {{ $requests->appends(request()->input())->links('pagination::bootstrap-5') }}
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
    <script src="{{ asset('assets/js/lightbox.min.js') }}"></script>
    <script type="text/javascript">
        lightbox.option({
            'resizeDuration'    : 200,
            'wrapAround'        : true,
            'disableScrolling'  : true
        });

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
