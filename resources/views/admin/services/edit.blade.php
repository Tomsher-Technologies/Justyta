@extends('layouts.admin_default', ['title' => 'Edit Service Details'])

@section('content')
    <div class="container-fluid">
        <div class="row mt-4 mb-4">
            <div class="col-lg-12 mx-auto">
                <div class="card card-horizontal card-default card-md mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 h4">Update Service Details : <strong>{{ $service->name }}</strong></h5>
                    </div>
                    <div class="card-body pb-md-30">
                        <form class="form-horizontal row" autocomplete="off"
                            action="{{ route('services.update', $service->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500">Icon <span
                                        class="text-danger">*</span></label>
                                <input type="file" name="icon" accept="image/*"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                    value="{{ old('icon') }}" />
                                @error('icon')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div id="logoPreview" class="mt-2"
                                    style="{{ $service->icon ? 'display:block;' : 'display:none;' }}">
                                    @if ($service->icon)
                                        <img src="{{ asset(getUploadedImage($service->icon)) }}" class="img-thumbnail"
                                            style="max-width: 200px;">
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500">Sort Order</label>
                                <input type="text" name="sort_order"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                    value="{{ old('sort_order', $service->sort_order) }}" />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="col-form-label color-dark fw-500">Status <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                    <option value="1" {{ $service->status ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$service->status ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                           
                            <div class="col-md-12 mt-3">
                                <!-- Language Tabs -->
                                <ul class="nav nav-tabs custom-lang-tabs w-100" id="langTabs" role="tablist"
                                    style="display: flex; flex-wrap: wrap;">
                                    @foreach ($languages as $lang)
                                        <li class="nav-item flex-fill text-center">
                                            <a class="nav-link @if ($loop->first) active @endif"
                                                id="tab-{{ $lang->code }}" data-toggle="tab"
                                                href="#lang-{{ $lang->code }}" role="tab"
                                                aria-controls="lang-{{ $lang->code }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                <span class="flag-icon flag-icon-{{ $lang->flag }} mr-1"></span>
                                                {{ $lang->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- Tab Contents -->
                                <div class="tab-content custom-tab-content" id="langTabsContent">
                                    @foreach ($languages as $lang)
                                        @php
                                            $trans = $service->translations->firstWhere('lang', $lang->code);
                                        @endphp
                                        <div class="tab-pane fade @if ($loop->first) show active @endif"
                                            id="lang-{{ $lang->code }}" role="tabpanel"
                                            aria-labelledby="tab-{{ $lang->code }}">
                                            {{-- <div class="form-group">
                                                <label class="col-form-label color-dark fw-500">Title ({{ $lang->name }})</label>
                                                <input type="text" name="translations[{{ $lang->id }}][title]"
                                                    class="form-control" value="{{ $trans->title ?? '' }}">
                                            </div> --}}
                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500">Description
                                                    ({{ $lang->name }})
                                                    @if ($lang->code == 'en')
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $lang->code }}][description]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    class="form-control ip-gray radius-xs b-light px-15 @error('translations.' . $lang->code . '.description') is-invalid @enderror"
                                                    rows="10">{{ old('translations.' . $lang->code . '.description', $trans->description ?? '') }}</textarea>

                                                @error("translations.$lang->code.description")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500">Form Section Info
                                                    ({{ $lang->name }})
                                                </label>
                                                <textarea name="translations[{{ $lang->code }}][info]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    class="form-control ip-gray radius-xs b-light px-15" rows="2">{{ old('translations.' . $lang->code . '.info', $trans->info ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>

                             @if($service->slug === 'immigration-requests')
                            
                                <div id="fee-section" class="col-md-12" style="display: {{ $service->payment_active ? 'flex' : 'none' }};">
                                    
                                    <div class="row">
                                        <div class="col-md-12 mt-4">
                                            <h5 class="mb-4">Payment Details</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="service_fee" class="col-form-label color-dark fw-500">Service Fee</label>
                                            <input type="number" step="0.01" class="form-control ih-small ip-gray radius-xs b-light px-15" id="service_fee" name="service_fee"
                                                value="{{ $service->service_fee ?? 0 }}" oninput="calculateFees()">
                                        </div>

                                        <div class="col-md-3">
                                            <label for="tax_total" class="col-form-label color-dark fw-500">Tax (5%)</label>
                                            <input type="number" step="0.01" class="form-control ih-small ip-gray radius-xs b-light px-15" id="tax_total" name="tax_total"
                                                value="{{ $service->tax ?? 0 }}" readonly>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="total_amount" class="col-form-label color-dark fw-500">Total Amount</label>
                                            <input type="number" step="0.01" class="form-control ih-small ip-gray radius-xs b-light px-15" id="total_amount" name="total_amount" value="{{ $service->total_amount ?? 0 }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            @elseif($service->slug === 'online-live-consultancy')
                                <div class="col-md-12">
                                    <div class="col-md-12 mt-4">
                                        <h5 class="mb-4">Consultation Duration & Amounts</h5>

                                        <div class="row">
                                            {{-- NORMAL CONSULTATION --}}
                                            <div class="col-md-6">
                                                <div class="card border h-100">
                                                    <div class="card-header">
                                                        <h6 class="mb-0 text-uppercase text-primary fw-700">Normal Consultation</h6> (Including Tax(5%))
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            @foreach ($consultationDurations->where('type', 'normal') as $duration)
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="fw-500 d-block ">
                                                                        <span class="fw-700 color-dark">{{ $duration->duration }}</span> Minutes
                                                                    </label>
                                                                    <input type="number"
                                                                        step="0.01"
                                                                        name="durations[{{ $duration->id }}]"
                                                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                                        value="{{ $duration->amount }}"
                                                                        placeholder="AED">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- VIP CONSULTATION --}}
                                            <div class="col-md-6">
                                                <div class="card border h-100">
                                                    <div class="card-header ">
                                                        <h6 class="mb-0 text-uppercase text-secondary fw-700">VIP Consultation </h6>(Including Tax(5%))
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            @foreach ($consultationDurations->where('type', 'vip') as $duration)
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="fw-500 d-block">
                                                                        <span class="fw-700 color-dark">{{ $duration->duration }}</span> Minutes                                                
                                                                    </label>
                                                                    <input type="number"
                                                                        step="0.01"
                                                                        name="durations[{{ $duration->id }}]"
                                                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                                        value="{{ $duration->amount }}"
                                                                        placeholder="AED">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($service->slug === 'annual-retainer-agreement')
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 mt-4">
                                            <h5 class="mb-4">Payment Details</h5>
                                        </div>
                                        <div class="col-md-12 sticky-table-container">
                                            <table class="table table-bordered sticky-table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th class="text-center">Calls/Month</th>
                                                        <th class="text-center">Visits/Year</th>
                                                        <th class="text-center">Service Fee</th>
                                                        <th class="text-center">Tax (5%)</th>
                                                        <th class="text-center">Base Total</th>
                                                        <th colspan="3" class="text-center">Installments (Extra %)</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="5"></th>
                                                        <th class="text-center">1x</th>
                                                        <th class="text-center">2x</th>
                                                        <th class="text-center">4x</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="color:#000;">
                                                    @foreach($fees as $fee)
                                                        <tr>
                                                            <td>
                                                                 {{ $fee->calls_per_month }} {{ ($fee->calls_per_month == 1) ? 'Call' : 'Calls' }}
                                                            </td>
                                                            <td>
                                                                 {{ $fee->visits_per_year }} {{ ($fee->visits_per_year == 0 || $fee->visits_per_year == 1) ? 'Visit' : 'Visits' }}
                                                            </td>
                                                            <td><input type="number" step="0.01" class="form-control field-color service-fee" name="fees[{{ $fee->id }}][service_fee]" value="{{ $fee->service_fee }}"></td>
                                                            
                                                            <td><input type="number" class="form-control field-color tax" readonly value="{{ $fee->tax }}"></td>
                                                            <td><input type="number" class="form-control field-color base-total" readonly value="{{ $fee->base_total }}"></td>
                                                            @foreach($fee->installments as $inst)
                                                                <td>
                                                                    <input type="number" step="0.01" name="fees[{{ $fee->id }}][installments][{{ $inst->id }}][extra_percent]" class="form-control field-color extra-percent" value="{{ $inst->extra_percent }}">
                                                                    <input type="number" readonly class="form-control field-color mt-1 final-total" value="{{ $inst->final_total }}">
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-12 text-right mt-4 form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Update Service</button>
                                <a href="{{ route('services.index') }}"
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
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/flag-icons.min.css') }}"> --}}

    <style>
        .field-color{
            border: 1px solid #dadada;
        }
        .field-color:disabled, .field-color[readonly]
        {
            background-color: #efeff0;
        }
        .sticky-table td {
            padding: 0.35rem;
            border: 1px solid #d6d6d8;
        }
        .sticky-table-container {
            max-height: 600px;
            overflow-y: auto;
        }

        .sticky-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #343a40; /* same as .thead-dark */
            color: white;
            border: 1px solid #dee2e6 !important;
            box-shadow: inset 0 -1px 0 #dee2e6;
        }

        /* Ensure second row sticks below first row */
        .sticky-table thead tr:nth-child(2) th {
            top: 42px; /* adjust if your header row height is different */
            z-index: 1;
            border: 1px solid #dee2e6 !important;
            box-shadow: inset 0 -1px 0 #dee2e6;
        }

        .sticky-table th,.sticky-table td {
            vertical-align: middle !important;
            text-align: center;
        }

        .flag-icon {
            margin-right: 6px;
            vertical-align: middle;
        }

        .custom-lang-tabs {
            /* margin-top: 20px; */
            border-bottom: 0;
            background: #f1f1f1;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }

        .custom-lang-tabs .nav-link {
            width: 100%;
            border: none;
            background: transparent;
            color: #555;
            border-radius: 0;
            transition: background-color 0.3s ease;
            padding: 12px 0;
        }

        .custom-lang-tabs .nav-link:hover {
            background-color: #e2e6ea;
        }

        .custom-lang-tabs .nav-link.active {
            background-color: #d3be89cf;
            color: #000;
            /* border-color: #c4b07f; */
            font-weight: 500;
        }

        .custom-tab-content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            background-color: #fff;
        }
    </style>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Loop through all tab panes
            let firstErrorTab = null;

            document.querySelectorAll('.tab-pane').forEach(function(pane) {
                // Check if this tab pane has any validation error
                if (pane.querySelector('.is-invalid')) {
                    firstErrorTab = pane;
                    return false; // break loop
                }
            });

            // If any error found, activate the corresponding tab
            if (firstErrorTab) {
                let id = firstErrorTab.id;
                let tabTrigger = document.querySelector(`a[data-toggle="tab"][href="#${id}"]`);
                if (tabTrigger) {
                    $(tabTrigger).tab('show');
                }
            }
        });

        function checkPayment(checkbox) {
            const feeSection = document.getElementById('fee-section');
            if (checkbox.checked) {
                feeSection.style.display = 'flex';
            } else {
                feeSection.style.display = 'none';

                // Optionally clear fee inputs
                document.getElementById('service_fee').value = 0;
                document.getElementById('tax_total').value = 0;
                document.getElementById('total_amount').value = 0;
            }
        }

        function calculateFees() {
            const serviceFee = parseFloat(document.getElementById('service_fee').value) || 0;
            
            const tax = parseFloat((serviceFee * 0.05).toFixed(2));
            const total = parseFloat((serviceFee + tax).toFixed(2));

            document.getElementById('tax_total').value = tax;
            document.getElementById('total_amount').value = total;
        }

        // Initial calculation if values are pre-filled
        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('switch-s1').checked) {
                calculateFees();
            }
        });

        document.querySelectorAll('.service-fee,  .extra-percent').forEach(input => {
            input.addEventListener('input', function () {
                const row = input.closest('tr');
                const service = parseFloat(row.querySelector('.service-fee').value) || 0;
                const tax = (service) * 0.05;
                const base = service + tax;

                row.querySelector('.tax').value = tax.toFixed(2);
                row.querySelector('.base-total').value = base.toFixed(2);

                row.querySelectorAll('.extra-percent').forEach(percentInput => {
                    const percent = parseFloat(percentInput.value) || 0;
                    const total = base + (base * (percent / 100));
                    percentInput.nextElementSibling.value = total.toFixed(2);
                });
            });
        });
    </script>
@endsection
