@extends('layouts.admin_default',['title' => 'Edit Staff'])

@section('content')
<div class="container-fluid">
    <div class="row mt-4 mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card card-horizontal card-default card-md mb-4">
                <div class="card-header">
                    <h5 class="mb-0 h4">Staff Information</h5>
                </div>

                <form action="{{ route('staffs.update', $staff->id) }}" method="POST" autocomplete="off">
                    <input name="_method" type="hidden" value="PATCH">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="name">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Enter name.." id="name" name="name" value="{{ old('name', $staff->name) }}" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="email">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Enter email.." id="email" name="email" value="{{ old('email', $staff->email) }}" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                @error('email')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="mobile">Phone</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Enter phone number.." id="mobile" name="mobile" value="{{ old('mobile', $staff->phone) }}" class="form-control ih-small ip-gray radius-xs b-light px-15" >
                                @error('mobile')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="password">Password <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" placeholder="Enter password.." autocomplete="new-password"  id="password" name="password" class="form-control ih-small ip-gray radius-xs b-light px-15"  value="{{ old('password') }}">
                                @error('password')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="password">Confirm Password <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" placeholder="Enter password.." id="password_confirmation" name="password_confirmation" class="form-control ih-small ip-gray radius-xs b-light px-15"  value="{{ old('password_confirmation') }}">
                                @error('password_confirmation')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label color-dark fw-500 align-center" for="name">Role <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select name="role_id" required class="form-control ih-small ip-gray radius-xs b-light px-15 aiz-selectpicker">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ $staff->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-sm-9 offset-sm-3 d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ route('staffs.index') }}" class="btn btn-secondary btn-square btn-sm ml-2">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
