@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.legal-translation-request') }}" id="legalTranslationForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $service->getTranslation('title', $lang) }}
                </h2>
                <hr class="mb-5" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                    <div class="border-b pb-6 col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('frontend.priority_level')  }}<span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="applicant-normal" type="radio" value="normal" name="priority_level" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" {{ (old('priority_level', 'normal') == 'normal') ? 'checked' : '' }} />
                                <label for="applicant-normal" class="ms-2 text-sm text-gray-900">{{ __('frontend.normal')  }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="applicant-urgent" type="radio" value="urgent" name="priority_level" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"  {{ (old('priority_level') == 'urgent') ? 'checked' : '' }}/>
                                <label for="applicant-urgent" class="ms-2 text-sm text-gray-900">{{ __('frontend.urgent')  }}</label>
                            </div>
                        </div>
                        @error('priority_level')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                
                    <div>
                        <label for="document_language" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.document_language') }}<span class="text-red-500">*</span></label>
                        <select id="document_language" name="document_language" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['document_language'] as $document_language)
                                <option value="{{ $document_language['id'] }}" {{ old('document_language') == $document_language['id'] ? 'selected' : '' }}>{{ $document_language['value'] }}</option>
                            @endforeach
                        </select>
                        @error('document_language')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="translation_language" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.translation_language') }}<span class="text-red-500">*</span></label>
                        <select id="translation_language" name="translation_language" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['translation_language'] as $translation_language)
                                <option value="{{ $translation_language['id'] }}" {{ old('translation_language') == $translation_language['id'] ? 'selected' : '' }}>{{ $translation_language['value'] }}</option>
                            @endforeach
                        </select>
                        @error('translation_language')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.document_type') }}<span class="text-red-500">*</span></label>
                        <select id="document_type" name="document_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['document_type'] as $docType)
                                <option value="{{ $docType['id'] }}"  {{ (old('document_type') == $docType['id']) ? 'selected' : '' }}>{{ $docType['value'] }}</option>
                            @endforeach
                        </select>
                        @error('document_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="document_sub_type" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.document_sub_type') }}<span class="text-red-500">*</span></label>

                        <select id="document_sub_type" name="document_sub_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                          
                        </select>

                        @error('document_sub_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('frontend.receive_by')  }}<span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="applicant-email" type="radio" value="email" name="receive_by" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" {{ (old('receive_by', 'email') == 'email') ? 'checked' : '' }} />
                                <label for="applicant-email" class="ms-2 text-sm text-gray-900">{{ __('frontend.email')  }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="applicant-physical" type="radio" value="physical" name="receive_by" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"  {{ (old('receive_by') == 'physical') ? 'checked' : '' }}/>
                                <label for="applicant-physical" class="ms-2 text-sm text-gray-900">{{ __('frontend.physical')  }}</label>
                            </div>
                        </div>
                        @error('receive_by')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="no_of_pages" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.no_of_pages') }}<span class="text-red-500">*</span></label>
                        <input type="number" step="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="no_of_pages" value="{{ old('no_of_pages',1) }}">
                        @error('no_of_pages')
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
                        <label for="memo" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.memo') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="memo" type="file"  name="memo[]" multiple data-preview="memo-preview"/>
                        <div id="memo-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        @error('memo')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
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
                        <label for="additional_documents" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.additional_documents') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="additional_documents" type="file"  name="additional_documents[]" multiple data-preview="additional_documents-preview"/>
                        <div id="additional_documents-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        
                        @error('additional_documents')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="consultation-time" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.trade_license_company') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="trade_license" type="file"   name="trade_license[]" multiple data-preview="trade-preview" />
                        <div id="trade-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        @error('trade_license')
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
                       <div class="text-gray-700 text-lg mb-4 text-center">{{ __('frontend.payment_amount') }} <span class="font-semibold text-xl text-[#07683B]" id="translation_price_result">{{ __('frontend.AED') }} 0.00</span></div>
                       
                        <button type="submit" id="submit_button" class="text-white bg-[#04502E] hover:bg-[#02331D] focus:ring-4 focus:ring-blue-300 font-normal rounded-xl text-md w-full px-8 py-4 text-center transition-colors duration-200 uppercase cursor-pointer">
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

            $("#legalTranslationForm").validate({
                ignore: [],
                rules: {
                    priority_level: { required: true },
                    document_language: { required: true },
                    translation_language: { required: true },
                    document_type: { required: true },
                    document_sub_type: { required: true },
                    receive_by: { required: true },
                    no_of_pages: { required: true },

                    "memo[]": {
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 1024
                    },
                    "documents[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 1024
                    },
                    "additional_documents[]": {
                        extension: "pdf,jpg,jpeg,webp,png,svg",
                        fileSize: 500
                    },
                    "trade_license[]": {
                        extension: "pdf,jpg,jpeg,webp,png,svg",
                        fileSize: 500
                    }
                },
                messages: {
                    priority_level: "{{ __('messages.priority_level_required') }}",
                    document_language: "{{ __('messages.document_language_required') }}",
                    translation_language: "{{ __('messages.translation_language_required') }}",
                    document_type: "{{ __('messages.document_type_required') }}",
                    document_sub_type: "{{ __('messages.document_sub_type_required') }}",
                    receive_by: "{{ __('messages.receive_by_required') }}",
                    no_of_pages: "{{ __('messages.no_of_pages_required') }}",

                    "memo[]": {
                        extension: "{{ __('messages.memo_file_mimes') }}",
                        fileSize: "{{ __('messages.memo_file_max') }}"
                    },
                    "documents[]": {
                        required: "{{ __('messages.document_required') }}",
                        extension: "{{ __('messages.document_file_mimes') }}",
                        fileSize: "{{ __('messages.document_file_max') }}"
                    },
                    "additional_documents[]": {
                        extension: "{{ __('messages.additional_documents_mimes') }}",
                        fileSize: "{{ __('messages.additional_documents_max') }}"
                    },
                    "trade_license[]": {
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


            $('#document_type').on('change', function () {
                const documentTypeId = $(this).val();
                $('#document_sub_type').html('<option value="">{{ __("frontend.choose_option") }}</option>');

                if (documentTypeId) {
                    $.ajax({
                        url: '{{ route("get.sub.document.types") }}', // adjust this to your actual route
                        type: 'POST',
                        data: {
                            document_type: documentTypeId,
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status && response.data.length) {
                                let options = '<option value="">{{ __("frontend.choose_option") }}</option>';
                                response.data.forEach(function (item) {
                                    options += `<option value="${item.id}">${item.value}</option>`;
                                });
                                $('#document_sub_type').html(options).trigger('change'); // if using Select2
                            }
                        },
                        error: function () {
                            toastr.error("{{ __('frontend.failed_to_fetch') }}");
                        }
                    });
                }
            });

            // Optional: Trigger change on page load if document_type was pre-selected (old value)
            const oldSelectedDocType = $('#document_type').val();
            if (oldSelectedDocType) {
                $('#document_type').trigger('change');
            }

            function calculateTranslationPrice() {
                let from_language_id = $('#document_language').val();
                let to_language_id = $('#translation_language').val();
                let no_of_pages = $('input[name="no_of_pages"]').val();

                if (from_language_id && to_language_id && no_of_pages) {
                    $.ajax({
                        url: "{{ route('user.calculate-translation-price') }}", // Set this route in web.php
                        type: 'POST',
                        data: {
                            from_language_id: from_language_id,
                            to_language_id: to_language_id,
                            no_of_pages: no_of_pages,
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function () {
                            $('#submit_button').prop('disabled', true); // Disable before request
                            $('#translation_price_result').html(`<span class="text-gray-500 text-sm">Calculating...</span>`);
                        },
                        success: function (res) {
                            if (res.status) {
                                $('#translation_price_result').html(`{{ __('frontend.AED') }} ${res.data.total_amount.toFixed(2)}`);
                                $('#submit_button').prop('disabled', false); // Enable if valid
                            } else {
                                $('#translation_price_result').html(`<p class="text-red-500">${res.message}</p>`);
                                $('#submit_button').prop('disabled', true); // Disable if error
                            }
                        },
                        error: function () {
                            $('#translation_price_result').html(`<p class="text-red-500">Something went wrong</p>`);
                            $('#submit_button').prop('disabled', true);
                        }
                    });
                }
            }

            // Bind change events
            $('#document_language, #translation_language').on('change', calculateTranslationPrice);
            $('input[name="no_of_pages"]').on('input', calculateTranslationPrice);
        });
    </script>
@endsection