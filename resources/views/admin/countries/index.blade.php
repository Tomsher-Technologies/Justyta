@extends('layouts.admin_default')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Countries</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        @can('add_dropdown_option')
                            <div class="action-btn">
                                <button class="btn btn-primary btn-sm btn-add" id="addcountryBtn">
                                    <i class="la la-plus"></i>Add New Country
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
                                <div class="col-md-4 input-group  mb-1">
                                    <input type="text" class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        id="search" name="search" value="{{ request()->search }}"
                                        placeholder="Type country name..">
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
                                    <a href="{{ route('countries.index') }}"
                                        class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">Sl No.</th>
                                            <th>Country Name</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="countriesTable">
                                        @can('view_dropdown_option')
                                            @if ($countries->isNotEmpty())
                                                @foreach ($countries as $key => $type)
                                                    <tr data-id="{{ $type->id }}">
                                                        <td class="text-center ">
                                                            {{ $key + 1 + ($countries->currentPage() - 1) * $countries->perPage() }}
                                                        </td>
                                                        <td class="">{{ $type->name }}</td>
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
                                                                    <a class="edit-btn pointer" data-id="{{ $type->id }}" title="Edit Country" style="cursor: pointer;">
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
                                        {{ $countries->appends(request()->input())->links('pagination::bootstrap-5') }}
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
    <div class="modal fade" id="countryModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true" autocomplete="off">
        <div class="modal-dialog modal-lg">
            <form id="countryForm">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id" id="typeId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Country</h5>
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
    <script src="{{ asset('assets/js/bootstrap/popper.js') }}"></script>
    <script>
        $(document).ready(function() {
            let modal = new bootstrap.Modal(document.getElementById('countryModal'));
            const storeUrl = "{{ url('admin/countries') }}";
            const updateUrl = "{{ url('admin/countries') }}/"; // Append ID dynamically
            const editUrl = "{{ url('admin/countries/edit') }}/";

            $('#addcountryBtn').click(function() {
                $('.modal-title').text('Add Country');
                $('#countryForm')[0].reset();
                $('#typeId').val('');
                $('input[name="_method"]').val('POST');
                $('#formErrors').addClass('d-none');
                modal.show();
            });

            $('.edit-btn').click(function() {
                $('.modal-title').text('Edit Country');
                $('#formErrors').addClass('d-none');
                const id = $(this).data('id');
                editcountry(id);
            });

            function editcountry(id) {
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

                    $('#countryModal').modal('show');
                });
            }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#countryForm').submit(function(e) {
                e.preventDefault();
                let method = $('#typeId').val() ? 'PUT' : 'POST';
                let id = $('#typeId').val();
                let url = method === 'POST' ? storeUrl : updateUrl + id;
                let formData = $('#countryForm').serialize();

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
            $.post('{{ route('countries.status') }}', {
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
