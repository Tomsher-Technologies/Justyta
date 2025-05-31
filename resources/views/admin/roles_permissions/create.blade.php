@extends('layouts.admin_default', ['title' => 'Create Role'])

@section('content')
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-sm-10 offset-sm-1">
                <div class="card card-horizontal card-default card-md mb-4">
                    <div class="card-header">
                        <h6>Create New Role</h6>
                    </div>
                    <div class="card-body py-md-20">
                        <div class="horizontal-form">
                            <form action="{{ route('roles.store') }}" method="POST" autocomplete="off">
                                @csrf
                                <div class="form-group row mb-25">
                                    <div class="col-sm-12 d-flex aling-items-center">
                                        <label for="inputName" class=" col-form-label color-dark fw-500 align-center">Role
                                            Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-12">
                                        <input type="text" placeholder="Enter role name" value="{{ old('name') }}"
                                            id="name" name="name"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15">
                                        @error('name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-25">
                                    <div class="col-sm-12 d-flex aling-items-center">
                                        <label class="col-form-label  color-dark fw-500 align-center">Permissions <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-12">

                                        @foreach ($permission as $parent)
                                            <div class="col-sm-12 d-flex mt-2">
                                                <div class="permission-group">

                                                    <label class="parent-label custom-checkbox-label">
                                                        <input type="checkbox" name="permissions[]"
                                                            value="{{ $parent->name }}" class="parent-checkbox demo-sw mr-2"
                                                            data-parent="{{ $parent->name }}">
                                                        <span class="custom-checkmark"></span>
                                                        {{ $parent->title }}
                                                    </label>

                                                    <div class="child-container mt-3" style="margin-left: 20px;">
                                                        @foreach ($parent->children as $child)
                                                            <label class="custom-checkbox-label">
                                                                <input type="checkbox" name="permissions[]"
                                                                    value="{{ $child->name }}" class="child-checkbox"
                                                                    data-parent="{{ $parent->name }}">
                                                                <span class="custom-checkmark"></span>
                                                                {{ $child->title }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @error('permissions')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-sm-12 d-flex flex-wrap align-items-end">
                                        <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                        <a href="{{ route('roles.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- ends: .card -->
            </div>

        </div>
    </div>
@endsection

@section('style')
    <style>
        .custom-checkbox-label {
            position: relative;
            padding-left: 28px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            user-select: none;
        }

        .custom-checkbox-label input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .custom-checkmark {
            position: absolute;
            left: 0;
            top: 2px;
            height: 15px;
            width: 15px;
            background-color: #ccc;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .custom-checkbox-label input:checked~.custom-checkmark {
            background-color: #08834a;
        }

        .custom-checkmark::after {
            content: "";
            position: absolute;
            display: none;
        }

        .custom-checkbox-label input:checked~.custom-checkmark::after {
            display: block;
            left: 6px;
            top: 2px;
            width: 4px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        /* Style the checkbox container */
        .permission-group {
            /* margin-bottom: 15px; */
        }

        /* Style for parent checkboxes */
        .parent-label {
            font-weight: bold;
            /* font-size: 14px; */
            display: flex;
            align-items: center;
            /* margin-bottom: 5px; */
        }

        .child-label {
            display: flex;
            align-items: center;
        }

        /* Child checkboxes section */
        .child-container {
            margin-left: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .checkbox-label:hover {
            color: #0958a3;
            cursor: pointer;
        }
    </style>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // When a child is checked/unchecked, update parent
            $('.child-checkbox').on('change', function() {
                let parentCheckbox = $('input[value="' + $(this).data('parent') + '"]');
                let allChildren = $('.child-checkbox[data-parent="' + $(this).data('parent') + '"]');
                let anyChecked = allChildren.is(':checked');

                parentCheckbox.prop('checked', anyChecked); // ✅ Check parent if any child is checked
            });

            // When a parent is checked/unchecked, update all children
            $('.parent-checkbox').on('change', function() {
                let allChildren = $('.child-checkbox[data-parent="' + $(this).data('parent') + '"]');
                allChildren.prop('checked', $(this).is(':checked')); // ✅ Check/uncheck all children
            });
        });
    </script>
@endsection
