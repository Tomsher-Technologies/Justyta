@extends('layouts.web_vendor_default', ['title' => __('frontend.my_account')])

@section('content')

    <div class="grid grid-cols-1 gap-6">
        <div class=" bg-white p-4 xl:p-10 rounded-[20px] border !border-[#FFE9B1] ">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('frontend.edit_profile') }}</h2>
            <hr class="mb-2">
            <div class="max-w-7xl mx-auto  sm:px-3 lg:px-2 py-4">
                <form method="POST" action="{{ route('vendor.update.profile') }}" enctype="multipart/form-data">
                    @csrf


                    <div class="p-6 bg-white rounded-2xl shadow-md" x-data="{ tab: '{{ $languages->first()->code }}' }">
                        <!-- Heading -->
                        <div class="mb-6">
                            <h5 class="text-lg font-semibold text-gray-800 underline">{{ __('frontend.law_firm_information') }}</h5>
                        </div>

                        <!-- Language Tabs -->
                        <div class="mb-4 border-b border-gray-200">
                            <nav class="-mb-px flex space-x-4" aria-label="Tabs">
                                @foreach($languages as $lang)
                                    <a
                                        href="#"
                                        @click.prevent="tab='{{ $lang->code }}'"
                                        :class="tab==='{{ $lang->code }}' 
                                            ? 'border-blue-600 text-blue-600' 
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                                        <span class="flag-icon flag-icon-{{ $lang->flag }} mr-2"></span>
                                        {{ $lang->name }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>


                        <!-- Tab Contents -->
                        <div class="space-y-4">
                            @foreach($languages as $lang)
                                @php
                                    $trans = $vendor->translations->firstWhere('lang', $lang->code);
                                @endphp
                                <div x-show="tab==='{{ $lang->code }}'" x-cloak>
                                    <!-- Law Firm Name -->
                                    <div class="mb-4 mt-2">
                                        <label class="block text-gray-700 font-medium mb-1">
                                            {{ __('frontend.law_firm_name') }} ({{ $lang->name }})
                                            @if($lang->code === 'en') <span class="text-red-500">*</span> @endif
                                        </label>
                                        <input type="text" @if($lang->rtl) dir="rtl" @endif
                                            name="translations[{{ $lang->code }}][name]"
                                            placeholder="{{ __('frontend.enter') }}"
                                            value="{{ old('translations.' . $lang->code . '.name', $trans->law_firm_name ?? '') }}"
                                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" />
                                        @error("translations.$lang->code.name")
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- About Firm -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 font-medium mb-1">
                                            {{ __('frontend.firm_description') }} ({{ $lang->name }})
                                        </label>
                                        <textarea name="translations[{{ $lang->code }}][about]" @if($lang->rtl) dir="rtl" @endif
                                            rows="4"
                                            placeholder="{{ __('frontend.enter') }}"
                                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">{{ old('translations.' . $lang->code . '.about', $trans->about ?? '') }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


                    <div class="p-6 bg-white rounded-2xl shadow-md mt-6 space-y-6">

                        <!-- Email, Phone, Website -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Email -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.email') }} <span class="text-red-500">*</span></label>
                                <input type="email" name="email" placeholder="{{ __('frontend.enter') }}"
                                    value="{{ old('email', $vendor->law_firm_email) }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.phone_number') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" placeholder="{{ __('frontend.enter') }}"
                                    value="{{ old('phone', $vendor->law_firm_phone) }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Website -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.website_url') }}</label>
                                <input type="text" name="website_url" placeholder="{{ __('frontend.enter') }}"
                                    value="{{ old('website_url', $vendor->website_url) }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('website_url')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                             <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.logo') }}</label>
                                <input type="file" name="logo" id="logoInput" accept="image/*"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                @error('logo')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror

                                <div id="logoPreview" class="mt-2 {{ $vendor->logo ? 'block' : 'hidden' }}">
                                    @if($vendor->logo)
                                        <img src="{{ asset(getUploadedImage($vendor->logo)) }}" class="rounded-md border" style="max-width:200px;">
                                    @endif
                                </div>
                            </div>

                            <!-- Office Address -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.address') }}</label>
                                <textarea name="office_address" rows="4" placeholder="{{ __('frontend.enter') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">{{ old('office_address', $vendor->office_address) }}</textarea>
                                @error('office_address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Emirate -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.emirate') }} <span class="text-red-500">*</span></label>
                                <select name="emirate_id" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                    <option value="">{{ __('frontend.choose_option') }}</option>
                                    @foreach(\App\Models\Emirate::with('translations')->get() as $emirate)
                                        <option value="{{ $emirate->id }}" {{ old('emirate_id', $vendor->emirate_id) == $emirate->id ? 'selected' : '' }}>
                                            {{ $emirate->translation('en')?->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('emirate_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- TRN -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.vat_number') }}</label>
                                <input type="text" name="trn" placeholder="{{ __('frontend.enter') }}"
                                    value="{{ old('trn', $vendor->trn) }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('trn')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Trade License -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.trade_license') }}<span class="text-red-500">*</span></label>
                                <input type="file" name="trade_license" id="trade_licenseInput" accept="image/*,application/pdf"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                @error('trade_license')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror

                                <div id="trade_licensePreview" class="mt-2 {{ $vendor->trade_license ? 'block' : 'hidden' }}">
                                    @if($vendor->trade_license)
                                        @php
                                            $ext = pathinfo($vendor->trade_license, PATHINFO_EXTENSION);
                                            $file = asset(getUploadedImage($vendor->trade_license));
                                        @endphp
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp','svg']))
                                            <img src="{{ $file }}" class="rounded-md border" style="max-width:200px;">
                                        @elseif(strtolower($ext) === 'pdf')
                                            <embed src="{{ $file }}" type="application/pdf" width="100%" height="300px" />
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Trade License Expiry -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.trade_license_expiry') }}<span class="text-red-500">*</span></label>
                                <input type="date" name="trade_license_expiry" placeholder="d M Y"
                                    value="{{ old('trade_license_expiry', $vendor->trade_license_expiry ?? '') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('trade_license_expiry')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>


                    <div class="p-6 bg-white rounded-2xl shadow-md mt-6 space-y-6">

                        <h5 class="text-lg font-semibold underline mb-4">{{ __('frontend.owner_information') }}</h5>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Owner Name -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.full_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="owner_name" placeholder="{{ __('frontend.enter') }}"
                                    value="{{ old('owner_name', $vendor->owner_name) }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('owner_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Owner Email -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.email') }} <span class="text-red-500">*</span></label>
                                <input type="email" name="owner_email" readonly placeholder="{{ __('frontend.enter') }}"
                                    value="{{ old('owner_email', $vendor->owner_email) }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('owner_email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Owner Phone -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.phone') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="owner_phone" placeholder="{{ __('frontend.enter') }}"
                                    value="{{ old('owner_phone', $vendor->owner_phone) }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('owner_phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <h5 class="text-lg font-semibold underline mb-4 mt-6">{{ __('frontend.documents') }}</h5>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Emirates ID Front -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.emirates_id_front') }} <span class="text-red-500">*</span></label>
                                <input type="file" name="emirates_id_front" id="emirates_id_frontInput" accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                @error('emirates_id_front')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <div  id="emirates_id_frontPreview" class="mt-2 {{ $vendor->emirates_id_front ? 'block' : 'hidden' }}">
                                    @if($vendor->emirates_id_front)
                                        @php
                                            $ext = pathinfo($vendor->emirates_id_front, PATHINFO_EXTENSION);
                                            $file = asset(getUploadedImage($vendor->emirates_id_front));
                                        @endphp
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp','svg']))
                                            <img src="{{ $file }}" class="rounded-md border" style="max-width:200px;">
                                        @elseif(strtolower($ext) === 'pdf')
                                            <embed src="{{ $file }}" type="application/pdf" width="100%" height="300px" />
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Emirates ID Back -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.emirates_id_back') }} <span class="text-red-500">*</span></label>
                                <input type="file" name="emirates_id_back" id="emirates_id_backInput" accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                @error('emirates_id_back')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <div id="emirates_id_backPreview" class="mt-2 {{ $vendor->emirates_id_back ? 'block' : 'hidden' }}">
                                    @if($vendor->emirates_id_back)
                                        @php
                                            $ext = pathinfo($vendor->emirates_id_back, PATHINFO_EXTENSION);
                                            $file = asset(getUploadedImage($vendor->emirates_id_back));
                                        @endphp
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp','svg']))
                                            <img src="{{ $file }}" class="rounded-md border" style="max-width:200px;">
                                        @elseif(strtolower($ext) === 'pdf')
                                            <embed src="{{ $file }}" type="application/pdf" width="100%" height="300px" />
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Emirates ID Expiry -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.emirates_id_expiry') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="emirates_id_expiry" placeholder="d M Y"
                                    value="{{ old('emirates_id_expiry', $vendor->emirates_id_expiry ?? '') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('emirates_id_expiry')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Residence Visa -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.residence_visa') }}</label>
                                <input type="file" name="residence_visa" id="residence_visaInput" accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                @error('residence_visa')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <div class="mt-2 {{ $vendor->residence_visa ? 'block' : 'hidden' }}" id="residence_visaPreview">
                                    @if($vendor->residence_visa)
                                        @php
                                            $ext = pathinfo($vendor->residence_visa, PATHINFO_EXTENSION);
                                            $file = asset(getUploadedImage($vendor->residence_visa));
                                        @endphp
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp','svg']))
                                            <img src="{{ $file }}" class="rounded-md border" style="max-width:200px;">
                                        @elseif(strtolower($ext) === 'pdf')
                                            <embed src="{{ $file }}" type="application/pdf" width="100%" height="300px" />
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Residence Visa Expiry -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.residence_visa_expiry') }}</label>
                                <input type="date" name="residence_visa_expiry" placeholder="d M Y"
                                    value="{{ old('residence_visa_expiry', $vendor->residence_visa_expiry ?? '') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('residence_visa_expiry')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Passport -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.passport') }} <span class="text-red-500">*</span></label>
                                <input type="file" name="passport" id="passportInput" accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                @error('passport')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <div id="passportPreview" class="mt-2 {{ $vendor->passport ? 'block' : 'hidden' }}">
                                    @if($vendor->passport)
                                        @php
                                            $ext = pathinfo($vendor->passport, PATHINFO_EXTENSION);
                                            $file = asset(getUploadedImage($vendor->passport));
                                        @endphp
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp','svg']))
                                            <img src="{{ $file }}" class="rounded-md border" style="max-width:200px;">
                                        @elseif(strtolower($ext) === 'pdf')
                                            <embed src="{{ $file }}" type="application/pdf" width="100%" height="300px" />
                                        @endif
                                    @endif
                                </div>
                            </div>

                             <!-- Passport Expiry -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.passport_expiry') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="passport_expiry" placeholder="d M Y"
                                    value="{{ old('passport_expiry', $vendor->passport_expiry ?? '') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('passport_expiry')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Card of Law -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.dubai_lawyer_card') }} <span class="text-red-500">*</span></label>
                                <input type="file" name="card_of_law" id="card_of_lawInput" accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                @error('card_of_law')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <div id="card_of_lawPreview" class="mt-2 {{ $vendor->card_of_law ? 'block' : 'hidden' }}">
                                    @if($vendor->card_of_law)
                                        @php
                                            $ext = pathinfo($vendor->card_of_law, PATHINFO_EXTENSION);
                                            $file = asset(getUploadedImage($vendor->card_of_law));
                                        @endphp
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp','svg']))
                                            <img src="{{ $file }}" class="rounded-md border" style="max-width:200px;">
                                        @elseif(strtolower($ext) === 'pdf')
                                            <embed src="{{ $file }}" type="application/pdf" width="100%" height="300px" />
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Card of Law Expiry -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.dubai_lawyer_card_expiry') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="card_of_law_expiry" placeholder="d M Y"
                                    value="{{ old('card_of_law_expiry', $vendor->card_of_law_expiry ?? '') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('card_of_law_expiry')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 mb-6 mt-4">
                        <button type="submit"
                            class="text-white bg-[#04502E] hover:bg-[#02331D] rounded-xl text-md w-full px-8 py-4 text-center">
                            {{ __('frontend.save_changes') }}
                        </button>
                    </div>
                </form>

                <hr class="my-8 mb-5">

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('vendor.change-password') }}" class="bg-[#E6EDEA] text-[#07683B] p-3 px-6 rounded-lg">
                        {{ __('frontend.change_password') }}
                    </a>
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

    setupFilePreview('logoInput', 'logoPreview');
    setupFilePreview('emirates_id_frontInput', 'emirates_id_frontPreview');
    setupFilePreview('emirates_id_backInput', 'emirates_id_backPreview');
    setupFilePreview('residence_visaInput', 'residence_visaPreview');
    setupFilePreview('passportInput', 'passportPreview');
    setupFilePreview('card_of_lawInput', 'card_of_lawPreview');
    setupFilePreview('trade_licenseInput', 'trade_licensePreview');
</script>
@endsection