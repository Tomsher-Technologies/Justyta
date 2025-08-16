@extends('layouts.admin_default', ['title' => 'Request Types & Request Titles'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Request Types & Request Titles</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        @can('add_dropdown_option')
                            <div class="action-btn">
                                <button class="btn btn-primary btn-sm btn-add" id="addRequestTypeBtn">
                                    <i class="la la-plus"></i>Add Request Type
                                </button>
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
                            <form class="row mb-2" id="sort_brands" action="" method="GET" autocomplete="off">
                                <div class="col-md-5 input-group  mb-1">
                                    <input type="text" class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        id="search" name="search" value="{{ request()->search }}"
                                        placeholder="Type Request Type name..">
                                </div>

                                <div class="col-md-3 input-group  mb-1">
                                    <select name="litigation_place"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                        <option value="">--Select Litigation Place--</option>
                                        <option value="court" {{ request()->litigation_place == 'court' ? 'selected' : '' }}>
                                            Court
                                        </option>
                                        <option value="public_prosecution" {{ request()->litigation_place == 'public_prosecution' ? 'selected' : '' }}>
                                            Public Prosecution
                                        </option>
                                       
                                    </select>
                                </div>

                                <div class="col-md-3 input-group  mb-1">
                                    <select name="litigation_type"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                        <option value="">--Select Litigation Type--</option>
                                        <option value="local" {{ request()->litigation_type == 'local' ? 'selected' : '' }}>
                                            Local
                                        </option>
                                        <option value="federal" {{ request()->litigation_type == 'federal' ? 'selected' : '' }}>
                                            Federal
                                        </option>
                                       
                                    </select>
                                </div>

                                <div class="col-md-3 input-group  mb-1 mt-1">
                                    <select name="status" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                        <option value="">--Select Status--</option>
                                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end mt-1">
                                    <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                    <a href="{{ route('request-types.index') }}"
                                        class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">Sl No.</th>
                                            <th>Name</th>
                                            <th class="text-center">Litigation Place</th>
                                            <th class="text-center">Litigation Type</th>
                                            <th class="text-center">Sort Order</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Request Titles</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="RequestTypesTable">
                                        @can('view_dropdown_option')
                                            @if ($request_types->isNotEmpty())
                                                @foreach ($request_types as $key => $type)
                                                    <tr data-id="{{ $type->id }}">
                                                        <td class="text-center ">
                                                            {{ $key + 1 + ($request_types->currentPage() - 1) * $request_types->perPage() }}
                                                        </td>
                                                        <td class="">{{ $type->title }}</td>
                                                        <td class="text-center">{{ ucwords(str_replace('_', ' ',$type->litigation_place)) ?? '-' }}</td>
                                                        <td class="text-center">{{ ucfirst($type->litigation_type) ?? '-' }}</td>
                                                        <td class="text-center ">{{ $type->sort_order }}</td>
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

                                                        <td class="text-center table-actions">
                                                            <a href="{{ route('request-titles.index', ['id' => base64_encode($type->id)]) }}" class="btn btn-primary btn-xxs">
                                                                 View ({{ $type->titles->count() }})
                                                            </a>
                                                        </td>

                                                        <td class="text-center">
                                                            @can('edit_dropdown_option')
                                                                <div class="table-actions">
                                                                    <a class="edit-btn" data-id="{{ $type->id }}" title="Edit Request Type" style="cursor: pointer;">
                                                                        <span data-feather="edit"></span></a>
                                                                </div>
                                                            @endcan
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
                                        {{ $request_types->appends(request()->input())->links('pagination::bootstrap-5') }}
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
    <div class="modal fade" id="request_typeModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true" autocomplete="off">
        <div class="modal-dialog modal-lg">
            <form id="RequestTypeForm">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id" id="typeId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Request Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div id="formErrors" class="alert alert-danger d-none col-sm-12"></div>
                       
                        <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Litigation Place <span class="text-danger">*</span></label>
                            <select name="litigation_place" id="litigation_place" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                <option value="">Select</option>
                                <option value="court">Court</option>
                                <option value="public_prosecution">Public Prosecution</option>
                            </select>
                        </div>

                        <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Litigation Type <span class="text-danger">*</span></label>
                            <select name="litigation_type" id="litigation_type" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                <option value="">Select</option>
                                <option value="local">Local</option>
                                <option value="federal">Federal</option>
                            </select>
                        </div>

                        @foreach ($languages as $lang)
                            <div class="form-group mb-2  col-sm-6">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Name ({{ $lang->name }})
                                    @if ($lang->code == 'en')
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="text"
                                    name="translations[{{ $lang->code }}][title]"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                    value="">
                            </div>
                        @endforeach

                        <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Sort Order</label>
                            <input type="number" name="sort_order" id="typeSort" value="0"
                                class="form-control ih-small ip-gray radius-xs b-light px-15">
                        </div>
                        
                        <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Status</label>
                            <select name="status" id="typeStatus"
                                class="form-control ih-small ip-gray radius-xs b-light px-15">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
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
            let modal = new bootstrap.Modal(document.getElementById('request_typeModal'));
            const storeUrl = "{{ url('admin/request-types') }}";
            const updateUrl = "{{ url('admin/request-types') }}/"; // Append ID dynamically
            const editUrl = "{{ url('admin/request-types/edit') }}/";

            $('#addRequestTypeBtn').click(function() {
                $('.modal-title').text('Add Request Type');
                $('#RequestTypeForm')[0].reset();
                $('#typeId').val('');
                $('input[name="_method"]').val('POST');
                $('#formErrors').addClass('d-none');
                modal.show();
            });

            $('.edit-btn').click(function() {
                $('.modal-title').text('Edit Request Type');
                $('#formErrors').addClass('d-none');
                const id = $(this).data('id');
                editRequestType(id);
            });

            function editRequestType(id) {
                $.get(editUrl + id, function (data) {
                    $('#typeId').val(data.id);
                    $('#litigation_place').val(data.litigation_place);
                    $('#litigation_type').val(data.litigation_type);
                    $('#typeStatus').val(data.status);
                    $('#typeSort').val(data.sort_order);

                    $('input[name^="translations"]').val('');

                    if (data.translations) {
                        for (const [lang, name] of Object.entries(data.translations)) {
                            $(`input[name="translations[${lang}][title]"]`).val(name);
                        }
                    }

                    $('#request_typeModal').modal('show');
                });
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#RequestTypeForm').submit(function(e) {
                e.preventDefault();
                let method = $('#typeId').val() ? 'PUT' : 'POST';
                let id = $('#typeId').val();
                let url = method === 'POST' ? storeUrl : updateUrl + id;
                let formData = $('#RequestTypeForm').serialize();

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
            $.post('{{ route('request-types.status') }}', {
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
                    }, 3000);
                }
            });
        }
    </script>
@endsection
