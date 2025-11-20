@extends('layouts.admin_default', ['title' => 'Request Titles'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="d-flex">
                        <h4 class="text-capitalize breadcrumb-title">Request Titles &nbsp; </h4>
                        <h6 class="mt-1">({{ ucwords(str_replace('_', ' ', $request_type->litigation_place)) }} -> {{ ucwords(str_replace('_', ' ', $request_type->litigation_type)) }} -> {{ $request_type->getTranslation('title', 'en') }})</h6>
                    </div>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <button class="btn btn-primary btn-sm btn-add" id="addRequestTitleBtn">
                            <i class="la la-plus"></i>Add Request Title
                        </button>

                        <a href="{{ Session::has('request_types_last_url') ? Session::get('request_types_last_url') : route('request-types.index') }}" class="btn btn-sm btn-secondary ml-2">← Back</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTING -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table4  bg-white mb-30">
                            <form class="row mb-2" id="sort_brands" action="" method="GET" autocomplete="off">
                                <div class="col-md-5 input-group  mb-1">
                                    <input type="text" class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        id="search" name="search" value="{{ request()->search }}"
                                        placeholder="Type Request Title name..">
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
                                    <a href="{{ route('request-titles.index', ['id' => base64_encode($request_type->id)]) }}"
                                        class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">Sl No.</th>
                                            <th class="text-start">Title</th>
                                            {{-- <th class="text-center">Request Type</th> --}}
                                            <th class="text-center">Sort Order</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="RequestTitlesTable">
                                        @if ($titles->isNotEmpty())
                                            @foreach ($titles as $key => $title)
                                                <tr>
                                                    <td class="text-center">{{ $key + 1 + ($titles->currentPage() - 1) * $titles->perPage() }}</td>
                                                    <td class="text-start">{{ $title->title }}</td>
                                                    {{-- <td class="text-center">{{ $title->type->getTranslation('title', 'en') }}</td> --}}
                                                    <td class="text-center">{{ $title->sort_order }}</td>
                                                    <td class="text-center">
                                                        @can('edit_dropdown_option')
                                                            <div class="atbd-switch-wrap">
                                                                <div
                                                                    class="custom-control custom-switch switch-secondary switch-sm ">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="switch-s1_{{ $title->id }}"
                                                                        onchange="update_status(this)"
                                                                        value="{{ $title->id }}" <?php if ($title->status == 1) {
                                                                            echo 'checked';
                                                                        } ?>>
                                                                    <label class="custom-control-label"
                                                                        for="switch-s1_{{ $title->id }}"></label>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="table-actions">
                                                            <a class="edit-btn" data-id="{{ $title->id }}" style="cursor: pointer;">
                                                                <span data-feather="edit"></span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">No Data Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                              
                                <div class="aiz-pagination mt-4">
                                    @can('view_dropdown_option')
                                        {{ $titles->appends(request()->input())->links('pagination::bootstrap-5') }}
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="request_titleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="RequestTitleForm">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id" id="titleId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Request Title ({{ ucwords(str_replace('_', ' ', $request_type->litigation_place)) }} -> {{ $request_type->litigation_type }} -> {{ $request_type->getTranslation('title', 'en') }})</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <div class="modal-body row">
                        <div id="formErrors" class="alert alert-danger d-none col-sm-12"></div>
                        <input type="hidden" name="request_type_id" id="request_type_id" value="{{ $request_type->id }}" class="form-control">
                        @foreach ($languages as $lang)
                            <div class="form-group mb-2 col-sm-6">
                                <label class="col-form-label color-dark fw-500 align-center">Title ({{ $lang->name }})</label>
                                <input type="text" name="translations[{{ $lang->code }}][title]" class="form-control ih-small ip-gray radius-xs b-light px-15">
                            </div>
                        @endforeach

                        <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Sort Order</label>
                            <input type="number" name="sort_order" id="titleSort" value="0" class="form-control ih-small ip-gray radius-xs b-light px-15">
                        </div>

                        <div class="form-group mb-2 col-sm-6">
                            <label class="col-form-label color-dark fw-500 align-center">Status</label>
                            <select name="status" id="titleStatus" class="form-control ih-small ip-gray radius-xs b-light px-15">
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
<script>
$(document).ready(function(){
    let modal = new bootstrap.Modal(document.getElementById('request_titleModal'));
    const storeUrl = "{{ route('request-titles.store') }}";
    const updateUrl = "{{ route('request-titles.update', ['id' => ':id']) }}";
    const editUrl = "{{ route('request-titles.edit', ['id' => ':id']) }}";

    $('#addRequestTitleBtn').click(function(){
        $('.modal-title').text('Add Request Title ({{ ucwords(str_replace('_', ' ', $request_type->litigation_place)) }} -> {{ ucwords(str_replace('_', ' ', $request_type->litigation_type)) }} -> {{ $request_type->getTranslation('title', 'en') }})');
        $('#RequestTitleForm')[0].reset();
        $('#titleId').val('');
        $('input[name="_method"]').val('POST');
        $('#formErrors').addClass('d-none');
        modal.show();
    });

    $('.edit-btn').click(function(){
        $('.modal-title').text('Edit Request Title ({{ ucwords(str_replace('_', ' ', $request_type->litigation_place)) }} -> {{ ucwords(str_replace('_', ' ', $request_type->litigation_type)) }} -> {{ $request_type->getTranslation('title', 'en') }})');
        $('#formErrors').addClass('d-none');
        const id = $(this).data('id');

        let url = editUrl.replace(':id', id);
        $.get(url, function(data){
            $('#titleId').val(data.id);
            $('#request_type_id').val(data.request_type_id);
            $('#titleStatus').val(data.status);
            $('#titleSort').val(data.sort_order);

            $('input[name^="translations"]').val('');
            if(data.translations){
                for(const [lang, name] of Object.entries(data.translations)){
                    $(`input[name="translations[${lang}][title]"]`).val(name);
                }
            }
            modal.show();
        });
    });

    $('#RequestTitleForm').submit(function(e){
        e.preventDefault();
        let method = $('#titleId').val() ? 'PUT' : 'POST';
        let id = $('#titleId').val();
        let url = method === 'POST' ? storeUrl : updateUrl.replace(':id', id);
        let formData = $(this).serialize();

        $.ajax({
            url: url, method: method, data: formData,
            success: function(){ location.reload(); },
            error: function(xhr){
                let errors = xhr.responseJSON.errors;
                let errorList = Object.values(errors).map(e => `<li>${e[0]}</li>`).join('');
                $('#formErrors').removeClass('d-none').html(`<ul>${errorList}</ul>`);
            }
        });
    });
});

function update_status(el){
    let status = el.checked ? 1 : 0;
    $.post('{{ route('request-titles.status') }}', {
        _token: '{{ csrf_token() }}', id: el.value, status: status
    }, function(data){
        if(data == 1){ toastr.success('Status updated'); }
        else { toastr.error('Something went wrong'); }
    });
}
</script>
@endsection
