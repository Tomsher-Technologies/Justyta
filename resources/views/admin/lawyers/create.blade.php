@extends('layouts.admin_default', ['title' => 'Add Lawyers'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Create Lawyer</h4>
                </div>
            </div>
        </div>
        <div class="row mt-2 mb-4">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4">
                    <div class="card-body pb-md-30">
                        <form action="{{ route('lawyers.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="row">
                                <!-- Law Firm Details -->
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h5><u>Lawyer Details</u></h5>
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
                                                            <label class="col-form-label color-dark fw-500 align-center">Lawyer Full Name
                                                                ({{ $lang->name }})
                                                                @if ($lang->code == 'en')
                                                                    <span class="text-danger">*</span>
                                                                @endif
                                                            </label>
                                                            <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                                                name="translations[{{ $lang->code }}][name]"
                                                                class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                                value="{{ old('translations.' . $lang->code . '.name', '') }}">
                                                            @error("translations.$lang->code.name")
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                            
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Law Firm<span
                                                    class="text-danger">*</span></label>

                                            <select name="law_firm" class="form-control select2 ih-small ip-gray radius-xs b-light px-15">
                                                <option value="">Select law firm</option>
                                                @foreach($lawfirms as $lf)
                                                    <option value="{{ $lf->id }}" {{ old('law_firm') == $lf->id ? 'selected' : '' }}>
                                                        {{ $lf->law_firm_name ?? '-' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('law_firm')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                
                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" name="email" placeholder="Enter lawyer email"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('email') }}" />
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="phone" placeholder="Enter lawyer phone"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('phone') }}" />
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Profile Photo</label>
                                            <input type="file" name="photo" id="logoInput" accept="image/*"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 ">
                                            @error('photo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div id="logoPreview" class="mt-2" style="display:none;"></div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Emirate <span
                                                    class="text-danger">*</span></label>

                                            <select name="emirate_id" class="form-control select2 ih-small ip-gray radius-xs b-light px-15">
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
                                            <label class="col-form-label color-dark fw-500 align-center">Gender <span
                                                    class="text-danger">*</span></label>
                                            <select name="gender" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                                <option value="male" {{ old('gender') == "male" ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender') == "female" ? 'selected' : '' }}>Female</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Date Of Birth <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="dob" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 alldatepicker"
                                                value="{{ old('dob') }}">
                                            @error('dob')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Nationality <span
                                                    class="text-danger">*</span></label>

                                            <select name="country" class="form-control select2 ih-small ip-gray radius-xs b-light px-15">
                                                <option value="">Select Nationality</option>
                                                @foreach (\App\Models\Country::get() as $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ old('country') == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Years Of Experience <span
                                                    class="text-danger">*</span></label>

                                            <select name="experience" class="form-control select2 ih-small ip-gray radius-xs b-light px-15">
                                                <option value="">Select Years</option>
                                                @foreach($dropdowns['years_experience']->options as $option)
                                                    <option value="{{ $option->id }}" {{ old('experience') == $option->id ? 'selected' : '' }}>
                                                        {{ $option->translations->first()->name ?? 'Unnamed' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('experience')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                         <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Specialities <span
                                                    class="text-danger">*</span></label>

                                            <select name="specialities[]" id="select-tag" class="form-control select2 ip-gray radius-xs b-light px-15" multiple>
                                                <option value="">Select Specialities</option>
                                                @foreach($dropdowns['specialities']->options as $option)
                                                    <option value="{{ $option->id }}" {{ in_array($option->id, old('specialities', [])) ? 'selected' : '' }}>
                                                        {{ $option->translations->first()->name ?? 'Unnamed' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('specialities')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Languages Spoken <span
                                                    class="text-danger">*</span></label>

                                            <select name="languages[]" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" id="select-tag2" multiple>
                                                <option value="">Select Languages</option>
                                                @foreach($dropdowns['languages']->options as $option)
                                                    <option value="{{ $option->id }}"  {{ in_array($option->id, old('languages', [])) ? 'selected' : '' }}>
                                                        {{ $option->translations->first()->name ?? 'Unnamed' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('languages')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Preferred Working Hours</label>
                                            <input type="text" name="working_hours" placeholder="9:00 AM - 5:00 PM"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                value="{{ old('working_hours') }}">
                                            @error('working_hours')
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
                                            <h5><u>Document Details</u></h5>
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
                                            <label class="col-form-label color-dark fw-500 align-center">Bar Card/Legal Certificate
                                                <span class="text-danger">*</span></label>
                                            <input type="file" name="bar_card" id="bar_cardInput"
                                                accept="image/*,application/pdf"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 ">

                                            @error('bar_card')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="bar_cardPreview" class="mt-2" style="display:none;"></div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Bar Card/Legal Certificate Expiry <span class="text-danger">*</span></label>
                                            <input type="text" name="bar_card_expiry" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                                value="{{ old('bar_card_expiry') }}">
                                            @error('bar_card_expiry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Ministry Of Justice Practicing Lawyer Card
                                                <span class="text-danger">*</span></label>
                                            <input type="file" name="practicing_lawyer_card" id="practicing_lawyer_cardInput"
                                                accept="image/*,application/pdf"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 ">

                                            @error('practicing_lawyer_card')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="practicing_lawyer_cardPreview" class="mt-2" style="display:none;"></div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Ministry Of Justice Practicing Lawyer Card Expiry <span class="text-danger">*</span></label>
                                            <input type="text" name="practicing_lawyer_card_expiry" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                                value="{{ old('practicing_lawyer_card_expiry') }}">
                                            @error('practicing_lawyer_card_expiry')
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
                            </div>

                            <div class="form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ route('lawyers.index') }}"
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
        setupFilePreview('bar_cardInput', 'bar_cardPreview');
        setupFilePreview('practicing_lawyer_cardInput', 'practicing_lawyer_cardPreview');
        setupFilePreview('trade_licenseInput', 'trade_licensePreview');
    </script>
@endsection
