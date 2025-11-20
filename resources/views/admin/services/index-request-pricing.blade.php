@extends('layouts.admin_default', ['title' => 'Request Submission Pricing'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Request Submission Pricing</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <div class="action-btn d-flex">
                            <a href="{{ route('request-pricing.create') }}" class="btn btn-sm btn-primary btn-add">
                                <i class="la la-plus"></i> Add New Pricing</a>
                            
                            <a href="{{ route('services.index') }}" class="btn btn-sm btn-secondary ml-2">← Back</a>
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

                            <form method="GET" action="{{ route('request-pricing.index') }}" autocomplete="off">
                                <div class="row mb-2">

                                    <div class="col-md-3 input-group mb-1">
                                        <select name="litigation_type" id="litigation_type" class="form-control">
                                            <option value="">Litigation Type</option>
                                            <option value="local" {{ request()->litigation_type == 'local' ? 'selected' : '' }}>
                                                Local
                                            </option>
                                            <option value="federal" {{ request()->litigation_type == 'federal' ? 'selected' : '' }}>
                                                Federal
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group mb-1">
                                        <select name="litigation_place" id="litigation_place" class="form-control">
                                            <option value="">Litigation Place</option>
                                            <option value="court" {{ request()->litigation_place == 'court' ? 'selected' : '' }}>
                                                Court
                                            </option>
                                            <option value="public_prosecution" {{ request()->litigation_place == 'public_prosecution' ? 'selected' : '' }}>
                                                Public Prosecution
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="case_type"  class="form-control select2 ih-small ip-gray radius-xs b-light px-15" id="case_type"  data-placeholder="Case Type" >
                                            <option value="">Case Type</option>
                                            
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="request_type" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" id="request_type"  data-placeholder="Request Type" >
                                            <option value="">Request Type</option>
                                            
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1 mt-1">
                                        <select name="request_title" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" id="request_title"  data-placeholder="Request Title" >
                                            <option value="">Request Title</option>
                                           
                                        </select>
                                    </div>

                                
                                    <div class="col-md-3 input-group mb-1 mt-1">
                                        <select name="status"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15">
                                            <option value="">Select Status</option>
                                            <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>

        
                                    <div class="col-md-2 mb-1 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('request-pricing.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">#</th>
                                            <th class="text-center">Litigation Type</th>
                                            <th class="text-center">Litigation Place</th>
                                            <th class="text-start">Case Type</th>
                                            <th class="text-start">Request Type</th>
                                            <th class="text-start">Request Title</th>
                                            <th class="text-center">Admin Amount (AED)</th>
                                            <th class="text-center">Govt Fee (AED)</th>
                                            <th class="text-center">VAT (5%)</th>
                                            <th class="text-center">Total Amount (AED)</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_service')
                                            @if ($requestPricing->isNotEmpty())
                                                @foreach ($requestPricing as $key => $price)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($requestPricing->currentPage() - 1) * $requestPricing->perPage() }}
                                                        </td>
                                                        <td class="text-center"> 
                                                            {{ ucfirst($price->litigation_type) ?? '-' }}
                                                        </td>

                                                        <td class="text-center"> 
                                                            {{ ucwords(str_replace('_', ' ',$price->litigation_place)) ?? '-' }}
                                                        </td>
                                                        <td class="text-start"> 
                                                            {{ $price->caseType?->getTranslation('title', 'en')  ?? '-' }}
                                                        </td>
                                                        <td class="text-start">
                                                            {{ $price->requestType?->getTranslation('title', 'en')  ?? '-' }}
                                                        </td>
                                                        <td class="text-start">
                                                            {{ $price->requestTitle?->getTranslation('title', 'en')  ?? '-' }}
                                                        </td>
                                                        
                                                        <td class="text-center">
                                                            {{ $price->admin_fee ?? '0' }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $price->govt_fee ?? '0' }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $price->vat ?? '0' }}
                                                        </td>
                                                        
                                                        <td class="text-center"> 
                                                            {{ $price->total_amount ?? '0' }}
                                                        </td>
                                                        <td class="text-center">
                                                            @can('edit_service')
                                                                <div class="atbd-switch-wrap">
                                                                    <div class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $price->id }}" <?php if ($price->status == 1) {
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
                                                                @can('edit_service')
                                                                    <a href="{{ route('request-pricing.edit', ['id' => base64_encode($price->id)]) }}" title="Edit Request Submission Pricing">
                                                                        <span data-feather="edit"></span>
                                                                    </a>

                                                                    <form id="delete-form-{{ $price->id }}"
                                                                        action="{{ route('request-pricing.destroy', $price->id) }}"
                                                                        method="POST" style="display:none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>

                                                                    <a href="javascript:void(0)"
                                                                        onclick="confirmDelete({{ $price->id }})"
                                                                        title="Delete Request Submission Pricing">
                                                                        <span data-feather="trash-2"></span>
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
                                    @can('view_service')
                                        {{ $requestPricing->appends(request()->input())->links('pagination::bootstrap-5') }}
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
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('request-pricing.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Request submission pricing status updated successfully');
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

        function loadCaseTypesAndRequestTypes(callback = null) {
            let litigationType = $('#litigation_type').val();
            let litigationPlace = $('#litigation_place').val();

            if (litigationType && litigationPlace) {
                $.get("{{ route('filter-case-types') }}", { litigation_type: litigationType, litigation_place: litigationPlace }, function (data) {
                    $('#case_type').empty().append('<option value="">Select</option>');
                    $.each(data, function (key, value) {
                        $('#case_type').append('<option value="' + value.id + '">' + value.title + '</option>');
                    });

                    let oldCaseType = "{{ request()->case_type }}";
                    if (oldCaseType) {
                        $('#case_type').val(oldCaseType);
                    }
                });

                $.get("{{ route('filter-request-types') }}", { litigation_type: litigationType, litigation_place: litigationPlace }, function (data) {
                    $('#request_type').empty().append('<option value="">Select</option>');
                    $.each(data, function (key, value) {
                        $('#request_type').append('<option value="' + value.id + '">' + value.title + '</option>');
                    });

                    let oldRequestType = "{{request()->request_type }}";
                    if (oldRequestType) {
                        $('#request_type').val(oldRequestType).trigger('change');
                    }

                    if (callback) callback();
                });
            }
        }

        function loadRequestTitles(requestTypeId, callback = null) {
            if (requestTypeId) {
                $.get("{{ route('filter-request-titles') }}", { request_type_id: requestTypeId }, function (data) {
                    $('#request_title').empty().append('<option value="">Select</option>');
                    $.each(data, function (key, value) {
                        $('#request_title').append('<option value="' + value.id + '">' + value.title + '</option>');
                    });

                    let oldRequestTitle = "{{ request()->request_title }}";
                    if (oldRequestTitle) {
                        $('#request_title').val(oldRequestTitle);
                    }

                    if (callback) callback();
                });
            }
        }

        $('#litigation_type, #litigation_place').change(function () {
            loadCaseTypesAndRequestTypes();
        });

        $('#request_type').change(function () {
            loadRequestTitles($(this).val());
        });

        @if(request()->litigation_type && request()->litigation_place)
            loadCaseTypesAndRequestTypes(function () {
                let oldRequestType = "{{ request()->request_type }}";
                if (oldRequestType) {
                    loadRequestTitles(oldRequestType);
                }
            });
        @endif
    </script>
@endsection
