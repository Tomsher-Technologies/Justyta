@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.company-setup-request') }}" id="companySetupForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $service->getTranslation('title', $lang) }}
                </h2>
                <hr class="mb-5" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">

                    <div class="border-b pb-6 col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('frontend.applicant_type')  }}<span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="applicant-company" type="radio" value="company" name="applicant_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" {{ (old('applicant_type', 'company') == 'company') ? 'checked' : '' }} />
                                <label for="applicant-company" class="ms-2 text-sm text-gray-900">{{ __('frontend.company')  }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="applicant-individual" type="radio" value="individual" name="applicant_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"  {{ (old('applicant_type') == 'individual') ? 'checked' : '' }}/>
                                <label for="applicant-individual" class="ms-2 text-sm text-gray-900">{{ __('frontend.individual')  }}</label>
                            </div>
                        </div>
                        @error('applicant_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                   
                    <div>
                        <label for="emirate_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirate') }}<span class="text-red-500">*</span></label>
                        <select id="emirate_id" name="emirate_id" data-url="{{ url('user/get-zones') }}" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.select_emirate') }}</option>
                            @foreach ($dropdownData['emirates'] as $emirate)
                                <option value="{{ $emirate['id'] }}" {{ old('emirate_id') == $emirate['id'] ? 'selected' : '' }}>{{ $emirate['value'] }}</option>
                            @endforeach
                        </select>
                        @error('emirate_id')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="company_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.company_type') }}<span class="text-red-500">*</span></label>
                        <select id="company_type" name="company_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['company_type'] as $companyType)
                                <option value="{{ $companyType['id'] }}"  {{ (old('company_type') == $companyType['id']) ? 'selected' : '' }}>{{ $companyType['value'] }}</option>
                            @endforeach
                        </select>
                        @error('company_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="zone" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.zone') }}<span class="text-red-500">*</span></label>

                        <select id="zone" name="zone" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                          
                        </select>
                        @error('zone')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.company_name') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="company_name" value="{{ old('company_name') }}">
                        @error('company_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="industry" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.industry') }}<span class="text-red-500">*</span></label>

                        <select id="industry" name="industry" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['industries'] as $you_rep)
                                <option value="{{ $you_rep['id'] }}" {{ (old('industry') == $you_rep['id']) ? 'selected' : '' }}>{{ $you_rep['value'] }}</option>
                            @endforeach
                        </select>

                        @error('industry')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.mobile') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="mobile"  id="mobile" value="{{ old('mobile') }}">
                        @error('mobile')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                   
                    <div>
                        <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.license_type') }}<span class="text-red-500">*</span></label>
                        <select id="license_type" data-url="{{ url('user/get-license-activities') }}" name="license_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['license_type'] as $licenseType)
                                <option value="{{ $licenseType['id'] }}"  {{ (old('license_type') == $licenseType['id']) ? 'selected' : '' }}>{{ $licenseType['value'] }}</option>
                            @endforeach
                        </select>
                        @error('license_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                     <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter_email') }}" name="email" value="{{ old('email') }}">
                        @error('email')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>


                    <div>
                        <label for="license_activity" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.license_activity') }}<span class="text-red-500">*</span></label>
                        <select id="license_activity" name="license_activity" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            
                        </select>
                        @error('license_activity')
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
                        <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.supporting_documents') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="documents" type="file" name="documents[]" multiple  data-preview="documents-preview"/>
                        <div id="documents-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        @error('documents')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
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
        $ads = getActiveAd('company_setup', 'web');
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

            $('#mobile').on('input', function () {
                this.value = this.value.replace(/[^0-9+]/g, '');
            });

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

            $("#companySetupForm").validate({
                ignore: [],
                rules: {
                    applicant_type: { required: true },
                    contract_type: { required: true },
                    emirate_id: { required: true },
                    license_activity: { required: true },
                    contract_language: { required: true },
                    company_name: { required: true },
                    industry: { required: true },
                    email: { required: true,email: true },
                    priority: { required: true },
                    "documents[]": {
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "eid[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "trade_license[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    }
                },
                messages: {
                    applicant_type: "{{ __('messages.applicant_type_required') }}",
                    license_activity: "{{ __('messages.license_activity_required') }}",
                    emirate_id: "{{ __('messages.emirate_required') }}",
                    contract_type: "{{ __('messages.contract_type_required') }}",
                    contract_language: "{{ __('messages.contract_language_required') }}",
                    company_name: "{{ __('messages.company_person_name_required') }}",
                    industry: "{{ __('messages.industry_required') }}",
                    email: {
                        required: "{{ __('messages.email_required') }}",
                        email: "{{ __('messages.valid_email') }}"
                    },
                    priority: "{{ __('messages.priority_required') }}",

                    "documents[]": {
                        extension: "{{ __('messages.document_file_mimes') }}",
                        fileSize: "{{ __('messages.document_file_max') }}"
                    },
                    "eid[]": {
                        required: "{{ __('messages.eid_required') }}",
                        extension: "{{ __('messages.eid_file_mimes') }}",
                        fileSize: "{{ __('messages.eid_file_max') }}"
                    },
                    "trade_license[]": {
                        required: "{{ __('messages.trade_license_required') }}",
                        extension: "{{ __('messages.trade_license_file_mimes') }}",
                        fileSize: "{{ __('messages.trade_license_file_max') }}"
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

            $('#license_type').on('change', function () {
                const license_typeId = $(this).val();
                const subSelect = $('#license_activity');
                const baseUrl = $('#license_type').data('url');

                subSelect.empty().append(`<option value="">Loading...</option>`);

                if (license_typeId) {
                    $.ajax({
                        url: `${baseUrl}/${license_typeId}`,
                        type: 'GET',
                        success: function (res) {
                            subSelect.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                            $.each(res, function (index, item) {
                                subSelect.append(`<option value="${item.id}">${item.value}</option>`);
                            });
                            subSelect.trigger('change');
                        },
                        error: function () {
                            subSelect.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                        }
                    });
                } else {
                    subSelect.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                }
            });
          
            $('#emirate_id').on('change', function () {
                const emirateId = $(this).val();
                const zones = $('#zone');
                const actionUrl = $('#emirate_id').data('url'); 

                zones.empty().append(`<option value="">Loading...</option>`);

                if (emirateId) {
                    $.ajax({
                        url: `${actionUrl}/${emirateId}`,
                        type: 'GET',
                        success: function (res) {
                            zones.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                            $.each(res, function (index, item) {
                                zones.append(`<option value="${item.id}">${item.value}</option>`);
                            });
                            zones.trigger('change'); 
                        },
                        error: function () {
                            zones.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                        }
                    });
                } else {
                    zones.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                }
            });
        });
    </script>
@endsection