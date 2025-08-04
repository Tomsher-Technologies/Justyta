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
                        <form action="{{ route('translator-pricing.store') }}" method="POST" enctype="multipart/form-data"  autocomplete="off">
                            @csrf
                            <div class="row">
                                <!-- Law Firm Details -->
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h5><u>Pricing Details</u></h5>
                                            <input type="hidden" name="translator_id" id="translator_id" value="{{ old('translator_id', $id) }}" class="form-control" />
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">From Language <span class="text-danger">*</span></label>
                                            <select name="from_language" class="form-control">
                                                @foreach ($languages as $lang)
                                                    <option value="{{ $lang->id }}" {{ old('from_language') == $lang->id ? 'selected' : '' }}>
                                                        {{ $lang->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('from_language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">To Language <span class="text-danger">*</span></label>
                                            <select name="to_language" class="form-control">
                                                @foreach ($languages->whereIn('id', [1, 3]) as $lang)
                                                    <option value="{{ $lang->id }}" {{ old('to_language') == $lang->id ? 'selected' : '' }}>
                                                        {{ $lang->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('to_language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Document Type <span
                                                    class="text-danger">*</span></label>
                                           <select name="doc_type" id="doc_type" class="select2 form-control" data-placeholder="Select Option">
                                                <option value="">Select</option>
                                                @foreach ($documentTypes as $doctype)
                                                    <option value="{{ $doctype->id }}" {{ old('doc_type') == $doctype->id ? 'selected' : '' }}>
                                                        {{ $doctype->getTranslation('name','en')}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('doc_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Subdocument Type<span
                                                    class="text-danger">*</span></label>
                                           <select name="sub_doc_type" id="sub_doc_type" class="select2 form-control" id="sub_doc_type">
                                                
                                            </select>
                                            @error('sub_doc_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Normal Priority Amount </label>
                                            <input type="number" step="0.01" name="normal" id="normal" value="{{ old('normal',0) }}" class="form-control" />
                                            @error('normal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Urgent Priority Amount </label>
                                            <input type="number" step="0.01" name="urgent" id="urgent" value="{{ old('urgent',0) }}" class="form-control" />
                                            @error('urgent')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Email Delivery Amount </label>
                                            <input type="number" step="0.01" name="email_delivery" id="email_delivery" value="{{ old('email_delivery',0) }}" class="form-control" />
                                            @error('email_delivery')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Physical Delivery Amount </label>
                                            <input type="number" step="0.01" name="physical_delivery" id="physical_delivery" value="{{ old('physical_delivery',0) }}" class="form-control" />
                                            @error('physical_delivery')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Admin Amount <span
                                                    class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="admin_amount" id="admin_amount" value="{{ old('admin_amount',0) }}" class="form-control" />
                                            @error('admin_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Translator Amount <span class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="translator_amount" id="translator_amount" value="{{ old('translator_amount',0) }}" class="form-control" />
                                            @error('translator_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Tax Amount (5%)<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="tax_amount" id="tax_amount" value="{{ old('tax_amount',0) }}" class="form-control" />
                                            @error('tax_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Total Amount<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="total_amount" id="total_amount" value="{{ old('total_amount',0) }}" class="form-control" />
                                            @error('total_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-2">
                                            <h5><u>Duration Details (Hours)</u></h5>
                                        </div>
                                        <div class="col-md-12 row">
                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 1-10 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="hours_1_10" id="hours_1_10" value="{{ old('hours_1_10',0) }}" class="form-control" />
                                                @error('hours_1_10')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 11-20 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="hours_11_20" id="hours_11_20" value="{{ old('hours_11_20',0) }}" class="form-control" />
                                                @error('hours_11_20')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 21-30 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="hours_21_30" id="hours_21_30" value="{{ old('hours_21_30',0) }}" class="form-control" />
                                                @error('hours_21_30')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 31-50 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="hours_31_50" id="hours_31_50" value="{{ old('hours_31_50',0) }}" class="form-control" />
                                                @error('hours_31_50')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 50+ <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="hours_above_50" id="hours_above_50" value="{{ old('hours_above_50',0) }}" class="form-control" />
                                                @error('hours_above_50')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ Session::has('translator_pricing_last_url') ? Session::get('translator_pricing_last_url') : route('translator-pricing', ['id' => $id ]) }}"
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
    const checkUrl = "{{ route('get-sub-doc-types', ['docTypeId' => '__docType__']) }}";

    function loadSubDocTypes(docTypeId, selectedSubTypeId = null) {
        if (!docTypeId) return;
        const routeUrl = checkUrl.replace('__docType__', docTypeId);

        $.ajax({
            url: routeUrl,
            method: 'GET',
            success: function (response) {
                let subDocSelect = $('#sub_doc_type');
                subDocSelect.empty().append('<option value="">{{ __("frontend.choose_option") }}</option>');

                let data = response.data;
                data.forEach(function (sub) {
                    let selected = (selectedSubTypeId == sub.id) ? 'selected' : '';
                    subDocSelect.append(`<option value="${sub.id}" ${selected}>${sub.value}</option>`);
                });
            }
        });
    }

    $('#doc_type').on('change', function () {
        loadSubDocTypes($(this).val());
    });

    let oldDocType = '{{ old("doc_type") }}';
    let oldSubDocType = '{{ old("sub_doc_type") }}';

    if (oldDocType) {
        loadSubDocTypes(oldDocType, oldSubDocType);
    }

    function parseFloatOrZero(value) {
        return parseFloat(value) || 0;
    }

    function calculateTaxAndTotal() {
        let normal      = parseFloatOrZero($('#normal').val());
        let urgent      = parseFloatOrZero($('#urgent').val());
        let email       = parseFloatOrZero($('#email_delivery').val());
        let physical    = parseFloatOrZero($('#physical_delivery').val());
        let admin       = parseFloatOrZero($('#admin_amount').val());
        let translator  = parseFloatOrZero($('#translator_amount').val());

        let subtotal = normal + urgent + email + physical + admin + translator;
        let tax = subtotal * 0.05;
        let total = subtotal + tax;

        $('#tax_amount').val(tax.toFixed(2));
        $('#total_amount').val(total.toFixed(2));
    }

     $(document).ready(function () {
        calculateTaxAndTotal();
        $('#normal, #urgent, #email_delivery, #physical_delivery, #admin_amount, #translator_amount').on('input', function () {
            calculateTaxAndTotal();
        });
    });

</script>
@endsection
