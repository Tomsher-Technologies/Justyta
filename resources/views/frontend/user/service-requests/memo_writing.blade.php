@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.memo-writing-request') }}" id="courtCaseForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $service->getTranslation('title', $lang) }}
                </h2>
                <hr class="mb-5" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                    <div class="border-b pb-6">
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
                    <div class="border-b pb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.litigation_type') }}<span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="litigation-local" type="radio" value="local" name="litigation_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"  {{ (old('litigation_type','local') == 'local') ? 'checked' : '' }} />
                                <label for="litigation-local" class="ms-2 text-sm text-gray-900">{{ __('frontend.local') }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="litigation-federal" type="radio" value="federal" name="litigation_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"  {{ (old('litigation_type') == 'federal') ? 'checked' : '' }}/>
                                <label for="litigation-federal" class="ms-2 text-sm text-gray-900">{{ __('frontend.federal') }}</label>
                            </div>
                        </div>
                        @error('litigation_type')
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
                    <div class="row-span-4">
                        <label for="you-represent" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.about_case') }}</label>
                        <textarea id="about_case" rows="15" name="about_case" rows="11" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 mb-1 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.type_here') }}">{{ old('about_case') }}</textarea>
                        <span class="text-[#717171] text-sm">0/1000</span>
                    </div>
                    
                    <div>
                        <label for="case_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.case_type') }}<span class="text-red-500">*</span></label>
                        <select id="case_type" name="case_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['case_type'] as $casetype)
                                <option value="{{ $casetype['id'] }}"  {{ (old('case_type') == $casetype['id']) ? 'selected' : '' }}>{{ $casetype['value'] }}</option>
                            @endforeach
                        </select>
                        @error('case_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="you_represent" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.you_represent') }}<span class="text-red-500">*</span></label>

                        <select id="you_represent" name="you_represent" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['you_represent'] as $you_rep)
                                <option value="{{ $you_rep['id'] }}" {{ (old('you_represent') == $you_rep['id']) ? 'selected' : '' }}>{{ $you_rep['value'] }}</option>
                            @endforeach
                        </select>

                        @error('you_represent')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="case-stage" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.full_name') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter_full_name') }}" name="full_name" value="{{ old('full_name') }}">
                        @error('full_name')
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
                            <span class="text-red-500">*</span>
                        </label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="documents" type="file" name="documents[]" multiple  data-preview="documents-preview"/>
                        <div id="documents-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        @error('documents')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
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

            $("#courtCaseForm").validate({
                ignore: [],
                rules: {
                    applicant_type: { required: true },
                    litigation_type: { required: true },
                    emirate_id: { required: true },
                    case_type: { required: true },
                    you_represent: { required: true },
                    full_name: { required: true },
                    
                    "documents[]": {
                        required: true,
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
                    litigation_type: "{{ __('messages.litigation_type_required') }}",
                    emirate_id: "{{ __('messages.emirate_required') }}",
                    case_type: "{{ __('messages.case_type_required') }}",
                    you_represent: "{{ __('messages.you_represent_required') }}",
                    full_name: "{{ __('messages.full_name_required') }}",
                    
                    "documents[]": {
                        required: "{{ __('messages.document_required') }}",
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

          
        });
    </script>
@endsection