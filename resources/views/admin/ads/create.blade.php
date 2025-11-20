@extends('layouts.admin_default', ['title' => 'Create New Ad'])

@section('content')
    <div class="container-fluid">
        <div class="row mt-4 mb-4">
            <div class="col-lg-12 mx-auto">
                <div class="card card-horizontal card-default card-md">
                    <div class="card-header">
                        <h5 class="mb-0 h4">Create New Ad</h5>
                    </div>
                    <div class="card-body pb-md-30 ">
                        <form action="{{ route('ads.store') }}" method="POST" enctype="multipart/form-data" class=" row">
                            @csrf

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title" class="form-control ih-small ip-gray radius-xs b-light px-15" value="{{ old('title') }}" />

                                @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Page <span class="text-danger">*</span>
                                </label>
                                <select name="page_id" class="select2 form-control" data-live-search="true">
                                    <option value="">Select Page</option>
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}" {{ old('page_id') == $page->id ? 'selected' : '' }}>{{ $page->name }}</option>
                                    @endforeach
                                </select>

                                @error('page_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>

                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                           
                           
                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Customer Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="customer_name" class="form-control ih-small ip-gray radius-xs b-light px-15 " value="{{ old('customer_name') }}" />

                                @error('customer_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Customer Email 
                                </label>
                                <input type="text" name="customer_email" class="form-control ih-small ip-gray radius-xs b-light px-15 " value="{{ old('customer_email') }}" />

                                @error('customer_email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Customer Phone 
                                </label>
                                <input type="text" name="customer_phone" class="form-control ih-small ip-gray radius-xs b-light px-15 " value="{{ old('customer_phone') }}" />

                                @error('customer_phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                             <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">Start Date <span class="text-danger">*</span></label>
                                <input type="text" name="start_date"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                    value="{{ old('start_date') }}" />
                                @error('start_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">End Date <span class="text-danger">*</span></label>
                                <input type="text" name="end_date" class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker" value="{{ old('end_date') }}" />
                                @error('end_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Redirection URL
                                </label>
                                <input type="url" name="redirection_url" class="form-control ih-small ip-gray radius-xs b-light px-15 " value="{{ old('redirection_url') }}" />

                                @error('redirection_url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Website File <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="web_file" class="form-control ih-small ip-gray radius-xs b-light px-15 " value="{{ old('web_file') }}" />

                                @error('web_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">
                                    Mobile App File <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="mob_file" class="form-control ih-small ip-gray radius-xs b-light px-15 " value="{{ old('mob_file') }}" />

                                @error('mob_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 text-right mt-4 form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ route('ads.index') }}" class="btn btn-secondary btn-square btn-sm ml-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
   
    <style>
      
    </style>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
        });
    </script>
@endsection
