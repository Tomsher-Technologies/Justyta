@extends('layouts.web_vendor_default', ['title' => __('frontend.create_lawyer')])

@section('content')
<div class="bg-white rounded-2xl  p-8 pb-12">

    <div class="flex justify-between items-center mb-8">
        <h2 class="text-xl font-semibold text-gray-800">{{ __('frontend.lawyer_information') }}</h2>

        <a href="{{ Session::has('lawyers_last_url') ? Session::get('lawyers_last_url') : route('vendor.lawyers') }}" class="ml-2 flex text-black bg-[#ccb478] rounded-full py-2.5 px-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-6 text-gray-700 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18" />
            </svg>
            {{ __('frontend.go_back') }}
        </a>
    </div>
    <div class="mx-auto mt-10">
        <!-- Stepper -->
        <div class="relative mb-8">
            <div class="flex justify-between items-center">
                <div class="step flex flex-col items-start z-[999]" data-step="0">
                    <div
                        class="circle w-8 h-8 flex items-center justify-center relative  rounded-full bg-[#ccb478] text-white">
                        1</div>
                    <span class="text-sm mt-2 z-[999]">{{ __('frontend.lawyer_information') }}</span>
                </div>
                <div class="step flex flex-col items-center z-[999]" data-step="1">
                    <div class="circle w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-800">
                        2</div>
                    <span class="text-sm mt-2">{{ __('frontend.upload_documents') }}</span>
                </div>
                <div class="step flex flex-col items-end z-[999]" data-step="2">
                    <div class="circle w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-800">
                        3</div>
                    <span class="text-sm mt-2">{{ __('frontend.set_up_login_details') }}</span>
                </div>
            </div>
            <!-- Progress Line -->
            <div class="absolute top-4 left-2 w-[99%] h-1 bg-gray-200 z-0">
                <div id="progress-bar" class="h-1 bg-[#ccb478] w-0 transition-all duration-300 z-[-1] relative">
                </div>
            </div>
        </div>

        <!-- Step Content -->
        <form id="lawyerForm" action="{{ route('vendor.store.lawyers') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Step 1 -->
            <div class="step-content" data-step="0">
            
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($languages as $lang)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2r">{{ __('frontend.full_name') }}
                                ({{ $lang->name }})
                                @if ($lang->code == 'en')
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif name="translations[{{ $lang->code }}][name]"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" value="{{ old('translations.' . $lang->code . '.name', '') }}" placeholder="{{ __('frontend.enter') }}">
                            @error("translations.$lang->code.name")
                                <div class="text-sm text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }} <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" placeholder="{{ __('frontend.enter') }}"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 "
                            value="{{ old('email') }}" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.phone') }} <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="phone" placeholder="{{ __('frontend.enter') }}"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 "
                            value="{{ old('phone') }}" />
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.gender') }} <span
                                                        class="text-red-500">*</span></label>
                        <select name="gender" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="male" {{ old('gender') == "male" ? 'selected' : '' }}>{{ __('frontend.male') }}</option>
                            <option value="female" {{ old('gender') == "female" ? 'selected' : '' }}>{{ __('frontend.female') }}</option>
                        </select>
                        @error('gender')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.date_of_birth') }} <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="dob" placeholder="d M Y"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 alldatepicker"
                            value="{{ old('dob') }}">
                        @error('dob')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.nationality') }} <span
                                class="text-red-500">*</span></label>
                        <select name="country" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach (\App\Models\Country::get() as $country)
                                <option value="{{ $country->id }}"
                                    {{ old('country') == $country->id ? 'selected' : '' }}>
                                    {{ $country->getTranslation('name', app()->getLocale() ?? 'en') }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirate') }} <span
                                class="text-red-500">*</span></label>

                        <select name="emirate_id" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach (\App\Models\Emirate::with('translations')->get() as $emirate)
                                <option value="{{ $emirate->id }}"
                                    {{ old('emirate_id') == $emirate->id ? 'selected' : '' }}>
                                    {{ $emirate->getTranslation('name', app()->getLocale() ?? 'en') }}
                                </option>
                            @endforeach
                        </select>
                        @error('emirate_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.years_of_experience') }}<span
                                class="text-red-500">*</span></label>

                        <select name="experience" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach($dropdowns['years_experience']->options as $option)
                                <option value="{{ $option->id }}" {{ old('experience') == $option->id ? 'selected' : '' }}>
                                    {{ $option->getTranslation('name', app()->getLocale()) ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('experience')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.specialities') }} <span
                                class="text-red-500">*</span></label>

                        <select name="specialities[]" id="select-tag" class="form-control select2 ip-gray radius-xs b-light px-15" multiple>
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach($dropdowns['specialities']->options as $option)
                                <option value="{{ $option->id }}" {{ in_array($option->id, old('specialities', [])) ? 'selected' : '' }}>
                                    {{ $option->getTranslation('name', app()->getLocale()) ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialities')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.languages_spoken') }} <span
                                class="text-red-500">*</span></label>

                        <select name="languages[]" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" id="select-tag2" multiple>
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach($dropdowns['languages']->options as $option)
                                <option value="{{ $option->id }}"  {{ in_array($option->id, old('languages', [])) ? 'selected' : '' }}>
                                    {{ $option->getTranslation('name', app()->getLocale()) ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('languages')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.profile_photo') }}</label>
                        <input type="file" name="photo" id="logoInput" accept="image/*"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                        @error('photo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div id="logoPreview" class="mt-2" style="display:none;"></div>
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.preferred_working_hours') }}</label>
                        <input type="text" name="working_hours" id="working_hours" placeholder="9:00 AM - 12:00 PM"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 "
                            value="{{ old('working_hours') }}" />
                        @error('working_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Step 2 -->
            <div class="step-content hidden" data-step="1">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_front') }}<span
                                class="text-red-500">*</span></label>
                        <input type="file" name="emirates_id_front" id="emirates_id_frontInput" accept="image/*,application/pdf" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                        @error('emirates_id_front')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        <div id="emirates_id_frontPreview" class="mt-2" style="display:none;">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_back') }}<span
                                class="text-red-500">*</span></label>
                        <input type="file" name="emirates_id_back" id="emirates_id_backInput" accept="image/*,application/pdf" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                        @error('emirates_id_back')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        <div id="emirates_id_backPreview" class="mt-2" style="display:none;">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_expiry') }}<span class="text-red-500">*</span></label>
                        <input type="date"  name="emirates_id_expiry" id="emirates_id_expiry" value="{{ old('emirates_id_expiry') }}" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        @error('emirates_id_expiry')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.passport') }}<span
                                class="text-red-500">*</span></label>
                        <input type="file" name="passport" id="passportInput" accept="image/*,application/pdf"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                        @error('passport')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        <div id="passportPreview" class="mt-2" style="display:none;"></div>
                    </div>
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.passport_expiry') }}<span class="text-red-500">*</span></label>
                        <input type="date" name="passport_expiry" id="passport_expiry" value="{{ old('passport_expiry') }}"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        @error('passport_expiry')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.residence_visa') }}</label>
                        <input type="file" name="residence_visa" id="residence_visaInput" accept="image/*,application/pdf"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                        @error('residence_visa')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        <div id="residence_visaPreview" class="mt-2" style="display:none;">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.residence_visa_expiry') }}</label>
                        <input type="date" name="residence_visa_expiry" id="residence_visa_expiry" value="{{ old('residence_visa_expiry') }}"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        @error('residence_visa_expiry')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.bar_card_legal_certificate') }}<span
                                class="text-red-500">*</span></label>
                        <input type="file" name="bar_card" id="bar_cardInput" accept="image/*,application/pdf"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                        @error('bar_card')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        <div id="bar_cardPreview" class="mt-2" style="display:none;"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.bar_card_legal_certificate_expiry') }}<span class="text-red-500">*</span></label>
                        <input type="date" name="bar_card_expiry" id="bar_card_expiry" value="{{ old('bar_card_expiry') }}"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        @error('bar_card_expiry')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.ministry_of_justice_card') }}<span class="text-red-500">*</span></label>
                        <input type="file"  name="ministry_of_justice_card" id="ministry_of_justice_cardInput" accept="image/*,application/pdf" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                        @error('ministry_of_justice_card')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        <div id="ministry_of_justice_cardPreview" class="mt-2" style="display:none;"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.ministry_of_justice_card_expiry') }}<span class="text-red-500">*</span></label>
                        <input type="date" name="ministry_of_justice_card_expiry" id="ministry_of_justice_card_expiry" value="{{ old('ministry_of_justice_card_expiry') }}"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        @error('ministry_of_justice_card_expiry')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="step-content hidden" data-step="2">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.password') }}<span
                                class="text-red-500">*</span></label>
                        <input type="password"  name="password" id="password" autocomplete="new-password"
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 mb-2"
                            placeholder="{{ __('frontend.enter') }}">

                        @error('password')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.confirm_password') }}<span
                                class="text-red-500">*</span></label>
                        <input type="password"  name="password_confirmation" id="password_confirmation" 
                            class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 mb-2"
                            placeholder="{{ __('frontend.enter') }}">
                        @error('password_confirmation')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-between mt-4">
                <button type="button" id="prevBtn" class="px-4 py-2 text-sm text-white bg-gray-500 rounded disabled:opacity-50" disabled>Previous</button>
                <button type="button" id="nextBtn" class="px-4 py-2 text-sm text-white bg-[#07683B] rounded">Next</button>
            </div>
        </form>

    </div>
</div>
@endsection

@section('script')
    <script>
        const steps = document.querySelectorAll(".step");
        const stepContents = document.querySelectorAll(".step-content");
        const progressBar = document.getElementById("progress-bar");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        $(document).ready(function() {
            let currentStep = 0;
            const totalSteps = $(".step-content").length;

            

            function showStep(step) {

                steps.forEach((step, index) => {
                    const circle = step.querySelector(".circle");
                    if (index === currentStep) {
                    circle.classList.replace("bg-gray-300", "bg-[#ccb478]");
                    circle.classList.replace("text-gray-800", "text-white");
                    } else {
                    circle.classList.replace("bg-[#ccb478]", "bg-gray-300");
                    circle.classList.replace("text-white", "text-gray-800");
                    }
                });

                stepContents.forEach((content, index) => {
                    content.classList.toggle("hidden", index !== currentStep);
                });

                progressBar.style.width = currentStep === 0 ? "0%" : currentStep === 1 ? "50%" : "100%";

                prevBtn.disabled = currentStep === 0;


                $(".step-content").addClass("hidden");
                $(`.step-content[data-step=${step}]`).removeClass("hidden");
                $("#prevBtn").prop("disabled", step === 0);
                $("#nextBtn").text(step === totalSteps - 1 ? "Submit" : "Next");
            }

            $("#nextBtn").click(function() {
                // Validate only current step's fields
                let valid = true;
                $(`.step-content[data-step=${currentStep}]`).find(':input').each(function() {
                    if (!form.element(this)) {
                        valid = false;
                    }
                });

                if (!valid) return;

                if (currentStep < totalSteps - 1) {
                    currentStep++;
                    showStep(currentStep);
                } else {
                    $("#lawyerForm").submit();
                }
            });

            $("#prevBtn").click(function() {
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            showStep(currentStep);
        });

        $.validator.addMethod("filesize", function(value, element, param) {
                if (value === "") return true;
                if (element.files.length === 0) return false; 
                return element.files[0].size <= param;
            }, "File size must be less than {0} bytes.");

        $.validator.addMethod("strongPassword", function(value, element) {
                return this.optional(element)
                    || /[A-Z]/.test(value)
                    && /[a-z]/.test(value)
                    && /[0-9]/.test(value)
                    && /[^A-Za-z0-9]/.test(value)
                    && value.length >= 8;
            }, "{{ __('frontend.strong_password') }}");

        $.validator.addMethod("phonePattern", function(value, element) {
                return this.optional(element) || /^[+]?[0-9]{7,15}$/.test(value);
            }, "{{ __('messages.phone_regex') }}");

        $.validator.addMethod("fileext", function(value, element, param) {
            if (value === "") return true;
            if (element.files.length === 0) return false;
            var ext = value.split('.').pop().toLowerCase();
            var allowed = param.split(',').map(function(v){ return v.trim().toLowerCase(); });
            return allowed.indexOf(ext) !== -1;
        }, "{{ __('frontend.invalid_file_type') }}");

        let form = $("#lawyerForm").validate({
            ignore: [], // so hidden fields can still be validated if needed
            rules: {
                'translations[en][name]': { required: true }, // example: English name required
                email: { required: true, 
                    email: true,
                    remote: {
                        url: "{{ route('check.lawyer.email') }}",
                        type: "post",
                        data: {
                            email: function() {
                                return $("#email").val();
                            },
                            _token: "{{ csrf_token() }}"
                        }
                    }
                },
                phone: { required: true, phonePattern: true },
                gender: { required: true },
                dob: { required: true, dateISO: true },
                country: { required: true },
                emirate_id: { required: true },
                experience: { required: true },
                'specialities[]': { required: true },
                'languages[]': { required: true },
                residence_visa: {
                    required: true, 
                    fileext: "jpg,jpeg,png,svg,pdf,webp", 
                    filesize: 2 * 1024 * 1024
                },
                residence_visa_expiry: {
                    required: true, 
                    dateISO: true
                },
                password: {
                    required: true, 
                    strongPassword: true
                },
                password_confirmation: {
                    required: true, 
                    equalTo: "#password"
                },
                emirates_id_front: {
                    required: true, 
                    fileext: "jpg,jpeg,png,svg,pdf,webp", 
                    filesize: 2 * 1024 * 1024
                }, 
                emirates_id_back:  {
                    required: true, 
                    fileext: "jpg,jpeg,png,svg,pdf,webp", 
                    filesize: 2 * 1024 * 1024
                },
                emirates_id_expiry: {
                    required: true, 
                    dateISO: true
                },
                passport: {
                    required: true, 
                    fileext: "jpg,jpeg,png,svg,pdf,webp", 
                    filesize: 2 * 1024 * 1024 
                },
                passport_expiry: {
                    required: true, 
                    dateISO: true
                },
                bar_card: {
                    required: true, 
                    fileext: "jpg,jpeg,png,svg,pdf,webp", 
                    filesize: 2 * 1024 * 1024
                },
                bar_card_expiry: {
                    required: true, 
                    dateISO: true
                },
                ministry_of_justice_card: {
                    required: true, 
                    fileext: "jpg,jpeg,png,svg,pdf,webp", 
                    filesize: 2 * 1024 * 1024
                },
                ministry_of_justice_card_expiry: {
                    required: true, 
                    dateISO: true
                },
            },
            messages: {
                'translations[en][name]': {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                email: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    email: "{{ __('messages.valid_email') }}",
                    remote: "{{ __('messages.email_already_exist') }}"
                },
                phone: {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                gender: {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                dob: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    dateISO: "{{ __('frontend.valid_date') }}"
                },
                password: {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                password_confirmation: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    equalTo: "{{ __('messages.password_confirmation_mismatch') }}"
                },
                country: {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                emirate_id: {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                experience: {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                'specialities[]': {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                'languages[]': {
                    required: "{{ __('frontend.this_field_required') }}"
                },
                emirates_id_front: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    fileext: "{{ __('frontend.allowed_files') }}", 
                    filesize: "{{ __('frontend.max_file_size', ['size' => '2MB']) }}"
                },
                emirates_id_back: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    fileext: "{{ __('frontend.allowed_files') }}", 
                    filesize: "{{ __('frontend.max_file_size', ['size' => '2MB']) }}"
                },
                emirates_id_expiry: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    dateISO: "{{ __('frontend.valid_date') }}"
                },
                passport: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    fileext: "{{ __('frontend.allowed_files') }}", 
                    filesize: "{{ __('frontend.max_file_size', ['size' => '2MB']) }}"
                },
                passport_expiry: {
                    required: "{{ __('frontend.this_field_required') }}",
                    dateISO: "{{ __('frontend.valid_date') }}"
                },
                bar_card: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    fileext: "{{ __('frontend.allowed_files') }}", 
                    filesize: "{{ __('frontend.max_file_size', ['size' => '2MB']) }}"
                },
                bar_card_expiry: {
                    required: "{{ __('frontend.this_field_required') }}",
                    dateISO: "{{ __('frontend.valid_date') }}"
                },
                residence_visa_expiry: {
                    required: "{{ __('frontend.this_field_required') }}",
                    dateISO: "{{ __('frontend.valid_date') }}"
                },
                residence_visa: {
                    fileext: "{{ __('frontend.allowed_files') }}", 
                    filesize: "{{ __('frontend.max_file_size', ['size' => '2MB']) }}"
                },
                ministry_of_justice_card: {
                    required: "{{ __('frontend.this_field_required') }}", 
                    fileext: "{{ __('frontend.allowed_files') }}", 
                    filesize: "{{ __('frontend.max_file_size', ['size' => '2MB']) }}"
                },
                ministry_of_justice_card_expiry: {
                    required: "{{ __('frontend.this_field_required') }}"
                },
            },
            errorElement: 'div',
            errorClass: 'text-sm text-red-500 mt-1',
            errorPlacement: function (error, element) {
                error.addClass('text-red-500 text-sm');

                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2')); 
                } else if (element.attr("type") === "checkbox") {
                    error.insertAfter(element.closest('label'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2').find('.select2-selection')
                        .addClass('border-red-500');
                } else {
                    $(element).addClass('border-red-500');
                }
            },
            unhighlight: function (element) {
                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2').find('.select2-selection')
                        .removeClass('border-red-500');
                } else {
                    $(element).removeClass('border-red-500');
                }
            },
        });

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
        setupFilePreview('ministry_of_justice_cardInput', 'ministry_of_justice_cardPreview');
        setupFilePreview('trade_licenseInput', 'trade_licensePreview');

    </script>
@endsection