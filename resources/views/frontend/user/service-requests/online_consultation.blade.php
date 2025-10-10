@extends('layouts.web_default', ['title' => __('frontend.online_consultation') ])

@section('content')
    <form method="POST" action="{{ route('service.request.consultation') }}" id="consultationForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ __('frontend.online_consultation') }}
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
                           
                        </select>
                        @error('emirate_id')
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
                        <label for="case_stage" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.case_stage') }}<span class="text-red-500">*</span></label>

                        <select id="case_stage" name="case_stage" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['case_stage'] as $case_stage)
                                <option value="{{ $case_stage['id'] }}" {{ (old('case_stage') == $case_stage['id']) ? 'selected' : '' }}>{{ $case_stage['value'] }}</option>
                            @endforeach
                        </select>

                        @error('case_stage')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="languages" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.preferred_language') }}<span class="text-red-500">*</span></label>

                        <select id="languages" name="language" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['languages'] as $languages)
                                <option value="{{ $languages['id'] }}" {{ (old('languages') == $languages['id']) ? 'selected' : '' }}>{{ $languages['value'] }}</option>
                            @endforeach
                        </select>

                        @error('languages')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    

                    <div class=" pb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.consultant_type') }}<span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="consultant-normal" type="radio" value="normal" name="consultant_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"  {{ (old('consultant_type','normal') == 'normal') ? 'checked' : '' }} />
                                <label for="consultant-normal" class="ms-2 text-sm text-gray-900">{{ __('frontend.normal') }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="consultant-vip" type="radio" value="vip" name="consultant_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"  {{ (old('consultant_type') == 'vip') ? 'checked' : '' }}/>
                                <label for="consultant-vip" class="ms-2 text-sm text-gray-900">{{ __('frontend.vip') }}</label>
                            </div>
                        </div>
                        @error('consultant_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="case_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.case_type') }}<span class="text-red-500">*</span></label>
                        <select id="case_type" name="case_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['case_types'] as $case_types)
                                <option value="{{ $case_types['id'] }}" {{ (old('case_types') == $case_types['id']) ? 'selected' : '' }}>{{ $case_types['value'] }}</option>
                            @endforeach
                        </select>
                        @error('case_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.consultation_time') }}<span class="text-red-500">*</span></label>

                        <select id="duration" name="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['timeslots'] as $duration)
                                <option value="{{ $duration['duration'] }}" {{ (old('duration') == $duration['duration']) ? 'selected' : '' }}>{{ $duration['value'] }}</option>
                            @endforeach
                        </select>

                        @error('duration')
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
                        <div class="text-gray-700 text-lg mb-4 text-center">{{ __('frontend.payment_amount') }} <span class="font-semibold text-xl text-[#07683B]" id="price_result_div">{{ __('frontend.AED') }} <span id="price_result">0.00</span></span></div>
                       

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
        $ads = getActiveAd('online_consultancy', 'web');
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
        $(document).ready(function () {

            $("#consultationForm").validate({
                ignore: [],
                rules: {
                    applicant_type: { required: true },
                    litigation_type: { required: true },
                    emirate_id: { required: true },
                    case_type: { required: true },
                    you_represent: { required: true },
                    case_stage: { required: true },
                    language: { required: true },
                    duration: { required: true },
                },
                messages: {
                    applicant_type: "{{ __('messages.applicant_type_required') }}",
                    litigation_type: "{{ __('messages.litigation_type_required') }}",
                    emirate_id: "{{ __('messages.emirate_required') }}",
                    case_type: "{{ __('messages.case_type_required') }}",
                    you_represent: "{{ __('messages.you_represent_required') }}",
                    case_stage: "{{ __('messages.case_stage_required') }}",
                    language: "{{ __('messages.language_required') }}",
                    duration: "{{ __('messages.duration_required') }}",
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

            function loadEmirates(litigationType) {
                $.ajax({
                    url: "{{ route('user.emirates') }}",
                    type: "GET",
                    data: { litigation_type: litigationType, service: 'online-live-consultancy' },
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

            $("input[name='litigation_type']").on("change", function () {
                if ($(this).is(":checked")) {
                    loadEmirates($(this).val());
                }
            });

            let defaultChecked = $("input[name='litigation_type']:checked").val();
            if (defaultChecked) {
                loadEmirates(defaultChecked);
            }

            loadConsultationFees();
            $("input[name='consultant_type'], #duration").on("change", function () {
                loadConsultationFees();
            });

            function loadConsultationFees() {
                $("#price_result").val(0);

                let consultantType = $("input[name='consultant_type']:checked").val();
                let duration = $("#duration").val();
                $.ajax({
                    url: "{{ route('user.consultation-fee') }}",
                    type: "GET",
                    data: { consultant_type: consultantType, duration: duration },
                    success: function (response) {
                        $("#price_result").html(response.data.total);
                    },
                    error: function (xhr) {
                        console.error("Error fetching consultation fee:", xhr.responseText);
                    }
                });
            }
          
        });

        
    </script>
@endsection