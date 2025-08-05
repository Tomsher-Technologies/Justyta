@extends('layouts.admin_default')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Contract Types</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        @can('add_dropdown_option')
                            <div class="action-btn">
                                <button class="btn btn-primary btn-sm btn-add" id="addContractTypeBtn">
                                    <i class="la la-plus"></i>Add Contract Type
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
                                <div class="col-md-3 input-group  mb-1">
                                    <input type="text" class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        id="search" name="search" value="{{ request()->search }}"
                                        placeholder="Type contract type name..">
                                </div>

                                <div class="col-md-3 input-group  mb-1">
                                    <select name="ptype_id"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                        <option value="">--Select Contract Type--</option>
                                        @foreach ($allParentTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ request()->ptype_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 input-group  mb-1">
                                    <select name="status" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                        <option value="">--Select Status--</option>
                                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end">
                                    <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                    <a href="{{ route('contract-types.index') }}"
                                        class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">Sl No.</th>
                                            <th>Name</th>
                                            <th>Parent</th>
                                            <th class="text-center">Sort Order</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contractTypesTable">
                                        @can('view_dropdown_option')
                                            @if ($contractTypes->isNotEmpty())
                                                @foreach ($contractTypes as $key => $type)
                                                    <tr data-id="{{ $type->id }}">
                                                        <td class="text-center text-primary">
                                                            {{ $key + 1 + ($contractTypes->currentPage() - 1) * $contractTypes->perPage() }}
                                                        </td>
                                                        <td class="text-primary">{{ $type->name }}</td>
                                                        <td class="text-primary">{{ $type->parent?->name ?? '-' }}</td>
                                                        <td class="text-center text-primary">{{ $type->sort_order }}</td>
                                                        <td class="text-center text-primary">
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
                                                                    <a class="edit-btn" data-id="{{ $type->id }}"  title="Edit Contract Type" style="cursor: pointer;">
                                                                        <span data-feather="edit"></span></a>
                                                                </div>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                    @foreach ($type->children as $j => $sub)
                                                        <tr data-id="{{ $sub->id }}">
                                                            <td></td>
                                                            <td class="text-secondary">— {{ $sub->name }}</td>
                                                            <td class="text-secondary">{{ $type->name }}</td>
                                                            <td class="text-center text-secondary">{{ $sub->sort_order }}</td>
                                                            <td class="text-center text-secondary">
                                                                @can('edit_dropdown_option')
                                                                    <div class="atbd-switch-wrap">
                                                                        <div
                                                                            class="custom-control custom-switch switch-secondary switch-sm ">
                                                                            <input type="checkbox" class="custom-control-input"
                                                                                id="switch-s1_s{{ $sub->id }}"
                                                                                onchange="update_status(this)"
                                                                                value="{{ $sub->id }}" <?php if ($sub->status == 1) {
                                                                                    echo 'checked';
                                                                                } ?>>
                                                                            <label class="custom-control-label"
                                                                                for="switch-s1_s{{ $sub->id }}"></label>
                                                                        </div>
                                                                    </div>
                                                                @endcan

                                                            </td>

                                                            <td class="text-center">

                                                                @can('edit_dropdown_option')
                                                                    <div class="table-actions">
                                                                        <a class="edit-btn" data-id="{{ $sub->id }}"
                                                                            data-name="{{ $sub->name }}"
                                                                            data-parent="{{ $sub->parent_id }}"
                                                                            data-sort="{{ $sub->sort_order }}"
                                                                            data-status="{{ $sub->status }}">
                                                                            <span data-feather="edit"></span></a>
                                                                    </div>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
                                        {{ $contractTypes->appends(request()->input())->links('pagination::bootstrap-5') }}
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
    <div class="modal fade" id="contractTypeModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true" autocomplete="off">
        <div class="modal-dialog modal-lg">
            <form id="contractTypeForm">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id" id="typeId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Contract Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div id="formErrors" class="alert alert-danger d-none col-sm-12"></div>
                        {{-- <div class="form-group mb-2">
                            <label class="col-form-label color-dark fw-500 align-center">Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control ih-small ip-gray radius-xs b-light px-15" id="typeName">
                        </div> --}}

                        <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Parent Type</label>
                            <select name="parent_id" id="typeParent"
                                class="form-control ih-small ip-gray radius-xs b-light px-15">
                                <option value="">-- None --</option>
                                @foreach ($contractTypes->whereNull('parent_id') as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Sort Order</label>
                            <input type="number" name="sort_order" id="typeSort" value="0"
                                class="form-control ih-small ip-gray radius-xs b-light px-15">
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
                                    name="translations[{{ $lang->code }}][name]"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                    value="">
                            </div>
                        @endforeach

                        
                        
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
    <script src="{{ asset('assets/js/bootstrap/popper.js') }}" ></script>
    <script>
        $(document).ready(function() {
            let modal = new bootstrap.Modal(document.getElementById('contractTypeModal'));
            const storeUrl = "{{ url('admin/contract-types') }}";
            const updateUrl = "{{ url('admin/contract-types') }}/"; // Append ID dynamically
            const editUrl = "{{ url('admin/contract-types/edit') }}/";

            $('#addContractTypeBtn').click(function() {
                $('.modal-title').text('Add Contract Type');
                $('#contractTypeForm')[0].reset();
                $('#typeId').val('');
                $('input[name="_method"]').val('POST');
                $('#formErrors').addClass('d-none');
                modal.show();
            });

            $('.edit-btn').click(function() {
                $('.modal-title').text('Edit Contract Type');
                $('#formErrors').addClass('d-none');
                const id = $(this).data('id');
                editContractType(id);
            });

            function editContractType(id) {
                $.get(editUrl + id, function (data) {
                    $('#typeId').val(data.id);
                    $('#typeParent').val(data.parent_id);
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

                    $('#contractTypeModal').modal('show');
                });
            }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#contractTypeForm').submit(function(e) {
                e.preventDefault();
                let method = $('#typeId').val() ? 'PUT' : 'POST';
                let id = $('#typeId').val();
                let url = method === 'POST' ? storeUrl : updateUrl + id;
                let formData = $('#contractTypeForm').serialize();

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
            $.post('{{ route('contract-types.status') }}', {
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
