@extends('layouts.admin_default')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Emirates</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        {{-- @can('add_dropdown_option')
                            <div class="action-btn">
                                <button class="btn btn-primary btn-sm btn-add" id="addemirateBtn">
                                    <i class="la la-plus"></i>Add New Emirate
                                </button>
                            </div>
                        @endcan --}}

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table4  bg-white mb-30">
                            <form class="row mb-2" id="sort_brands" action="" method="GET" autocomplete="off">
                                <div class="col-md-5 input-group  mb-1">
                                    
                                    @php
                                        $module = request()->module ?? 'others';
                                        $local_federal_need = false;
                                        $local_federal = ['court-case-submission','criminal-complaint','expert-report','memo-writing','online-live-consultancy','request-submission'];

                                        if (in_array($module,$local_federal)) {
                                            $local_federal_need = true;
                                        }
                                    @endphp
                                    <select name="module" onchange="this.form.submit()" class="select2 form-control ih-small ip-gray radius-xs b-light px-15">
                                        <option value="">-- Select Module --</option>
                                        <option {{ $module === "annual-retainer-agreement" ? 'selected' : '' }} value="annual-retainer-agreement">Companies Retainership Annual Agreement</option>
                                        <option {{ $module === "company-setup" ? 'selected' : '' }} value="company-setup">Company Setup</option>
                                        <option {{ $module === "contract-drafting" ? 'selected' : '' }} value="contract-drafting">Contract Drafting</option>
                                        <option {{ $module === "court-case-submission" ? 'selected' : '' }} value="court-case-submission">Court Case Submission</option>
                                        <option {{ $module === "criminal-complaint" ? 'selected' : '' }} value="criminal-complaint">Criminal Complaint</option>
                                        <option {{ $module === "debts-collection" ? 'selected' : '' }} value="debts-collection">Debts Collection</option>
                                        <option {{ $module === "expert-report" ? 'selected' : '' }} value="expert-report">Expert Report</option>
                                        <option {{ $module === "last-will-and-testament" ? 'selected' : '' }} value="last-will-and-testament">Last Will & Testament</option>
                                        <option {{ $module === "memo-writing" ? 'selected' : '' }} value="memo-writing">Memo Writing</option>
                                        <option {{ $module === "online-live-consultancy" ? 'selected' : '' }} value="online-live-consultancy">Online Live Consultancy</option>
                                        <option {{ $module === "others" ? 'selected' : '' }} value="others">Others</option>
                                        <option {{ $module === "power-of-attorney" ? 'selected' : '' }} value="power-of-attorney">Power Of Attorney</option>
                                        <option {{ $module === "request-submission" ? 'selected' : '' }} value="request-submission">Request Submission</option>
                                        <option {{ $module === "training" ? 'selected' : '' }} value="training">Training</option>
                                    </select>
                                </div>

                                {{-- <div class="col-md-3 input-group  mb-1">
                                    <select name="status" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                        <option value="">--Select Status--</option>
                                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div> --}}

                                <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end">
                                    <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                    <a href="{{ route('emirates.index') }}"
                                        class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">Sl No.</th>
                                            <th>Emirate Name</th>
                                            @if ($local_federal_need)
                                                <th class="text-center">Local</th>
                                                <th class="text-center">Federal</th>    
                                            @endif
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emiratesTable">
                                        @can('view_dropdown_option')
                                            @if ($emirates->isNotEmpty())
                                                @foreach ($emirates as $key => $type)
                                                    <tr data-id="{{ $type->id }}">
                                                        <td class="text-center ">
                                                            {{ $key + 1 + ($emirates->currentPage() - 1) * $emirates->perPage() }}
                                                        </td>
                                                        <td class="">{{ $type->emirate->name }}</td>
                                                        @if ($local_federal_need)
                                                            <td class="text-center ">
                                                                @can('edit_dropdown_option')
                                                                    <div class="atbd-switch-wrap">
                                                                        <div class="custom-control custom-switch switch-secondary switch-sm ">
                                                                            <input type="checkbox" class="custom-control-input"
                                                                                id="switch_local_{{ $type->id }}"  onchange="update_local_status(this)"
                                                                                value="{{ $type->id }}" <?php if ($type->is_local == 1) {
                                                                                    echo 'checked';
                                                                                } ?>>
                                                                            <label class="custom-control-label" for="switch_local_{{ $type->id }}"></label>
                                                                        </div>
                                                                    </div>
                                                                @endcan
                                                            </td>

                                                            <td class="text-center ">
                                                                @can('edit_dropdown_option')
                                                                    <div class="atbd-switch-wrap">
                                                                        <div class="custom-control custom-switch switch-secondary switch-sm ">
                                                                            <input type="checkbox" class="custom-control-input"
                                                                                id="switch_federal_{{ $type->id }}"  onchange="update_federal_status(this)"
                                                                                value="{{ $type->id }}" <?php if ($type->is_federal == 1) {
                                                                                    echo 'checked';
                                                                                } ?>>
                                                                            <label class="custom-control-label" for="switch_federal_{{ $type->id }}"></label>
                                                                        </div>
                                                                    </div>
                                                                @endcan
                                                            </td>
                                                        @endif

                                                        <td class="text-center ">
                                                            @can('edit_dropdown_option')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $type->id }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $type->id }}" <?php if ($type->status == 1) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="custom-control-label"
                                                                            for="switch-s1_{{ $type->id }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endcan
                                                        </td>

                                                        <td class="text-center">
                                                            @can('edit_dropdown_option')
                                                                <div class="table-actions">
                                                                    <a class="edit-btn" data-id="{{ $type->emirate->id }}" title="Edit Emirate"  style="cursor: pointer;">
                                                                        <span data-feather="edit"></span></a>
                                                                </div>
                                                            @endcan
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
                                    @can('view_dropdown_option')
                                        {{ $emirates->appends(request()->input())->links('pagination::bootstrap-5') }}
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="emirateModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true" autocomplete="off">
        <div class="modal-dialog modal-lg">
            <form id="emirateForm">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id" id="typeId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Emirate</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div id="formErrors" class="alert alert-danger d-none col-sm-12"></div>
                        @foreach ($languages as $lang)
                            <div class="form-group mb-2  col-sm-6">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Name ({{ $lang->name }})
                                    @if ($lang->code == 'en')
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="text"
                                    name="translations[{{ $lang->code }}][name]"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                    value="">
                            </div>
                        @endforeach

                        
                        
                        {{-- <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Status</label>
                            <select name="status" id="typeStatus"
                                class="form-control ih-small ip-gray radius-xs b-light px-15">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/bootstrap/popper.js') }}"></script>
    <script>
        $(document).ready(function() {
            let modal = new bootstrap.Modal(document.getElementById('emirateModal'));
            const storeUrl = "{{ url('admin/emirates') }}";
            const updateUrl = "{{ url('admin/emirates') }}/"; // Append ID dynamically
            const editUrl = "{{ url('admin/emirates/edit') }}/";

            $('#addemirateBtn').click(function() {
                $('.modal-title').text('Add Emirate');
                $('#emirateForm')[0].reset();
                $('#typeId').val('');
                $('input[name="_method"]').val('POST');
                $('#formErrors').addClass('d-none');
                modal.show();
            });

            $('.edit-btn').click(function() {
                $('.modal-title').text('Edit Emirate');
                $('#formErrors').addClass('d-none');
                const id = $(this).data('id');
                editemirate(id);
            });

            function editemirate(id) {
                $.get(editUrl + id, function (data) {
                    $('#typeId').val(data.id);
                    $('#typeParent').val(data.emirate_id);
                    $('#typeStatus').val(data.status);
                    $('#typeSort').val(data.sort_order);

                    // Clear old translation values
                    $('input[name^="translations"]').val('');

                    // Fill translations
                    if (data.translations) {
                        for (const [lang, name] of Object.entries(data.translations)) {
                            $(`input[name="translations[${lang}][name]"]`).val(name);
                        }
                    }

                    $('#emirateModal').modal('show');
                });
            }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#emirateForm').submit(function(e) {
                e.preventDefault();
                let method = $('#typeId').val() ? 'PUT' : 'POST';
                let id = $('#typeId').val();
                let url = method === 'POST' ? storeUrl : updateUrl + id;
                let formData = $('#emirateForm').serialize();

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorList = Object.values(errors).map(e => `<li>${e[0]}</li>`).join(
                            '');
                        $('#formErrors').removeClass('d-none').html(`<ul>${errorList}</ul>`);
                    }
                });
            });
        });

        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('emirates.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Status updated successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);

                } else {
                    toastr.error('Something went wrong');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            });
        }

        function update_federal_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('emirates.federal-status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Federal status updated successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);

                } else {
                    toastr.error('Something went wrong');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            });
        }

        function update_local_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('emirates.local-status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Local status updated successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);

                } else {
                    toastr.error('Something went wrong');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            });
        }
    </script>
@endsection
