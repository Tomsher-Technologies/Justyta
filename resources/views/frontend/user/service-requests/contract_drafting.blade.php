@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.contract-drafting-request') }}" id="contractDraftingForm" enctype="multipart/form-data">
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
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirate') }}<span class="text-red-500">*</span></label>
                        <select id="emirate" name="emirate_id" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
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
                        <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.contract_type') }}<span class="text-red-500">*</span></label>
                        <select id="contract_type" data-url="{{ url('user/get-sub-contract-types') }}" name="contract_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['contract_type'] as $casetype)
                                <option value="{{ $casetype['id'] }}"  {{ (old('contract_type') == $casetype['id']) ? 'selected' : '' }}>{{ $casetype['value'] }}</option>
                            @endforeach
                        </select>
                        @error('contract_type')
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
                        <label for="sub_contract_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.sub_contract_type') }}<span class="text-red-500">*</span></label>
                        <select id="sub_contract_type" name="sub_contract_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            
                        </select>
                        @error('sub_contract_type')
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
                        <label for="contract_language" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.contract_language') }}<span class="text-red-500">*</span></label>

                        <select id="contract_language" name="contract_language" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['contract_languages'] as $you_rep)
                                <option value="{{ $you_rep['id'] }}" {{ (old('contract_language') == $you_rep['id']) ? 'selected' : '' }}>{{ $you_rep['value'] }}</option>
                            @endforeach
                        </select>

                        @error('contract_language')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="case-stage" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter_email') }}" name="email" value="{{ old('email') }}">
                        @error('email')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <hr class="col-span-2 my-6">

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.priority_level') }}<span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="priority-normal" type="radio" value="normal" name="priority" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" {{ (old('priority', 'normal') == 'normal') ? 'checked' : '' }} >
                                <label for="priority-normal" class="ms-2 text-sm font-medium text-gray-900">{{ __('frontend.normal') }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="priority-urgent" type="radio" value="urgent" name="priority" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" {{ (old('priority') == 'urgent') ? 'checked' : '' }}>
                                <label for="priority-urgent" class="ms-2 text-sm font-medium text-gray-900">{{ __('frontend.urgent') }}</label>
                            </div>
                        </div>
                        @error('priority')
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
                        <label for="eid" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.id') }}<span class="text-red-500">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="eid" type="file"  name="eid[]" multiple data-preview="eid-preview"/>
                        <div id="eid-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        
                        @error('eid')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="consultation-time" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.trade_license_company') }}<span class="text-red-500">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="trade_license" type="file"   name="trade_license[]" multiple data-preview="trade-preview" />
                        <div id="trade-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        @error('trade_license')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

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

                        // Add remove button
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
            }, "File size must be less than {0}KB");

            $("#contractDraftingForm").validate({
                ignore: [],
                rules: {
                    applicant_type: { required: true },
                    contract_type: { required: true },
                    emirate_id: { required: true },
                    sub_contract_type: { required: true },
                    contract_language: { required: true },
                    company_name: { required: true },
                    industry: { required: true },
                    email: { required: true,email: true },
                    priority: { required: true },
                    "documents[]": {
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 1024
                    },
                    "eid[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg",
                        fileSize: 500
                    },
                    "trade_license[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg",
                        fileSize: 500
                    }
                },
                messages: {
                    applicant_type: "{{ __('messages.applicant_type_required') }}",
                    sub_contract_type: "{{ __('messages.sub_contract_type_required') }}",
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
                        error.insertAfter(element.next('.select2')); // Insert after the visible Select2 dropdown
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

                /** 👇 Prevent actual form submission if invalid */
                submitHandler: function (form) {
                    form.submit(); // real submit
                }
            });

            $('#contract_type').on('change', function () {
                const contractId = $(this).val();
                const subSelect = $('#sub_contract_type');
                const baseUrl = $('#contract_type').data('url'); // example: "/user/get-sub-contract-types"

                subSelect.empty().append(`<option value="">Loading...</option>`);

                if (contractId) {
                    $.ajax({
                        url: `${baseUrl}/${contractId}`,
                        type: 'GET',
                        success: function (res) {
                            subSelect.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                            $.each(res, function (index, item) {
                                subSelect.append(`<option value="${item.id}">${item.value}</option>`);
                            });
                            subSelect.trigger('change'); // if select2 is used
                        },
                        error: function () {
                            subSelect.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                        }
                    });
                } else {
                    subSelect.empty().append(`<option value="">{{ __('frontend.choose_option') }}</option>`);
                }
            });
          
        });
    </script>
@endsection