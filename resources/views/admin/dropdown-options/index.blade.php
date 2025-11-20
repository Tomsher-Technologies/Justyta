@extends('layouts.admin_default')

@section('content')
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-horizontal card-default card-md mb-4">
                    <div class="card-header">
                        <h4 class="fs-20">Manage Options for Dropdown: <strong>{{ $dropdown->name }}</strong></h4>
                    </div>
                    <div class="card-body py-md-20">
                        @can('add_dropdown_option')
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Add New Option</h4>
                                </div>
                                <div class="card-body">
                                    <div class="horizontal-form">
                                        <form method="POST" action="{{ route('dropdown-options.store', $dropdown->id) }}" autocomplete="off">
                                            @csrf
                                            <div class="row mb-3">

                                                @foreach ($languages as $lang)
                                                    <div class="col-md-3">
                                                        <label class="col-form-label color-dark fw-500 align-center">Name
                                                            ({{ $lang->name }})
                                                            @if ($lang->code == 'en')
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text" name="translations[{{ $lang->code }}][name]"
                                                            class="form-control ih-small ip-gray radius-xs b-light px-15 @error('translations.' . $lang->code . '.name') is-invalid @enderror"
                                                            value="{{ old('translations.' . $lang->code . '.name') }}">
                                                        @error('translations.' . $lang->code . '.name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endforeach

                                                <div class="col-md-2">
                                                    <label class="col-form-label color-dark fw-500 align-center">Status <span
                                                            class="text-danger">*</span></label>
                                                    <select name="status"
                                                        class="form-control ih-small ip-gray radius-xs b-light px-15 @error('status') is-invalid @enderror">
                                                        <option value="active"
                                                            {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive"
                                                            {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                                        </option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="col-form-label color-dark fw-500 align-center">Sort
                                                        Order</label>
                                                    <input type="number" name="sort_order"
                                                        class="form-control ih-small ip-gray radius-xs b-light px-15 @error('sort_order') is-invalid @enderror"
                                                        value="{{ old('sort_order', 1) }}">
                                                    @error('sort_order')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                <div class="col-md-2 align-self-end d-flex">
                                                    <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                                    <a href="{{ route('dropdowns.index') }}"
                                                        class="btn btn-secondary btn-sm ml-1">Cancel</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endcan


                        <div class="card">
                            <div class="card-header">
                                <h4>Existing Options</h4>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr>
                                            @foreach ($languages as $lang)
                                                <th>Name ({{ $lang->name }})</th>
                                            @endforeach
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Sort Order</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_dropdown_option')
                                            @forelse ($options as $option)
                                                <tr>

                                                    @foreach ($languages as $key => $lang)
                                                        <td>
                                                            {{ $option->translations->firstWhere('language_code', $lang->code)?->name ?? '-' }}
                                                        </td>
                                                    @endforeach

                                                    <td class="text-center">
                                                        @can('edit_dropdown_option')
                                                            <div class="atbd-switch-wrap">
                                                                <div
                                                                    class="custom-control custom-switch switch-secondary switch-sm ">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="switch-s1_{{ $option->id }}"
                                                                        onchange="update_status(this)" value="{{ $option->id }}"
                                                                        <?php if ($option->status === 'active') {
                                                                            echo 'checked';
                                                                        } ?>>
                                                                    <label class="custom-control-label"
                                                                        for="switch-s1_{{ $option->id }}"></label>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                    </td>
                                                    <td class="text-center">{{ $option->sort_order }}</td>
                                                    <td class="text-center">
                                                        <div class="table-actions">
                                                            @can('edit_dropdown_option')
                                                                <a href="javascript:void(0)" role="button" title="Edit Options"
                                                                    data-toggle="modal" data-target="#editOptionModal"
                                                                    data-id="{{ $option->id }}"
                                                                    data-status="{{ $option->status }}"
                                                                    data-sort_order="{{ $option->sort_order }}"
                                                                    @foreach ($languages as $lang) data-name-{{ $lang->code }}="{{ $option->translations->firstWhere('language_code', $lang->code)?->name }}" @endforeach  style="cursor: pointer;">
                                                                    <span data-feather="edit"></span>
                                                                </a>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ 3 + $languages->count() }}" class="text-center">No options
                                                        available.</td>
                                                </tr>
                                            @endforelse
                                        @endcan

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editOptionModal" tabindex="-1" role="dialog" aria-labelledby="editOptionModalLabel"
        aria-hidden="true" autocomplete="off">
        <div class="modal-dialog modal-lg">
            <form method="POST" id="editOptionForm">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Dropdown Option</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row g-3">
                        @foreach ($languages as $lang)
                            <div class="col-md-6">
                                <label class="col-form-label color-dark fw-500 align-center">Name
                                    ({{ $lang->name }})
                                    @if ($lang->code == 'en')
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="text" name="translations[{{ $lang->code }}][name]"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                    id="edit-name-{{ $lang->code }}" @if ($lang->code == 'en') required @endif>
                            </div>
                        @endforeach

                        <div class="col-md-6">
                            <label class="col-form-label color-dark fw-500 align-center">Status</label>
                            <select name="status" class="form-control ih-small ip-gray radius-xs b-light px-15"
                                id="edit-status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="col-form-label color-dark fw-500 align-center">Sort Order</label>
                            <input type="number" name="sort_order"
                                class="form-control ih-small ip-gray radius-xs b-light px-15" id="edit-sort-order">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
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
            $('#editOptionModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const status = button.data('status');
                const sortOrder = button.data('sort_order');

                const form = $('#editOptionForm');
                form.attr('action', `/admin/dropdown-options/${id}`);

                $('#edit-status').val(status);
                $('#edit-sort-order').val(sortOrder);

                @foreach ($languages as $lang)
                    const name_{{ $lang->code }} = button.data('name-{{ $lang->code }}');
                    $('#edit-name-{{ $lang->code }}').val(name_{{ $lang->code }});
                @endforeach
            });


        });

        function update_status(el) {
            if (el.checked) {
                var status = 'active';
            } else {
                var status = 'inactive';
            }
            $.post('{{ route('dropdown-options.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Status updated successfully');
                    // setTimeout(function() {
                    //     window.location.reload();
                    // }, 3000);

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
