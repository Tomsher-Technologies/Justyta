@extends('layouts.admin_default', ['title' => 'Create New Pricing'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Create {{ $plan->title }} Plan Translation Pricing</h4>
                </div>
            </div>
        </div>
        <div class="row mt-2 mb-4">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4">
                    <div class="card-body pb-md-30">
                        <form action="{{ route('plan-pricing.store') }}" method="POST" enctype="multipart/form-data"  autocomplete="off">
                            @csrf
                            <div class="row">
                                <!-- Law Firm Details -->
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h5><u>Pricing Details</u></h5>
                                            <input type="hidden" name="membership_plan_id" id="membership_plan_id" value="{{ old('membership_plan_id', $id) }}" class="form-control" />
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

                                        
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-2">
                                            <h5><u>Normal Priority</u></h5>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Email Amount </label>
                                            <input type="number" step="0.01" name="email_delivery_normal_email" id="email_delivery_normal_email" value="{{ old('email_delivery_normal_email',0) }}" class="form-control" />
                                            @error('email_delivery_normal_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Admin Amount <span
                                                    class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="admin_amount_normal_email" id="admin_amount_normal_email" value="{{ old('admin_amount_normal_email',0) }}" class="form-control" />
                                            @error('admin_amount_normal_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Translator Amount <span class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="plan_amount_normal_email" id="plan_amount_normal_email" value="{{ old('plan_amount_normal_email',0) }}" class="form-control" />
                                            @error('plan_amount_normal_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Tax Amount (5%)<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="tax_amount_normal_email" id="tax_amount_normal_email" value="{{ old('tax_amount_normal_email',0) }}" class="form-control" />
                                            @error('tax_amount_normal_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Total Amount<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="total_amount_normal_email" id="total_amount_normal_email" value="{{ old('total_amount_normal_email',0) }}" class="form-control" />
                                            @error('total_amount_normal_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Physical Amount </label>
                                            <input type="number" step="0.01" name="physical_delivery_normal_physical" id="physical_delivery_normal_physical" value="{{ old('physical_delivery_normal_physical',0) }}" class="form-control" />
                                            @error('physical_delivery_normal_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Admin Amount <span
                                                    class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="admin_amount_normal_physical" id="admin_amount_normal_physical" value="{{ old('admin_amount_normal_physical',0) }}" class="form-control" />
                                            @error('admin_amount_normal_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Translator Amount <span class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="plan_amount_normal_physical" id="plan_amount_normal_physical" value="{{ old('plan_amount_normal_physical',0) }}" class="form-control" />
                                            @error('plan_amount_normal_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Tax Amount (5%)<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="tax_amount_normal_physical" id="tax_amount_normal_physical" value="{{ old('tax_amount_normal_physical',0) }}" class="form-control" />
                                            @error('tax_amount_normal_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Total Amount<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="total_amount_normal_physical" id="total_amount_normal_physical" value="{{ old('total_amount_normal_physical',0) }}" class="form-control" />
                                            @error('total_amount_normal_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-2">
                                            <h5><u>Urgent Priority</u></h5>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Email Amount </label>
                                            <input type="number" step="0.01" name="email_delivery_urgent_email" id="email_delivery_urgent_email" value="{{ old('email_delivery_urgent_email',0) }}" class="form-control" />
                                            @error('email_delivery_urgent_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Admin Amount <span
                                                    class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="admin_amount_urgent_email" id="admin_amount_urgent_email" value="{{ old('admin_amount_urgent_email',0) }}" class="form-control" />
                                            @error('admin_amount_urgent_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Translator Amount <span class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="plan_amount_urgent_email" id="plan_amount_urgent_email" value="{{ old('plan_amount_urgent_email',0) }}" class="form-control" />
                                            @error('plan_amount_urgent_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Tax Amount (5%)<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="tax_amount_urgent_email" id="tax_amount_urgent_email" value="{{ old('tax_amount_urgent_email',0) }}" class="form-control" />
                                            @error('tax_amount_urgent_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Total Amount<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="total_amount_urgent_email" id="total_amount_urgent_email" value="{{ old('total_amount_urgent_email',0) }}" class="form-control" />
                                            @error('total_amount_urgent_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Physical Amount </label>
                                            <input type="number" step="0.01" name="physical_delivery_urgent_physical" id="physical_delivery_urgent_physical" value="{{ old('physical_delivery_urgent_physical',0) }}" class="form-control" />
                                            @error('physical_delivery_urgent_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Admin Amount <span
                                                    class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="admin_amount_urgent_physical" id="admin_amount_urgent_physical" value="{{ old('admin_amount_urgent_physical',0) }}" class="form-control" />
                                            @error('admin_amount_urgent_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Translator Amount <span class="text-danger">*</span> </label>
                                            <input type="number" step="0.01" name="plan_amount_urgent_physical" id="plan_amount_urgent_physical" value="{{ old('plan_amount_urgent_physical',0) }}" class="form-control" />
                                            @error('plan_amount_urgent_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Tax Amount (5%)<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="tax_amount_urgent_physical" id="tax_amount_urgent_physical" value="{{ old('tax_amount_urgent_physical',0) }}" class="form-control" />
                                            @error('tax_amount_urgent_physical')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center"> Total Amount<span class="text-danger">*</span> </label>
                                            <input type="number" readonly step="0.01" name="total_amount_urgent_physical" id="total_amount_urgent_physical" value="{{ old('total_amount_urgent_physical',0) }}" class="form-control" />
                                            @error('total_amount_urgent_physical')
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
                                            <div class="col-md-12 mb-3 mt-2">
                                                <h6 class="text-secondary" style="font-size: 15px"><u>Normal Priority (Hours)</u></h6>
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 1-10 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="normal_hours_1_10" id="normal_hours_1_10" value="{{ old('normal_hours_1_10',0) }}" class="form-control" />
                                                @error('normal_hours_1_10')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 11-20 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="normal_hours_11_20" id="normal_hours_11_20" value="{{ old('normal_hours_11_20',0) }}" class="form-control" />
                                                @error('normal_hours_11_20')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 21-30 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="normal_hours_21_30" id="normal_hours_21_30" value="{{ old('normal_hours_21_30',0) }}" class="form-control" />
                                                @error('normal_hours_21_30')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 31-50 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="normal_hours_31_50" id="normal_hours_31_50" value="{{ old('normal_hours_31_50',0) }}" class="form-control" />
                                                @error('normal_hours_31_50')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 50+ <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="normal_hours_above_50" id="normal_hours_above_50" value="{{ old('normal_hours_above_50',0) }}" class="form-control" />
                                                @error('normal_hours_above_50')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                           
                                        </div>

                                        <div class="col-md-12 row">
                                            <div class="col-md-12 mb-3 mt-2">
                                                <h6 class="text-secondary" style="font-size: 15px"><u>Urgent Priority (Hours)</u></h6>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 1-10 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="urgent_hours_1_10" id="urgent_hours_1_10" value="{{ old('urgent_hours_1_10',0) }}" class="form-control" />
                                                @error('urgent_hours_1_10')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 11-20 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="urgent_hours_11_20" id="urgent_hours_11_20" value="{{ old('urgent_hours_11_20',0) }}" class="form-control" />
                                                @error('urgent_hours_11_20')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 21-30 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="urgent_hours_21_30" id="urgent_hours_21_30" value="{{ old('urgent_hours_21_30',0) }}" class="form-control" />
                                                @error('urgent_hours_21_30')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 31-50 <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="urgent_hours_31_50" id="urgent_hours_31_50" value="{{ old('urgent_hours_31_50',0) }}" class="form-control" />
                                                @error('urgent_hours_31_50')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label color-dark fw-500 align-center">
                                                    No. Of Pages 50+ <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="urgent_hours_above_50" id="urgent_hours_above_50" value="{{ old('urgent_hours_above_50',0) }}" class="form-control" />
                                                @error('urgent_hours_above_50')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ Session::has('plan_pricing_last_url') ? Session::get('plan_pricing_last_url') : route('plan-pricing', ['id' => $id ]) }}"
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

    function calculateNormalEmailTaxAndTotal() {
        let delivery_amount       = parseFloatOrZero($('#email_delivery_normal_email').val());
        let admin_amount    = parseFloatOrZero($('#admin_amount_normal_email').val());
        let plan_amount       = parseFloatOrZero($('#plan_amount_normal_email').val());

        let subtotal = delivery_amount + admin_amount + plan_amount;
        let tax = subtotal * 0.05;
        let total = subtotal + tax;

        $('#tax_amount_normal_email').val(tax.toFixed(2));
        $('#total_amount_normal_email').val(total.toFixed(2));
    }

    function calculateNormalPhysicalTaxAndTotal() {
        let delivery_amount       = parseFloatOrZero($('#physical_delivery_normal_physical').val());
        let admin_amount    = parseFloatOrZero($('#admin_amount_normal_physical').val());
        let plan_amount       = parseFloatOrZero($('#plan_amount_normal_physical').val());

        let subtotal = delivery_amount + admin_amount + plan_amount;
        let tax = subtotal * 0.05;
        let total = subtotal + tax;

        $('#tax_amount_normal_physical').val(tax.toFixed(2));
        $('#total_amount_normal_physical').val(total.toFixed(2));
    }

    function calculateUrgentEmailTaxAndTotal() {
        let delivery_amount       = parseFloatOrZero($('#email_delivery_urgent_email').val());
        let admin_amount    = parseFloatOrZero($('#admin_amount_urgent_email').val());
        let plan_amount       = parseFloatOrZero($('#plan_amount_urgent_email').val());

        let subtotal = delivery_amount + admin_amount + plan_amount;
        let tax = subtotal * 0.05;
        let total = subtotal + tax;

        $('#tax_amount_urgent_email').val(tax.toFixed(2));
        $('#total_amount_urgent_email').val(total.toFixed(2));
    }

    function calculateUrgentPhysicalTaxAndTotal() {
        let delivery_amount       = parseFloatOrZero($('#physical_delivery_urgent_physical').val());
        let admin_amount    = parseFloatOrZero($('#admin_amount_urgent_physical').val());
        let plan_amount       = parseFloatOrZero($('#plan_amount_urgent_physical').val());

        let subtotal = delivery_amount + admin_amount + plan_amount;
        let tax = subtotal * 0.05;
        let total = subtotal + tax;

        $('#tax_amount_urgent_physical').val(tax.toFixed(2));
        $('#total_amount_urgent_physical').val(total.toFixed(2));
    }


     $(document).ready(function () {
  
        $('#email_delivery_normal_email, #admin_amount_normal_email, #plan_amount_normal_email').on('input', function () {
            calculateNormalEmailTaxAndTotal();
        });

        $('#physical_delivery_normal_physical, #admin_amount_normal_physical, #plan_amount_normal_physical').on('input', function () {
            calculateNormalPhysicalTaxAndTotal();
        });

        $('#email_delivery_urgent_email, #admin_amount_urgent_email, #plan_amount_urgent_email').on('input', function () {
            calculateUrgentEmailTaxAndTotal();
        });

        $('#physical_delivery_urgent_physical, #admin_amount_urgent_physical, #plan_amount_urgent_physical').on('input', function () {
            calculateUrgentPhysicalTaxAndTotal();
        });
    });



</script>
@endsection
