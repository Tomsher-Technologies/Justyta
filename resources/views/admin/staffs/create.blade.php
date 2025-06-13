@extends('layouts.admin_default', ['title' => 'Create Staff'])

@section('content')
    <div class="container-fluid">
        <div class="row mt-4 mb-4">
            <div class="col-sm-8 offset-sm-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h4">Staff Information</h5>
                    </div>

                    <form class="form-horizontal" autocomplete="off" action="{{ route('staffs.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="name">Name
                                    <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="Enter name.." id="name" name="name"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="email">Email
                                    <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="Enter email.." id="email" name="email"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label color-dark fw-500 align-center"
                                    for="mobile">Phone</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="Enter phone number.." id="mobile" name="mobile"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        value="{{ old('mobile') }}">
                                    @error('mobile')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label color-dark fw-500 align-center"
                                    for="password">Password <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="password" placeholder="Enter password.. " autocomplete="new-password"
                                        id="password" name="password"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        value="{{ old('password') }}">
                                    @error('password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="password">Confirm
                                    Password <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="password" placeholder="Enter password.." id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        value="{{ old('password_confirmation') }}">
                                    @error('password_confirmation')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="name">Role
                                    <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="role"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-sm-9 offset-sm-3 d-flex flex-wrap align-items-end">
                                    <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                    <a href="{{ route('staffs.index') }}"
                                        class="btn btn-secondary btn-square btn-sm ml-2">Cancel</a>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
