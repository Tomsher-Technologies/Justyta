@extends('layouts.web_default', ['title' => __('frontend.law_training_request')])

@section('content')
<div class="grid grid-cols-1 gap-6">
    <div class=" bg-white p-10 rounded-[20px] border !border-[#FFE9B1] ">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('frontend.law_training_request') }}</h2>
        <hr class="mb-5">

        <form method="POST" id="trainingRequest" action="{{ route('user-training-training-submit') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 mb-6">

                <div>
                    <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirate')
                        }}<span class="text-red-500">*</span></label>
                    <select id="emirate" name="emirate_id"
                        class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option value="">{{ __('frontend.select_emirate') }}</option>
                        @foreach ($response['emirates'] as $emirate)
                        <option value="{{ $emirate['id'] }}" {{ old('emirate_id')==$emirate['id'] ? 'selected' : '' }}>
                            {{ $emirate['value'] }}</option>
                        @endforeach
                    </select>
                    @error('emirate_id')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.position') }}<span
                            class="text-red-500">*</span></label>
                    <select id="position" name="position" class="select2 bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option value="">{{ __('frontend.choose_option') }}</option>
                        @foreach ($response['training_positions'] as $ert)
                            <option value="{{ $ert['id'] }}"  {{ (old('current_position') == $ert['id']) ? 'selected' : '' }}>{{ $ert['value'] }}</option>
                        @endforeach
                    </select>
                    @error('position')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="case-stage" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.start_date') }}<span
                            class="text-red-500">*</span></label>
                    <input type="date" id="start_date" name="start_date"
                        class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                        placeholder="DD-MM-YYYY">
                    @error('start_date')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label for="residency_status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.residency_status') }}<span class="text-red-500">*</span></label>
                    <select id="residency_status" data-url="{{ url('user/get-sub-contract-types') }}" name="residency_status" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option value="">{{ __('frontend.choose_option') }}</option>
                        @foreach ($response['residency_status'] as $res_status)
                            <option value="{{ $res_status['id'] }}"  {{ (old('residency_status') == $res_status['id']) ? 'selected' : '' }}>{{ $res_status['value'] }}</option>
                        @endforeach
                    </select>
                    @error('residency_status')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <hr class="col-span-2 my-6">

            <h2 class="text-xl font-medium text-[#07683B] mb-4">{{ __('frontend.upload_documents') }}</h2>

            <div class="grid grid-cols-1 gap-x-6 gap-6">
               
                <div>
                    <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('frontend.files') }}
                        {{-- <span class="text-red-500">*</span> --}}
                    </label>
                    <input class="file-input block text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none w-125" id="documents" type="file" name="documents[]" multiple  data-preview="documents-preview"/>
                    <div id="documents-preview" class="mt-2 grid grid-cols-10 gap-2"></div>
                    @error('documents')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="self-end">
                <button type="submit" class="text-white bg-[#04502E] hover:bg-[#02331D] focus:ring-4 focus:ring-blue-300 font-normal rounded-xl text-md w-75 px-8 py-4 text-center transition-colors duration-200 mt-6">{{ __('frontend.submit') }}</button>
            </div>

            
        </form>
    </div>
</div>
@endsection

@section('ads')
    @php
        $ads = getActiveAd('training_requests', 'web');
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

            $("#trainingRequest").validate({
                ignore: [],
                rules: {
                    emirate_id: { required: true },
                    position: { required: true },
                    start_date: { required: true },
                    residency_status: { required: true },
                    "documents[]": {
                        extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                        fileSize: 1024
                    },
                },
                messages: {
                    emirate_id: "{{ __('messages.emirate_required') }}",
                    position: "{{ __('messages.position_required') }}",
                    start_date: "{{ __('messages.start_date_required') }}",
                    residency_status: "{{ __('messages.residency_status_required') }}",
                    "documents[]": {
                        extension: "{{ __('messages.document_file_mimes') }}",
                        fileSize: "{{ __('messages.document_file_max') }}"
                    },
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