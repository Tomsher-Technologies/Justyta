@extends('layouts.admin_default', ['title' => 'Add Law Firms'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Create Law Firm</h4>
                </div>
            </div>
        </div>
        <div class="row mt-2 mb-4">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4">
                    <div class="card-body pb-md-30">
                        <form action="{{ route('vendors.store') }}" autocomplete="off" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Law Firm Details -->
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h5><u>Law Firm Details</u></h5>
                                        </div>

                                        <div class="col-md-12 mb-3">
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
                                                    <div class="tab-pane fade @if ($loop->first) show active @endif"
                                                        id="lang-{{ $lang->code }}" role="tabpanel"
                                                        aria-labelledby="tab-{{ $lang->code }}">

                                                        <div class="form-group">
                                                            <label class="col-form-label color-dark fw-500 align-center">Law Firm Name
                                                                ({{ $lang->name }})
                                                                @if ($lang->code == 'en')
                                                                    <span class="text-danger">*</span>
                                                                @endif
                                                            </label>
                                                            <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                                                name="translations[{{ $lang->code }}][name]" placeholder="Enter law firm name ({{ $lang->name }})"
                                                                class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                                value="{{ old('translations.' . $lang->code . '.name', '') }}">
                                                            @error("translations.$lang->code.name")
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="col-form-label color-dark fw-500 align-center">About
                                                                Firm ({{ $lang->name }})</label>
                                                            <textarea name="translations[{{ $lang->code }}][about]"  @if ($lang->rtl == 1) dir="rtl" @endif class="form-control ip-gray radius-xs b-light px-15 " rows="4"
                                                                placeholder="Enter details about law firm ({{ $lang->name }})">{{ old('translations.' . $lang->code . '.about', '') }}</textarea>
                                                        </div>
                                            
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" name="email" placeholder="Enter law firm email"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('email') }}" />
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="phone" placeholder="Enter law firm phone"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('phone') }}" />
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Website URL</label>
                                            <input type="text" name="website_url" placeholder="Enter law firm website url"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('website_url') }}" />
                                            @error('website_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Logo</label>
                                            <input type="file" name="logo" id="logoInput" accept="image/*"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 ">
                                            @error('logo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div id="logoPreview" class="mt-2" style="display:none;"></div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Office
                                                Address</label>
                                            <textarea name="office_address" class="form-control ip-gray radius-xs b-light px-15 " rows="4"
                                                placeholder="Enter law firm address">{{ old('office_address') }}</textarea>
                                            @error('office_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Emirate <span
                                                    class="text-danger">*</span></label>

                                            <select name="emirate_id" class="form-control">
                                                <option value="">Select Emirate</option>
                                                @foreach (\App\Models\Emirate::with('translations')->get() as $emirate)
                                                    <option value="{{ $emirate->id }}"
                                                        {{ old('emirate_id') == $emirate->id ? 'selected' : '' }}>
                                                        {{ $emirate->translation('en')?->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('emirate_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">TRN
                                                (VAT)</label>
                                            <input type="text" name="trn" placeholder="Enter TRN"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('trn') }}">
                                            @error('trn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Subscription
                                                Plan <span class="text-danger">*</span></label>
                                            <select name="subscription_plan_id"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 ">
                                                @foreach ($plans as $plan)
                                                    <option value="{{ $plan->id }}"
                                                        {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                                        {{ $plan->title }}</option>
                                                @endforeach
                                            </select>
                                            @error('subscription_plan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Trade License
                                                (Image/PDF) <span class="text-danger">*</span></label>
                                            <input type="file" name="trade_license" id="trade_licenseInput"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                accept="image/*,application/pdf">
                                            @error('trade_license')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="trade_licensePreview" class="mt-2" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Trade License
                                                Expiry <span class="text-danger">*</span></label>
                                            <input type="text" name="trade_license_expiry" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                                value="{{ old('trade_license_expiry') }}">
                                            @error('trade_license_expiry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        
                                    </div>
                                </div>

                                <!-- Legal Documents -->
                                <div class="col-lg-12">
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-2">
                                            <h5><u>Owner Details</u></h5>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Full Name
                                                <span class="text-danger">*</span></label>
                                            <input type="text" name="owner_name" placeholder="Enter owner full name"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('owner_name') }}" />
                                            @error('owner_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" name="owner_email" placeholder="Enter owner email"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('owner_email') }}" />
                                            @error('owner_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="owner_phone"
                                                placeholder="Enter owner phone number"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('owner_phone') }}" />
                                            @error('owner_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Emirates ID
                                                Front <span class="text-danger">*</span></label>
                                            <input type="file" name="emirates_id_front" id="emirates_id_frontInput"
                                                accept="image/*,application/pdf"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15">
                                            @error('emirates_id_front')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="emirates_id_frontPreview" class="mt-2" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Emirates ID
                                                Back <span class="text-danger">*</span></label>
                                            <input type="file" name="emirates_id_back" id="emirates_id_backInput"
                                                accept="image/*,application/pdf"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 ">
                                            @error('emirates_id_back')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="emirates_id_backPreview" class="mt-2" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Emirates ID
                                                Expiry <span class="text-danger">*</span> </label>
                                            <input type="text" name="emirates_id_expiry" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                                value="{{ old('emirates_id_expiry') }}">
                                            @error('emirates_id_expiry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Residence Visa
                                                {{-- <span class="text-danger">*</span> --}}
                                            </label>
                                            <input type="file" name="residence_visa" id="residence_visaInput"
                                                accept="image/*,application/pdf"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15">
                                            @error('residence_visa')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="residence_visaPreview" class="mt-2" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Residence Visa
                                                Expiry
                                                {{-- <span class="text-danger">*</span>  --}}
                                            </label>
                                            <input type="text" name="residence_visa_expiry" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                                value="{{ old('residence_visa_expiry') }}">
                                            @error('residence_visa_expiry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Passport <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" name="passport" id="passportInput"
                                                accept="image/*,application/pdf"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15">
                                            @error('passport')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="passportPreview" class="mt-2" style="display:none;"></div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Passport
                                                Expiry <span class="text-danger">*</span></label>
                                            <input type="text" name="passport_expiry" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                                value="{{ old('passport_expiry') }}">
                                            @error('passport_expiry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Card of Law
                                                <span class="text-danger">*</span></label>
                                            <input type="file" name="card_of_law" id="card_of_lawInput"
                                                accept="image/*,application/pdf"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 ">

                                            @error('card_of_law')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="card_of_lawPreview" class="mt-2" style="display:none;"></div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Card of Law
                                                Expiry <span class="text-danger">*</span></label>
                                            <input type="text" name="card_of_law_expiry" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                                value="{{ old('card_of_law_expiry') }}">
                                            @error('card_of_law_expiry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Legal Documents -->
                                <div class="col-lg-12">
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-2">
                                            <h5><u>Credentials</u></h5>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password" autocomplete="new-password"
                                                placeholder="******"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 " />
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Confirm
                                                Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" placeholder="******"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15" />
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 mb-3 mt-2">
                                            <h5><u>Admin Commissions</u></h5>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Online Consultation Commission (%) <span class="text-danger">*</span></label>

                                            <input type="number" step="0.01" name="consultation_commission" class="form-control ih-small ip-gray radius-xs b-light px-15 " value="{{ old('consultation_commission', 0) }}">
                                            @error('consultation_commission')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ route('vendors.index') }}"
                                    class="btn btn-secondary btn-square btn-sm ml-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function setupFilePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewId);

            if (!input || !previewContainer) return;

            input.addEventListener('change', function() {
                const file = this.files[0];

                // Clear any existing content
                previewContainer.innerHTML = '';
                previewContainer.style.display = 'none';

                if (file) {
                    const fileType = file.type;

                    if (fileType.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.className = 'img-thumbnail mt-2';
                        img.style.maxHeight = '150px';

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            img.src = e.target.result;
                            previewContainer.innerHTML = '';
                            previewContainer.appendChild(img);
                            previewContainer.style.display = 'block';
                        }
                        reader.readAsDataURL(file);
                    } else if (fileType === 'application/pdf') {
                        const object = document.createElement('embed');
                        object.src = URL.createObjectURL(file);
                        object.type = 'application/pdf';
                        object.width = '100%';
                        object.height = '150px';
                        object.className = 'mt-2 border';

                        previewContainer.innerHTML = '';
                        previewContainer.appendChild(object);
                        previewContainer.style.display = 'block';
                    }
                }
            });
        }

        // Replace image previews with container IDs for both images & PDFs
        setupFilePreview('logoInput', 'logoPreview');
        setupFilePreview('emirates_id_frontInput', 'emirates_id_frontPreview');
        setupFilePreview('emirates_id_backInput', 'emirates_id_backPreview');
        setupFilePreview('residence_visaInput', 'residence_visaPreview');
        setupFilePreview('passportInput', 'passportPreview');
        setupFilePreview('card_of_lawInput', 'card_of_lawPreview');
        setupFilePreview('trade_licenseInput', 'trade_licensePreview');
    </script>
@endsection
