@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.power-of-attorney-request') }}" id="poaForm" enctype="multipart/form-data">
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
                        <label for="case-stage" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.name_of_authorized') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="name_of_authorized" value="{{ old('name_of_authorized') }}">
                        @error('name_of_authorized')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="poa_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.poa_type') }}<span class="text-red-500">*</span></label>
                        <select id="poa_type" name="poa_type" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['poa_type'] as $poaType)
                                <option value="{{ $poaType['id'] }}"  {{ (old('poa_type') == $poaType['id']) ? 'selected' : '' }}>{{ $poaType['value'] }}</option>
                            @endforeach
                        </select>
                        @error('poa_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="id_number_authorized" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.id_number_authorized') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="id_number_authorized" value="{{ old('id_number_authorized') }}">
                        @error('id_number_authorized')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="appointer_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.appointer_name') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="appointer_name" value="{{ old('appointer_name') }}">
                        @error('appointer_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="authorized_mobile" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.authorized_mobile') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="authorized_mobile" id="authorized_mobile" value="{{ old('authorized_mobile') }}">
                        @error('authorized_mobile')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="id_number" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.id_number') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="id_number" value="{{ old('id_number') }}">
                        @error('id_number')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row-span-3">
                        <label for="you-represent" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.authorized_address') }}</label>
                        <textarea id="authorized_address" name="authorized_address" rows="6" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 mb-1 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.type_here') }}">{{ old('authorized_address') }}</textarea>
                        <span class="text-[#717171] text-sm">0/1000</span>
                        @error('authorized_address')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="appointer_mobile" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.appointer_mobile') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter') }}" name="appointer_mobile" id="appointer_mobile" value="{{ old('appointer_mobile') }}">
                        @error('appointer_mobile')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="relationship" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.relationship') }}<span class="text-red-500">*</span></label>
                        <select id="relationship" name="relationship" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['poa_relationships'] as $relationship)
                                <option value="{{ $relationship['id'] }}"  {{ (old('relationship') == $relationship['id']) ? 'selected' : '' }}>{{ $relationship['value'] }}</option>
                            @endforeach
                        </select>
                        @error('relationship')
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
                        <label for="appointer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.appointer_id') }}
                            {{-- <span class="text-red-500">*</span> --}}
                        </label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="appointer_id" type="file" name="appointer_id[]" multiple  data-preview="appointer_id-preview"/>
                        <div id="appointer_id-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        @error('appointer_id')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="authorized_id" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.authorized_id') }}<span class="text-red-500">*</span></label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="authorized_id" type="file"  name="authorized_id[]" multiple data-preview="authorized_id-preview"/>
                        <div id="authorized_id-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        
                        @error('authorized_id')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="consultation-time" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('frontend.authorized_passport') }}</label>
                        <input class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="authorized_passport" type="file"   name="authorized_passport[]" multiple data-preview="authorized_passport-preview" />
                        <div id="authorized_passport-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                        @error('authorized_passport')
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
        $ads = getActiveAd('power_of_attorney', 'web');
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

            $('#authorized_mobile, #appointer_mobile').on('input', function () {
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

            $("#poaForm").validate({
                ignore: [],
                rules: {
                    applicant_type: { required: true },
                    appointer_name: { required: true },
                    emirate_id: { required: true },
                    id_number: { required: true },
                    appointer_mobile: { required: true },
                    poa_type: { required: true },
                    name_of_authorized: { required: true },
                    authorized_mobile: { required: true },
                    id_number_authorized: { required: true },
                    authorized_address: { required: true },
                    relationship: { required: true },
                    
                    "authorized_passport[]": {
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "appointer_id[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    },
                    "authorized_id[]": {
                        required: true,
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 102400
                    }
                },
                messages: {
                    applicant_type: "{{ __('messages.applicant_type_required') }}",
                    emirate_id: "{{ __('messages.emirate_required') }}",
                    appointer_name: "{{ __('messages.appointer_name_required') }}",
                    id_number: "{{ __('messages.id_number_required') }}",
                    appointer_mobile: "{{ __('messages.appointer_mobile_required') }}",
                    poa_type: "{{ __('messages.poa_type_required') }}",
                    name_of_authorized: "{{ __('messages.name_of_authorized_required') }}",
                    authorized_mobile: "{{ __('messages.authorized_mobile_required') }}",
                    id_number_authorized: "{{ __('messages.id_number_authorized_required') }}",
                    authorized_address: "{{ __('messages.authorized_address_required') }}",
                    relationship: "{{ __('messages.relationship_required') }}",

                    "authorized_passport[]": {
                        extension: "{{ __('messages.authorized_passport_mimes') }}",
                        fileSize: "{{ __('messages.authorized_passport_max') }}"
                    },
                    "appointer_id[]": {
                        required: "{{ __('messages.appointer_id_required') }}",
                        extension: "{{ __('messages.appointer_id_mimes') }}",
                        fileSize: "{{ __('messages.appointer_id_max') }}"
                    },
                    "authorized_id[]": {
                        required: "{{ __('messages.authorized_id_required') }}",
                        extension: "{{ __('messages.authorized_id_mimes') }}",
                        fileSize: "{{ __('messages.authorized_id_max') }}"
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