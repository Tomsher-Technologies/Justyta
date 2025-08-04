@extends('layouts.admin_default', ['title' => 'Add Translators'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Create New Translator</h4>
                </div>
            </div>
        </div>
        <div class="row mt-2 mb-4">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4">
                    <div class="card-body pb-md-30">
                        <form action="{{ route('translators.store') }}" method="POST" enctype="multipart/form-data"  autocomplete="off">
                            @csrf
                            <div class="row">
                                <!-- Law Firm Details -->
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h5><u>Translator Details</u></h5>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center" for="type">Type <span
                                                    class="text-danger">*</span></label>
                                            <select name="type" id="type" class="form-control">
                                                <option value="inhouse" {{ old('type', '') == 'inhouse' ? 'selected' : '' }}>In-house</option>
                                                <option value="external" {{ old('type', '') == 'external' ? 'selected' : '' }}>External</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" placeholder="Enter translator name"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('name') }}" />
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" name="email" placeholder="Enter translator email"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('email') }}" />
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="phone" placeholder="Enter translator phone"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('phone') }}" />
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Company Name</label>
                                            <input type="text" name="company_name" placeholder="Enter translator company name"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 "
                                                value="{{ old('company_name') }}" />
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Profile Photo</label>
                                            <input type="file" name="logo" id="logoInput" accept="image/*"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 ">
                                            @error('logo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div id="logoPreview" class="mt-2" style="display:none;"></div>
                                        </div>


                                        <div class="col-md-4 mb-3">
                                            <label class="col-form-label color-dark fw-500 align-center">Emirate <span
                                                    class="text-danger">*</span></label>

                                            <select name="emirate_id" class="form-control select2">
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
                                            <label class="col-form-label color-dark fw-500 align-center">Trade License
                                                (Image/PDF) </label>
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
                                                Expiry </label>
                                            <input type="text" name="trade_license_expiry" placeholder="d M Y"
                                                class="form-control ih-small ip-gray radius-xs b-light px-15 datepicker"
                                                value="{{ old('trade_license_expiry') }}">
                                            @error('trade_license_expiry')
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
                                <a href="{{ Session::has('translator_last_url') ? Session::get('translator_last_url') : route('translators.index') }}"
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
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
@endsection

@section('script')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
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
        // setupFilePreview('card_of_lawInput', 'card_of_lawPreview');
        setupFilePreview('trade_licenseInput', 'trade_licensePreview');
    </script>
@endsection
