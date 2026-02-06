@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.expert-report-request') }}" id="expertReportForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 xl:p-10 rounded-[20px] border !border-[#FFE9B1]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $service->getTranslation('title', $lang) }}
                </h2>
                <hr class="mb-5" />
                
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-x-6 gap-y-4 mb-6">
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
                   
                    <div class="border-b pb-6 ">
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('frontend.applicant_place')  }}<span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="applicant-local" type="radio" value="local" name="applicant_place" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 applicant_place" {{ (old('applicant_place', 'local') == 'local') ? 'checked' : '' }} />
                                <label for="applicant-local" class="ms-2 text-sm text-gray-900">{{ __('frontend.local')  }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="applicant-federal" type="radio" value="federal" name="applicant_place" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 applicant_place"  {{ (old('applicant_place') == 'federal') ? 'checked' : '' }}/>
                                <label for="applicant-federal" class="ms-2 text-sm text-gray-900">{{ __('frontend.federal')  }}</label>
                            </div>
                        </div>
                        @error('applicant_place')
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

                     <div class="row-span-3">
                        <label for="you-represent" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.about_case') }}</label>
                        <textarea id="about_case" name="about_case" rows="11" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 mb-1 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.type_here') }}">{{ old('about_case') }}</textarea>
                        {{-- <span class="text-[#717171] text-sm">0/1000</span> --}}
                    </div>

                    <div>
                        <label for="expert_report_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.expert_report_type') }}<span class="text-red-500">*</span></label>
                        <select id="expert_report_type" data-url="{{ url('user/get-sub-contract-types') }}" name="expert_report_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['expert_report_type'] as $ert)
                                <option value="{{ $ert['id'] }}"  {{ (old('expert_report_type') == $ert['id']) ? 'selected' : '' }}>{{ $ert['value'] }}</option>
                            @endforeach
                        </select>
                        @error('expert_report_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="expert_report_language" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.expert_report_language') }}<span class="text-red-500">*</span></label>
                        <select id="expert_report_language" data-url="{{ url('user/get-sub-contract-types') }}" name="expert_report_language" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['expert_report_languages'] as $erl)
                                <option value="{{ $erl['id'] }}"  {{ (old('expert_report_language') == $erl['id']) ? 'selected' : '' }}>{{ $erl['value'] }}</option>
                            @endforeach
                        </select>
                        @error('expert_report_language')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    
                </div>

                <hr class="my-8 mb-5" />

                <h2 class="text-xl font-medium text-[#07683B]">
                    {{ __('frontend.upload_documents') }}
                </h2>

                <div class="mb-6">
                    <small class="text-gray-500 d-block mb-4">
                        ({{ __('frontend.file_size_info') }})
                    </small>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-x-6 gap-2 xl:gap-6">
                    
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
                            {{ __('frontend.trade_license_company') }}<span class="text-red-500 tradeLicence">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="trade_license" type="file"   name="trade_license[]" multiple data-preview="trade-preview" />
                        <div id="trade-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        @error('trade_license')
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
                </div>
                
            </div>
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 xl:p-10 rounded-[20px] border !border-[#FFE9B1] h-[auto] xl:h-[calc(100vh-150px)] flex flex-col justify-between">
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
                        <div class="text-gray-700 text-lg mb-4 mt-5 xl:mt-0 text-center">{{ __('frontend.payment_amount') }} <span class="font-semibold text-xl text-[#07683B]" id="price_result_div">{{ __('frontend.AED') }} <span id="price_result">0.00</span></span></div>
                       

                        <button type="submit" class="text-white bg-[#04502E] hover:bg-[#02331D] focus:ring-4 focus:ring-blue-300 font-normal rounded-xl text-md w-full px-8 py-4 text-center transition-colors duration-200 uppercase cursor-pointer mt-5">
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
        $ads = getActiveAd('expert_report', 'web');
    @endphp

    @if ($ads && $ads->files->isNotEmpty())

        <div class="relative w-full mb-12 px-[50px]">
            @php
                $file = $ads->files->first();
            @endphp

            <a href="{{ $ads->cta_url ?? '#' }}" target="_blank" title="{{ $ads->cta_text ?? 'View More' }}">
                @if($file->file_type === 'video')
                    <video id="adVideo{{ $ads->id }}" class="w-full object-cover"  style="height: 500px;" autoplay muted loop playsinline>
                        <source src="{{ asset($file->file_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ asset($file->file_path) }}" class="w-full h-80 object-cover" alt="Ad Image">
                @endif
            </a>

            @if($file->file_type === 'video')
                <button 
                    onclick="toggleMute('adVideo{{ $ads->id }}', this)" 
                    class="absolute bottom-2 bg-gray-800 bg-opacity-50 text-white px-3 py-1 rounded hover:bg-opacity-80 z-10" style="right: 4rem;">
                    <!-- Unmute Icon -->
                    <svg id="unmuteIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9v6h4l5 5V4l-5 5H9" />
                    </svg>

                    <!-- Mute Icon -->
                    <svg id="muteIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white-500 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9v6h4l5 5V4l-5 5H9" />
                        <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
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

            $("#expertReportForm").validate({
                ignore: [],
                rules: {
                    applicant_type: { required: true },
                    applicant_place: { required: true },
                    emirate_id: { required: true },
                    expert_report_type: { required: true },
                    expert_report_language: { required: true },
                    about_case: { required: false, maxlength: 1000 },
                    "documents[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "eid[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "trade_license[]": {
                        required: function (element) {
                            return $('input[name="applicant_type"]:checked').val() === 'company';
                        },
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    }
                },
                messages: {
                    applicant_type: "{{ __('messages.applicant_type_required') }}",
                    applicant_place: "{{ __('messages.applicant_place_required') }}",
                    emirate_id: "{{ __('messages.emirate_required') }}",
                    expert_report_type: "{{ __('messages.expert_report_type_required') }}",
                    expert_report_language: "{{ __('messages.expert_report_language_required') }}",
                  
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

            let defaultChecked = $("input[name='applicant_place']:checked").val();
            if (defaultChecked) {
                loadEmirates(defaultChecked);
            }

            $("input[name='applicant_place']").on("change", function () {
                if ($(this).is(":checked")) {
                    loadEmirates($(this).val());
                }
            });

            function loadEmirates(applicant_place) {
                $.ajax({
                    url: "{{ route('user.emirates') }}",
                    type: "GET",
                    data: { litigation_type: applicant_place, service: 'expert-report' },
                    success: function (response) {
                        let $emirate = $("#emirate");
                        $emirate.empty();
                        $emirate.append('<option value="">{{ __("frontend.choose_option") }}</option>');

                        let emirateData = response.data.emirates;
                        $.each(emirateData, function (index, item) {
                            $emirate.append('<option value="' + item.id + '">' + item.value + '</option>');
                        });
                    },
                    error: function (xhr) {
                        console.error("Error fetching emirates:", xhr.responseText);
                    }
                });
            }
            $('.applicant_place, #expert_report_type, #expert_report_language').on('change', fetchPrice);

            function fetchPrice() {
                const litigation_type = $('input[name="applicant_place"]:checked').val();
                const report_type = $('#expert_report_type').val();
                const report_language = $('#expert_report_language').val();

                if (!litigation_type || !report_type || !report_language) {
                    $('#price_result').html(0);
                    return;
                }

                $.ajax({
                    url: '{{ route("user.expert-report-price") }}',
                    type: 'GET',
                    data: { litigation_type, report_type, report_language },
                    success: function (res) {
                        if (res.status) {
                            const data = res.data;
                           
                            $('#price_result').html(data.total);
                        } else {
                            $('#price_result').html(0);
                        }
                    },
                    error: function () {
                        $('#price_result').html(0);
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('input[name="applicant_type"]');
            const tradeLicence = document.querySelector('.tradeLicence');

            function toggleTradeLicence() {
                const selected = document.querySelector('input[name="applicant_type"]:checked')?.value;
                tradeLicence.style.display = (selected === 'company') ? 'inline' : 'none';
            }

            toggleTradeLicence();

            radios.forEach(radio => {
                radio.addEventListener('change', toggleTradeLicence);
            });
        });
    </script>
@endsection