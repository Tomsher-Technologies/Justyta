@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.immigration-request') }}" id="immigrationForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $service->getTranslation('title', $lang) }}
                </h2>
                <hr class="mb-5" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
    
                    <div>
                        <label for="preferred_country" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.preferred_country') }}<span class="text-red-500">*</span></label>
                        <select id="preferred_country" name="preferred_country" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['nationality'] as $preferred_country)
                                <option value="{{ $preferred_country['id'] }}" {{ old('preferred_country') == $preferred_country['id'] ? 'selected' : '' }}>{{ $preferred_country['value'] }}</option>
                            @endforeach
                        </select>
                        @error('preferred_country')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="current_position" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.current_position') }}<span class="text-red-500">*</span></label>
                        <select id="current_position" data-url="{{ url('user/get-sub-contract-types') }}" name="position" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['immigration_positions'] as $ert)
                                <option value="{{ $ert['id'] }}"  {{ (old('current_position') == $ert['id']) ? 'selected' : '' }}>{{ $ert['value'] }}</option>
                            @endforeach
                        </select>
                        @error('position')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.age') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="age" value="{{ old('age') }}">
                        @error('age')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.nationality') }}<span class="text-red-500">*</span></label>
                        <select id="nationality" name="nationality" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['nationality'] as $nationality)
                                <option value="{{ $nationality['id'] }}" {{ old('nationality') == $nationality['id'] ? 'selected' : '' }}>{{ $nationality['value'] }}</option>
                            @endforeach
                        </select>
                        @error('nationality')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="years_of_experience" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.years_of_experience') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="years_of_experience" value="{{ old('years_of_experience') }}">
                        @error('years_of_experience')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row-span-3">
                        <label for="you-represent" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.current_address') }}</label>
                        <textarea id="address" name="address" rows="6" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 mb-1 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.type_here') }}">{{ old('address') }}</textarea>
                        <span class="text-[#717171] text-sm">0/1000</span>
                        @error('address')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                     <div>
                        <label for="residency_status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.residency_status') }}<span class="text-red-500">*</span></label>
                        <select id="residency_status" data-url="{{ url('user/get-sub-contract-types') }}" name="residency_status" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['residency_status'] as $res_status)
                                <option value="{{ $res_status['id'] }}"  {{ (old('residency_status') == $res_status['id']) ? 'selected' : '' }}>{{ $res_status['value'] }}</option>
                            @endforeach
                        </select>
                        @error('residency_status')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="current_salary" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.current_salary') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="current_salary" value="{{ old('current_salary') }}">
                        @error('current_salary')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="application_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.application_type') }}<span class="text-red-500">*</span></label>
                        <select id="application_type" data-url="{{ url('user/get-sub-contract-types') }}" name="application_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['immigration_type'] as $appType)
                                <option value="{{ $appType['id'] }}"  {{ (old('application_type') == $appType['id']) ? 'selected' : '' }}>{{ $appType['value'] }}</option>
                            @endforeach
                        </select>
                        @error('application_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <hr class="my-8 mb-5" />

                <h2 class="text-xl font-medium text-[#07683B] mb-4">
                    {{ __('frontend.upload_documents') }}
                </h2>

                <div class="grid grid-cols-2 gap-x-6 gap-6">
                    
                    <div>
                        <label for="cv" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.cv') }}<span class="text-red-500">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="cv" type="file"  name="cv[]" multiple data-preview="cv-preview"/>
                        <div id="cv-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        
                        @error('cv')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="certificates" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.certificates') }}<span class="text-red-500">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="certificates" type="file"  name="certificates[]" multiple data-preview="certificates-preview"/>
                        <div id="certificates-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        
                        @error('certificates')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="passport" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.passport') }}<span class="text-red-500">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="passport" type="file"  name="passport[]" multiple data-preview="passport-preview"/>
                        <div id="passport-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        
                        @error('passport')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.photo') }}<span class="text-red-500">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="photo" type="file"  name="photo[]" multiple data-preview="photo-preview"/>
                        <div id="photo-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        
                        @error('photo')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="account_statement" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.account_statement') }}<span class="text-red-500">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="account_statement" type="file"  name="account_statement[]" multiple data-preview="account_statement-preview"/>
                        <div id="account_statement-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        
                        @error('account_statement')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
                <hr class="my-8 mb-5">
                @if ($dropdownData['form_info']['content'] != NULL)
                    <p class="text-sm text-[#777777] mt-4 flex items-center gap-1">
                        <svg class="w-5 h-5 text-[#777777]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>

                        <span>{{ $dropdownData['form_info']['content'] }}</span>
                    </p>
                @endif
                
            </div>
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-10 rounded-[20px] border !border-[#FFE9B1] h-[calc(100vh-150px)] flex flex-col justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            {{ __('frontend.description') }}
                        </h2>

                        <hr class="my-5" />
                        <p class="text-gray-600 text-sm leading-relaxed">
                            {!! $service->getTranslation('description', $lang) !!}
                        </p>
                    </div>

                    <div>
                        @if ($dropdownData['payment']['total_amount']  != 0)
                             <div class="text-gray-700 text-lg mb-4 text-center">{{ __('frontend.payment_amount') }} <span class="font-semibold text-xl text-[#07683B]">{{ __('frontend.AED') }} {{ $dropdownData['payment']['total_amount'] ?? 0 }}</span></div>

                        @endif
                       
                        <button type="submit" class="text-white bg-[#04502E] hover:bg-[#02331D] focus:ring-4 focus:ring-blue-300 font-normal rounded-xl text-md w-full px-8 py-4 text-center transition-colors duration-200 uppercase cursor-pointer">
                            {{ __('frontend.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('ads')
    @php
        $ads = getActiveAd('immigration_request', 'web');
    @endphp

    @if ($ads && $ads->files->isNotEmpty())

        <div class="w-full mb-12 px-[50px]">
            {{-- <img src="{{ asset('assets/images/ad-img.jpg') }}" class="w-full" alt="" /> --}}
           {{-- muted --}}
            @php
                $file = $ads->files->first();
                $media = $file->file_type === 'video'
                    ? '<video class="w-full h-100" autoplay loop>
                        <source src="' . asset($file->file_path) . '" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>'
                    : '<img src="' . asset($file->file_path) . '" class="w-full h-80" alt="Ad Image">';
            @endphp

            @if (!empty($ads->cta_url))
                <a href="{{ $ads->cta_url }}" target="_blank" title="{{ $ads->cta_text ?? 'View More' }}">
                    {!! $media !!}
                </a>
            @else
                {!! $media !!}
            @endif
        </div>
    @endif
@endsection

@section('script')

    <script>
        document.querySelectorAll('.file-input').forEach(input => {
            input.addEventListener('change', function () {
                const previewId = this.dataset.preview;
                const previewBox = document.getElementById(previewId);
                previewBox.innerHTML = '';

                Array.from(this.files).forEach((file, index) => {
                    const ext = file.name.split('.').pop().toLowerCase();
                    const allowedExts = ['jpg','jpeg','png','webp','svg','pdf','doc','docx'];

                    if (!allowedExts.includes(ext)) return;

                    const reader = new FileReader();
                    const previewItem = document.createElement('div');
                    previewItem.className = "relative border p-2 rounded";

                    reader.onload = function (e) {
                        if (file.type.startsWith('image/')) {
                            previewItem.innerHTML = `<img src="${e.target.result}" class="h-20 w-20 object-cover rounded" />`;
                        } else {
                            previewItem.innerHTML = `<div class="text-xs break-words w-20 h-20 overflow-auto">${file.name}</div>`;
                        }

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'absolute top-0 right-0 bg-red-500 text-white rounded-full px-1 text-xs';
                        removeBtn.innerText = '×';
                        removeBtn.onclick = () => {
                            const dt = new DataTransfer();
                            Array.from(input.files).forEach((f, i) => {
                                if (i !== index) dt.items.add(f);
                            });
                            input.files = dt.files;
                            previewItem.remove();
                        };
                        previewItem.appendChild(removeBtn);
                        previewBox.appendChild(previewItem);
                    };

                    reader.readAsDataURL(file);
                });
            });
        });

        $(document).ready(function () {
            $.validator.addMethod("fileSize", function (value, element, param) {
                if (!element.files || element.files.length === 0) {
                    return true;
                }
                for (let i = 0; i < element.files.length; i++) {
                    if (element.files[i].size > param * 1024) {
                        return false;
                    }
                }
                return true;
            }, function (param, element) {
                return "File size must be less than " + (param / 1024) + " MB";
            });

            $("#immigrationForm").validate({
                ignore: [],
                rules: {
                    preferred_country: { required: true },
                    position: { required: true },
                    age: { required: true },
                    nationality: { required: true },
                    years_of_experience: { required: true },
                    address: { required: true },
                    residency_status: { required: true },
                    current_salary: { required: true },
                    application_type: { required: true },
                
                    "cv[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "photo[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "certificates[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "passport[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "account_statement[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    }
                },
                messages: {
                    preferred_country: "{{ __('messages.preferred_country_required') }}",
                    position: "{{ __('messages.position_required') }}",
                    age: "{{ __('messages.age_required') }}",
                    nationality: "{{ __('messages.nationality_required') }}",
                    years_of_experience: "{{ __('messages.years_of_experience_required') }}",
                    address: "{{ __('messages.address_required') }}",
                    residency_status: "{{ __('messages.residency_status_required') }}",
                    current_salary: "{{ __('messages.current_salary_required') }}",
                    application_type: "{{ __('messages.application_type_required') }}",
                    years_of_experience: "{{ __('messages.years_of_experience_required') }}",
                  
                    "cv[]": {
                        required: "{{ __('messages.cv_required') }}",
                        extension: "{{ __('messages.cv_mimes') }}",
                        fileSize: "{{ __('messages.cv_max') }}"
                    },
                    "certificates[]": {
                        required: "{{ __('messages.certificates_required') }}",
                        extension: "{{ __('messages.certificates_mimes') }}",
                        fileSize: "{{ __('messages.certificates_max') }}"
                    },
                    "account_statement[]": {
                        required: "{{ __('messages.account_statement_required') }}",
                        extension: "{{ __('messages.account_statement_mimes') }}",
                        fileSize: "{{ __('messages.account_statement_max') }}"
                    },
                    "passport[]": {
                        required: "{{ __('messages.passport_required') }}",
                        extension: "{{ __('messages.passport_mimes') }}",
                        fileSize: "{{ __('messages.passport_max') }}"
                    },
                    "photo[]": {
                        required: "{{ __('messages.photo_required') }}",
                        extension: "{{ __('messages.photo_mimes') }}",
                        fileSize: "{{ __('messages.photo_max') }}"
                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('text-red-500 text-sm');

                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2')); 
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
                submitHandler: function (form) {
                    form.submit(); 
                }
            });

        });
    </script>
@endsection