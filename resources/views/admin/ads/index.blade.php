@extends('layouts.admin_default', ['title' => 'All Ads'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Ads</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        @can('add_ads')
                            <div class="action-btn">
                                <a href="{{ route('ads.create') }}" class="btn btn-sm btn-primary btn-add">
                                    <i class="la la-plus"></i> Add New Ad</a>
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
                            <form method="GET" action="{{ route('ads.index') }}" autocomplete="off">
                                <div class="row mb-2">

                                    <div class="col-md-4 input-group mb-1">
                                        <input type="text" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker" name="daterange" placeholder="From Date - To Date" value="{{ request('daterange') }}">
                                    </div>

                                    <div class="col-md-4 input-group mb-1">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                placeholder="Search with title or customer info.">
                                    </div>

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

                                    <div class="col-md-4 input-group  mb-1 mt-2">
                                        <select name="page_id" data-placeholder="Select Page" class="form-control select2 ih-small ip-gray radius-xs b-light px-15">
                                            <option value="">--Select Page--</option>
                                            @foreach($pages as $page)
                                                <option value="{{ $page->id }}" {{ request()->page_id == $page->id ? 'selected' : '' }}>{{ $page->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end mt-2">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('ads.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">#</th>
                                            <th class="text-center">Title</th>
                                            <th class="text-center">Page</th>
                                            <th class="text-start">Customer Info</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Web File</th>
                                            <th class="text-center">Mobile File</th>
                                            {{-- <th class="text-center">Url</th> --}}
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_ads')
                                            @forelse($ads as $key => $ad)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $key + 1 + ($ads->currentPage() - 1) * $ads->perPage() }}
                                                    </td>
                                                    <td class="text-center">{{ $ad->title }}</td>
                                                    <td class="text-center">{{ $ad->page->name ?? '-' }}</td>
                                                   
                                                    <td class="text-start">
                                                        @if($ad->customer_name)
                                                            <strong>Name : </strong> {{ $ad->customer_name }}<br>
                                                        @endif
                                                        @if($ad->customer_email)
                                                            <strong>Email : </strong> {{ $ad->customer_email }}<br>
                                                        @endif
                                                        @if($ad->customer_phone)
                                                            <strong>Phone : </strong> {{ $ad->customer_phone }}<br>
                                                        @endif
                                                    </td>
                                                     <td class="text-center">
                                                        {{ $ad->start_date->format('d M, Y') }}<br>
                                                        to<br>
                                                        {{ $ad->end_date->format('d M, Y') }}
                                                    </td>
                                                     
                                                     @foreach($ad->files as $i => $file)
                                                        <td>
                                                            @php
                                                                $path = asset($file->file_path) . '?v=' . time();
                                                            @endphp
                                                            @if($file->file_type === 'video')
                                                                <a href="{{ $path }}" class="glightbox" data-type="video" data-gallery="row-{{ $key }}{{ $i }}">
                                                                    <img src="{{ asset('assets/images/video-thumbnail.jpg') }}" alt="Video" width="100" height="75">
                                                                </a>
                                                            @else
                                                                <a href="{{ $path }}" class="glightbox" data-gallery="row-{{ $key }}{{ $i }}">
                                                                    <img src="{{ $path }}" alt="Ad Image" width="100">
                                                                </a>
                                                            @endif
                                                        </td>
                                                    @endforeach

                                                     {{-- <td class="text-center">
                                                        {{ $ad->cta_url ?? '-' }}
                                                     </td> --}}

                                                    <td class="text-center">
                                                        @can('edit_ads')
                                                            <div class="atbd-switch-wrap">
                                                                <div
                                                                    class="custom-control custom-switch switch-secondary switch-sm ">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="switch-s1_{{ $key }}"
                                                                        onchange="update_status(this)"
                                                                        value="{{ $ad->id }}" <?php if ($ad->status == 1) {
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
                                                            @can('edit_ads')
                                                                <a href="{{ route('ads.edit', $ad->id) }}" title="Edit Ad"><span data-feather="edit"></span></a>
                                                            @endcan
                                                            
                                                            @can('delete_ads')
                                                                <form id="delete-form-{{ $ad->id }}" action="{{ route('ads.destroy', $ad->id) }}" method="POST" style="display:none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>

                                                                <a href="javascript:void(0)"
                                                                    onclick="confirmDelete({{ $ad->id }})"
                                                                    title="Delete Ad">
                                                                    <span data-feather="trash-2"></span>
                                                                </a>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
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
                                            @endforelse
                                        @endcan
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    @can('view_ads')
                                        {{ $ads->appends(request()->input())->links('pagination::bootstrap-5') }}
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
    <link rel="stylesheet" href="{{ asset('assets/css/glightbox.min.css') }}">

@endsection

@section('script')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/glightbox.min.js') }}"></script>

    <script type="text/javascript">
        
      
        let  glightbox  = GLightbox({
            selector: '.glightbox',
            autoplayVideos: false,
        });

        glightbox.on('open', () => {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });


        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('ads.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Ad status updated successfully');
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
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
