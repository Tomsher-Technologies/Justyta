@extends('layouts.web_login', ['title' => __('frontend.sign_up')])

@section('content')
<section class="px-[100px] py-[80px] pt-12">
    <div class="w-full p-8 space-y-6 p-5 bg-white rounded-lg ">
        <form action="{{ route('law-firm.register.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-8">
                <div class="flex items justify-between">
                    <h2 class="text-3xl text-black">{{ __('frontend.sign_up') }}</h2>
                    <a href="{{ route('frontend.login') }}" class="text-[#B9A572] underline">{{ __('frontend.back_to_login') }}</a>
                </div>

                <hr class="my-8 border-gray-300">

                <div>
                    <h3 class="text-xl font-medium text-[#07683B] mb-4">{{ __('frontend.law_firm_information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.law_firm_name') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="law_firm_name" id="law_firm_name" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('law_firm_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirate') }}<span
                                    class="text-red-500">*</span></label>
                            <select name="emirate_id" id="emirate_id" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                <option>{{ __('frontend.choose_option') }}</option>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.location') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text"  name="location"  id="location"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.phone_number') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text"  name="phone"  id="phone"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.website_url') }}</label>
                            <input type="url" name="website_url" id="website_url"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('website_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.trade_license') }}</label>
                            <input type="file" name="trade_license" id="trade_licenseInput" accept="image/*,application/pdf"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                            @error('trade_license')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="trade_licensePreview" class="mt-2" style="display:none;">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.trade_license_expiry') }}</label>
                            <input type="date" name="trade_license_expiry" placeholder="d M Y" value="{{ old('trade_license_expiry') }}" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            @error('trade_license_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.vat_number') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="trn" id="trn" value="{{ old('trn') }}"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('trn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.firm_description') }}<span
                                    class="text-red-500">*</span></label>
                            <textarea name="firm_description" id="firm_description" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 h-24"
                                placeholder="{{ __('frontend.enter') }}">{{ old('firm_description') }}</textarea>
                            <span class="text-[#717171] text-sm">0/1000</span>
                            @error('firm_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- Owner Information -->

                <hr class="my-8 border-gray-300">

                <div>
                    <h3 class="text-xl font-medium text-[#07683B] mb-4">{{ __('frontend.owner_information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.full_name') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name') }}"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('owner_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="email"  name="owner_email" id="owner_email" value="{{ old('owner_email') }}"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('owner_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.phone') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="owner_phone" id="owner_phone" value="{{ old('owner_phone') }}"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('owner_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_front') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="file" name="emirates_id_front" id="emirates_id_frontInput" accept="image/*,application/pdf" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                            @error('emirates_id_front')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="emirates_id_frontPreview" class="mt-2" style="display:none;">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_back') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="file" name="emirates_id_back" id="emirates_id_backInput" accept="image/*,application/pdf" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                            @error('emirates_id_back')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="emirates_id_backPreview" class="mt-2" style="display:none;">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_expiry') }}<span class="text-red-500">*</span></label>
                            <input type="date"  name="emirates_id_expiry" id="emirates_id_expiry" value="{{ old('emirates_id_expiry') }}" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            @error('emirates_id_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.passport') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="file" name="passport" id="passportInput" accept="image/*,application/pdf"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                            @error('passport')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="passportPreview" class="mt-2" style="display:none;"></div>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.passport_expiry') }}<span class="text-red-500">*</span></label>
                            <input type="date" name="passport_expiry" id="passport_expiry" value="{{ old('passport_expiry') }}"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            @error('passport_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.residence_visa') }}</label>
                            <input type="file" name="residence_visa" id="residence_visaInput" accept="image/*,application/pdf"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                            @error('residence_visa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="residence_visaPreview" class="mt-2" style="display:none;">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.residence_visa_expiry') }}</label>
                            <input type="date" name="residence_visa_expiry" id="residence_visa_expiry" value="{{ old('residence_visa_expiry') }}"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            @error('residence_visa_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.dubai_lawyer_card') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="file" name="card_of_law" id="card_of_lawInput" accept="image/*,application/pdf"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                            @error('card_of_law')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="card_of_lawPreview" class="mt-2" style="display:none;"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.dubai_lawyer_card_expiry') }}<span class="text-red-500">*</span></label>
                            <input type="date" name="card_of_law_expiry" id="card_of_law_expiry" value="{{ old('card_of_law_expiry') }}"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            @error('card_of_law_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.ministry_of_justice_card') }}<span class="text-red-500">*</span></label>
                            <input type="file"  name="ministry_of_justice_card" id="ministry_of_justice_cardInput" accept="image/*,application/pdf" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                            @error('ministry_of_justice_card')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="ministry_of_justice_cardPreview" class="mt-2" style="display:none;"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.ministry_of_justice_card_expiry') }}<span class="text-red-500">*</span></label>
                            <input type="date" name="ministry_of_justice_card_expiry" id="ministry_of_justice_card_expiry" value="{{ old('ministry_of_justice_card_expiry') }}"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            @error('ministry_of_justice_card_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-8 border-gray-300">
                <!-- Membership Plan & Login -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                    <div >
                        <h3 class="text-xl font-medium text-[#07683B] mb-4">{{ __('frontend.choose_membership_plan') }}</h3>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.membership_plan') }}<span class="text-red-500">*</span></label>
                            <a href="#" class="text-[#B9A572] underline text-sm" data-modal-target="plan-modal"
                                data-modal-toggle="plan-modal">{{ __('frontend.view_plans') }}</a>
                        </div>
                        <select name="subscription_plan_id" id="subscription_plan_id" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 mb-2">
                            <option>{{ __('frontend.choose_option') }}</option>
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}"
                                    {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->title }}</option>
                            @endforeach
                        </select>
                        <div class="mb-2">{{ __('frontend.membership_amount') }} : <b class="text-[#07683B]">AED 0.00</b></div>

                    </div>
                    <div>
                        <h3 class="text-xl font-medium text-[#07683B] mb-4">{{ __('frontend.set_up_login_details') }}</h3>
                        <div class="flex items-center gap-5">
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.password') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="password"  name="password" id="password" autocomplete="new-password"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 mb-2"
                                    placeholder="{{ __('frontend.enter') }}">

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.confirm_password') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="password"  name="password_confirmation" id="password_confirmation" 
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 mb-2"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <ul class="text-xs text-gray-500 list-disc pl-5 mb-2">
                            <li>{{ __('frontend.min_characters') }}</li>
                            <li>{{ __('frontend.atleast_case') }}</li>
                            <li>{{ __('frontend.atleast_digit_special') }}</li>
                        </ul>

                    </div>
                </div>
            
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="terms" id="terms" class="mr-2" />
                    <label for="terms" class="text-sm">
                            {!! __('frontend.agree_terms', ['terms' => '<a href="#" class="underline text-[#B9A572]">' . __('frontend.terms') . '</a>']) !!}
                    </label>
                </div>
                <button class="bg-[#04502E] text-white px-8 py-3 rounded-lg">{{ __('frontend.sign_up') }}</button>
            </div>
        </form>

    </div>
</section>
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
        setupFilePreview('ministry_of_justice_cardInput', 'ministry_of_justice_cardPreview');
        setupFilePreview('residence_visaInput', 'residence_visaPreview');
        setupFilePreview('passportInput', 'passportPreview');
        setupFilePreview('card_of_lawInput', 'card_of_lawPreview');
        setupFilePreview('trade_licenseInput', 'trade_licensePreview');
    </script>
@endsection