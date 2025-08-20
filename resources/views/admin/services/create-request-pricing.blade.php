@extends('layouts.admin_default', ['title' => 'Create New Pricing'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Create New Pricing</h4>
                </div>
            </div>
        </div>
        <div class="row mt-2 mb-4">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4">
                    <div class="card-body pb-md-30">
                        <form action="{{ route('request-pricing.store') }}" method="POST" enctype="multipart/form-data"  autocomplete="off">
                            @csrf
                            <div class="row">
                                <!-- Law Firm Details -->
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h5><u>Pricing Details</u></h5>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">Litigation Type <span class="text-danger">*</span></label>
                                            <select name="litigation_type" id="litigation_type" class="form-control">
                                                <option value="">Select Litigation Type</option>
                                                <option value="local" {{ old('litigation_type') == 'local' ? 'selected' : '' }}>
                                                    Local
                                                </option>
                                                <option value="federal" {{ old('litigation_type') == 'federal' ? 'selected' : '' }}>
                                                    Federal
                                                </option>
                                       
                                            </select>
                                            @error('litigation_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">Litigation Place <span class="text-danger">*</span></label>
                                            <select name="litigation_place" id="litigation_place" class="form-control">
                                                <option value="">Select Litigation Place</option>
                                                <option value="court" {{ old('litigation_place') == 'court' ? 'selected' : '' }}>
                                                    Court
                                                </option>
                                                <option value="public_prosecution" {{ old('litigation_place') == 'public_prosecution' ? 'selected' : '' }}>
                                                    Public Prosecution
                                                </option>
                                       
                                            </select>
                                            @error('litigation_place')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">Case Type <span class="text-danger">*</span></label>
                                            <select name="case_type" id="case_type" class="form-control select2" data-placeholder="Select Case Type">
                                                <option value="">Select</option>
                                                
                                            </select>
                                            @error('case_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">Request Type <span class="text-danger">*</span></label>
                                            <select name="request_type" id="request_type" class="form-control select2" data-placeholder="Select Request Type">
                                                <option value="">Select</option>
                                                
                                            </select>
                                            @error('request_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">Request Title <span class="text-danger">*</span></label>
                                            <select name="request_title" id="request_title" class="form-control select2" data-placeholder="Select Request Title">
                                                <option value="">Select</option>
                                                
                                            </select>
                                            @error('request_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Govt Fee (AED) </label>
                                            <input type="number" step="0.01" name="govt_amount" id="govt_amount" value="{{ old('govt_amount',0) }}" class="form-control" />
                                            @error('govt_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Admin Amount (AED) <span
                                                    class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="admin_amount" id="admin_amount" value="{{ old('admin_amount',0) }}" class="form-control" />
                                            @error('admin_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Tax on Admin Amount (5%)<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="tax_amount" id="tax_amount" value="{{ old('tax_amount',0) }}" class="form-control" />
                                            @error('tax_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Total Amount (AED) <span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="total_amount" id="total_amount" value="{{ old('total_amount',0) }}" class="form-control" />
                                            @error('total_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ route('request-pricing.index') }}"
                                    class="btn btn-secondary btn-square btn-sm ml-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    
@endsection

@section('script')
<script>
    $(document).ready(function () {
        function parseFloatOrZero(value) {
            return parseFloat(value) || 0;
        }

        function calculateTaxAndTotal() {
            let admin_amount = parseFloatOrZero($('#admin_amount').val());
            let govt_amount  = parseFloatOrZero($('#govt_amount').val());

            let subtotal = admin_amount + govt_amount;
            let tax = admin_amount * 0.05;
            let total = subtotal + tax;

            $('#tax_amount').val(tax.toFixed(2));
            $('#total_amount').val(total.toFixed(2));
        }

        function loadCaseTypesAndRequestTypes(callback = null) {
            let litigationType = $('#litigation_type').val();
            let litigationPlace = $('#litigation_place').val();

            if (litigationType && litigationPlace) {
                $.get("{{ route('filter-case-types') }}", { litigation_type: litigationType, litigation_place: litigationPlace }, function (data) {
                    $('#case_type').empty().append('<option value="">Select</option>');
                    $.each(data, function (key, value) {
                        $('#case_type').append('<option value="' + value.id + '">' + value.title + '</option>');
                    });

                    let oldCaseType = "{{ old('case_type') }}";
                    if (oldCaseType) {
                        $('#case_type').val(oldCaseType);
                    }
                });

                $.get("{{ route('filter-request-types') }}", { litigation_type: litigationType, litigation_place: litigationPlace }, function (data) {
                    $('#request_type').empty().append('<option value="">Select</option>');
                    $.each(data, function (key, value) {
                        $('#request_type').append('<option value="' + value.id + '">' + value.title + '</option>');
                    });

                    let oldRequestType = "{{ old('request_type') }}";
                    if (oldRequestType) {
                        $('#request_type').val(oldRequestType).trigger('change');
                    }

                    if (callback) callback();
                });
            }
        }

        function loadRequestTitles(requestTypeId, callback = null) {
            if (requestTypeId) {
                $.get("{{ route('filter-request-titles') }}", { request_type_id: requestTypeId }, function (data) {
                    $('#request_title').empty().append('<option value="">Select</option>');
                    $.each(data, function (key, value) {
                        $('#request_title').append('<option value="' + value.id + '">' + value.title + '</option>');
                    });

                    let oldRequestTitle = "{{ old('request_title') }}";
                    if (oldRequestTitle) {
                        $('#request_title').val(oldRequestTitle);
                    }

                    if (callback) callback();
                });
            }
        }

        $('#litigation_type, #litigation_place').change(function () {
            loadCaseTypesAndRequestTypes();
        });

        $('#request_type').change(function () {
            loadRequestTitles($(this).val());
        });

        $('#admin_amount, #govt_amount').on('input', function () {
            calculateTaxAndTotal();
        });

        @if(old('litigation_type') && old('litigation_place'))
            loadCaseTypesAndRequestTypes(function () {
                let oldRequestType = "{{ old('request_type') }}";
                if (oldRequestType) {
                    loadRequestTitles(oldRequestType);
                }
            });
        @endif
    });
</script>

@endsection
