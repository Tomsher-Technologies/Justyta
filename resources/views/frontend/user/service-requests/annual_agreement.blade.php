@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.annual-agreement-request') }}" id="annualAgreementForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $service->getTranslation('title', $lang) }}
                </h2>
                <hr class="mb-5" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                    
                    <div>
                        <label for="emirate_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirate') }}<span class="text-red-500">*</span></label>
                        <select id="emirate_id" name="emirate_id" data-url="{{ url('user/get-zones') }}" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
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
                        <label for="case_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.type_of_case') }}<span class="text-red-500">*</span></label>
                        <select id="case_type" name="case_type[]" multiple class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
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
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.company_name') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="company_name" value="{{ old('company_name') }}">
                        @error('company_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="no_of_calls" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.no_of_calls') }}<span class="text-red-500">*</span></label>
                        <select id="no_of_calls" name="no_of_calls" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['calls'] as $no_of_calls)
                                <option value="{{ $no_of_calls }}" {{ old('no_of_calls') == $no_of_calls ? 'selected' : '' }}>{{ $no_of_calls }}</option>
                            @endforeach
                        </select>
                        @error('no_of_calls')
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
                        <label for="no_of_visits" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.no_of_visits') }}<span class="text-red-500">*</span></label>
                        <select id="no_of_visits" name="no_of_visits" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['visits'] as $no_of_visits)
                                <option value="{{ $no_of_visits }}" {{ old('no_of_visits') == $no_of_visits ? 'selected' : '' }}>{{ $no_of_visits }}</option>
                            @endforeach
                        </select>
                        @error('no_of_visits')
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
                        <label for="no_of_installment" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.no_of_installment') }}<span class="text-red-500">*</span></label>
                        <select id="no_of_installment" name="no_of_installment" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['installments'] as $no_of_installment)
                                <option value="{{ $no_of_installment }}" {{ old('no_of_installment') == $no_of_installment ? 'selected' : '' }}>{{ $no_of_installment }}</option>
                            @endforeach
                        </select>
                        @error('no_of_installment')
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

                    
                    <div>
                        <label for="no_of_employees" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.no_of_employees') }}<span class="text-red-500">*</span></label>
                        <select id="no_of_employees" name="no_of_employees" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['no_of_employees'] as $no_of_employees)
                                <option value="{{ $no_of_employees['id'] }}" {{ old('no_of_employees') == $no_of_employees['id'] ? 'selected' : '' }}>{{ $no_of_employees['value'] }}</option>
                            @endforeach
                        </select>
                        @error('no_of_employees')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- <div>
                        <label for="lawfirm" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.lawfirm') }}<span class="text-red-500">*</span></label>
                        <select id="lawfirm" name="lawfirm" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['law_firms'] as $docType)
                                <option value="{{ $docType['id'] }}"  {{ (old('lawfirm') == $docType['id']) ? 'selected' : '' }}>{{ $docType['value'] }}</option>
                            @endforeach
                        </select>
                        @error('lawfirm')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div> --}}

                </div>

                @if ($dropdownData['form_info'])
                    <p class="text-sm text-[#777777] mt-4 flex items-center gap-1">
                        <svg class="w-5 h-5 text-[#777777]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>

                        <span>{{ $dropdownData['form_info']['description'] }}</span>
                    </p>
                    <hr class="my-8 mb-5" />
                    <p class="text-sm text-[#777777] mt-4 flex items-center gap-1">
        
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
                       <div class="text-gray-700 text-lg mb-4 text-center">{{ __('frontend.payment_amount') }} <span class="font-semibold text-xl text-[#07683B]" id="annual_price_result">{{ __('frontend.AED') }} 0.00</span></div>
                       
                        <button type="submit" id="submit_button" class="text-white bg-[#04502E] hover:bg-[#02331D] focus:ring-4 focus:ring-blue-300 font-normal rounded-xl text-md w-full px-8 py-4 text-center transition-colors duration-200 uppercase cursor-pointer">
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
        $ads = getActiveAd('company_annual_agreement', 'web');
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
            }, "File size must be less than {0}KB");

            $("#annualAgreementForm").validate({
                ignore: [],
                rules: {
                    company_name: { required: true },
                    emirate_id: { required: true },
                    license_type: { required: true },
                    license_activity: { required: true },
                    industry: { required: true },
                    no_of_employees: { required: true },
                    case_type: { required: true },
                    no_of_calls: { required: true },
                    no_of_visits: { required: true },
                    no_of_installment: { required: true },
                    lawfirm: { required: true },
                },
                messages: {
                    company_name: "{{ __('messages.company_name_required') }}",
                    emirate_id: "{{ __('messages.emirate_required') }}",
                    license_type: "{{ __('messages.license_type_required') }}",
                    license_activity: "{{ __('messages.license_activity_required') }}",
                    industry: "{{ __('messages.industry_required') }}",
                    no_of_employees: "{{ __('messages.no_of_employees_required') }}",
                    case_type: "{{ __('messages.case_type_required') }}",
                    no_of_calls: "{{ __('messages.no_of_calls_required') }}",
                    no_of_visits: "{{ __('messages.no_of_visits_required') }}",
                    no_of_installment: "{{ __('messages.no_of_installment_required') }}",
                    lawfirm: "{{ __('messages.lawfirm_required') }}",
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

            function fetchAnnualAgreementPrice() {
                const calls = $('#no_of_calls').val();
                const visits = $('#no_of_visits').val();
                const installments = $('#no_of_installment').val();

                if (!calls || !visits || !installments) {
                    $('#annual_price_result').html('');
                    return;
                }

                $.ajax({
                    url: '{{ route("ajax.getAnnualAgreementPrice") }}',
                    type: 'GET',
                    data: { calls, visits, installments },
                    success: function (res) {
                        if (res.status) {
                            const data = res.data;
                            let installmentHtml = '';
                            const installmentCount = parseInt(data.installments);
                            const perInstallmentAmount = (data.final_total / installmentCount).toFixed(2);
                            
                            for (let i = 1; i <= installmentCount; i++) {
                                if(i != 1){
                                    installmentHtml += `
                                    <div class="flex justify-between text-sm mb-2">
                                        <span>{{ __('frontend.installment') }} ${i}</span>
                                        <span>{{ __('frontend.AED') }} ${perInstallmentAmount}</span>
                                    </div> `;
                                }else{
                                    installmentHtml += `
                                    <div class="flex justify-between text-md mb-2">
                                        <span>{{ __('frontend.pay_now') }}</span>
                                        <span>{{ __('frontend.AED') }} ${perInstallmentAmount}</span>
                                    </div> `;
                                }
                                
                            }

                            $('#annual_price_result').html(`
                                <div class="bg-gray-50 p-4 border rounded-md shadow">
                                    <p class="text-md  mb-3">{{ __('frontend.total_payable') }}: <span class="text-blue-600">{{ __('frontend.AED') }} ${parseFloat(data.final_total).toFixed(2)}</span></p>
                                    <div>
                                        ${installmentHtml}
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#annual_price_result').html(`<p class="text-red-600">${res.message}</p>`);
                        }
                    },
                    error: function () {
                        $('#annual_price_result').html(`<p class="text-red-600">Error fetching price</p>`);
                    }
                });
            }

            $('#no_of_calls, #no_of_visits, #no_of_installment').on('change', fetchAnnualAgreementPrice);
        });
    </script>
@endsection