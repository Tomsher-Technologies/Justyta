@extends('layouts.admin_default', ['title' => 'Edit Pricing'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Edit Expert Report Pricing</h4>
                </div>
            </div>
        </div>
        <div class="row mt-2 mb-4">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4">
                    <div class="card-body pb-md-30">
                        <form action="{{ route('expert-pricing.update', $pricing->id) }}" method="POST" enctype="multipart/form-data"  autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Law Firm Details -->
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h5><u>Pricing Details</u></h5>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">Litigation Type <span class="text-danger">*</span></label>
                                            <select name="litigation_type" class="form-control">
                                                <option value="">--Select Litigation Type--</option>
                                                <option value="local" {{ old('litigation_type', $pricing->litigation_type) == 'local' ? 'selected' : '' }}>
                                                    Local
                                                </option>
                                                <option value="federal" {{ old('litigation_type', $pricing->litigation_type) == 'federal' ? 'selected' : '' }}>
                                                    Federal
                                                </option>
                                       
                                            </select>
                                            @error('litigation_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">Export Report Type <span class="text-danger">*</span></label>
                                            <select name="expert_report_type" class="form-control select2" data-placeholder="Expert Report Type">
                                                <option value="">Select</option>
                                                @foreach($dropdowns['expert_report_type']->options as $report_type)
                                                    <option value="{{ $report_type->id }}"  {{ old('expert_report_type', $pricing->expert_report_type_id) == $report_type->id ? 'selected' : '' }}>
                                                        {{ $report_type->getTranslation('name', 'en') ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('expert_report_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Expert Report Language <span class="text-danger">*</span></label>
                                           <select name="expert_report_language" id="expert_report_language" class="select2 form-control" data-placeholder="Select Option">
                                                <option value="">Select</option>
                                                @foreach ($dropdowns['expert_report_languages']->options as $report_lang)
                                                    <option value="{{ $report_lang->id }}"  {{ old('expert_report_language', $pricing->language_id) == $report_lang->id ? 'selected' : '' }}>
                                                        {{ $report_lang->getTranslation('name', 'en') ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('expert_report_language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Admin Amount <span
                                                    class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="admin_amount" id="admin_amount" value="{{ old('admin_amount', $pricing->admin_fee) }}" class="form-control" />
                                            @error('admin_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Tax Amount (5%)<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', $pricing->vat) }}" class="form-control" />
                                            @error('tax_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Total Amount<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="total_amount" id="total_amount" value="{{ old('total_amount', $pricing->total) }}" class="form-control" />
                                            @error('total_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        
                                    </div>
                                </div>

                            </div>

                            <div class="form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ Session::get('expert_pricing_last_url') ?? route('expert-pricing.index') }}"
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
    
    function parseFloatOrZero(value) {
        return parseFloat(value) || 0;
    }

    function calculateTaxAndTotal() {
        let admin_amount    = parseFloatOrZero($('#admin_amount').val());
        
        let subtotal = admin_amount;
        let tax = subtotal * 0.05;
        let total = subtotal + tax;

        $('#tax_amount').val(tax.toFixed(2));
        $('#total_amount').val(total.toFixed(2));
    }


     $(document).ready(function () {
  
        $('#admin_amount').on('input', function () {
            calculateTaxAndTotal();
        });
    });



</script>
@endsection
