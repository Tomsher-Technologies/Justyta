@extends('layouts.web_default', ['title' => $response['details']['title'] ])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white p-4 xl:p-10 rounded-[20px] border !border-[#FFE9B1] h-[calc(100vh-150px)]">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('frontend.apply_now') }}</h2>
        <hr class="mb-5">
        <form method="POST" action="{{ route('user.job.apply') }}" id="jobApplyForm" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                <div>
                    <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.full_name') }}<span
                            class="text-red-500">*</span></label>
                    <input type="text" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" name="full_name" value="{{ old('full_name', $user->name) }}"
                        placeholder="{{ __('frontend.enter') }}">
                    <input type="hidden" name="job_id" value="{{ base64_encode($response['details']['job_id']) }}">

                    @error('full_name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="you-represent" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span class="text-red-500">*</span></label>
                    <input type="email" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" name="email"  value="{{ old('email', $user->email) }}"
                        placeholder="{{ __('frontend.enter') }}">

                    @error('email')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="case-stage" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.phone') }}<span
                            class="text-red-500">*</span></label>
                    <input type="text"
                        class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" name="phone" id="phone"  value="{{ old('phone', $user->phone) }}"
                        placeholder="{{ __('frontend.enter') }}">

                    @error('phone')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="preferred-language" class="block text-sm font-medium text-gray-700 mb-2" >{{ __('frontend.current_position') }}<span class="text-red-500">*</span></label>
                    <select id="preferred-language" name="position"
                        class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option value="">{{ __('frontend.choose_option') }}</option>
                        @foreach ($response['job_positions'] as $ert)
                            <option value="{{ $ert['id'] }}"  {{ (old('position') == $ert['id']) ? 'selected' : '' }}>{{ $ert['value'] }}</option>
                        @endforeach
                    </select>

                    @error('position')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="consultation-time" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.upload_cv') }}<span class="text-red-500">*</span></label>
                    <input  class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" name="resume" id="file_input" type="file" data-preview="resume-preview">
                    <div id="resume-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                    @error('resume')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
               
            </div>
            <div >
                <button type="submit" class="uppercase text-white !bg-[#04502E] hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-12 py-3 text-center">{{ __('frontend.apply_now') }}</button>

                <a href="{{ Session::has('job_details_last_url') ? Session::get('job_details_last_url') : route('user-lawfirm-jobs') }}"
                    class="uppercase text-sm text-black px-12 py-3 text-center bg-[#c4b07e] font-medium rounded-lg ">
                    {{ __('frontend.cancel') }}
                </a>
            </div>
        </form>
    </div>
    <div class="lg:col-span-1 space-y-6">
        <div
            class="bg-white p-6 xl:p-10 rounded-[20px] border !border-[#FFE9B1] h-[auto] xl:h-[calc(100vh-150px)] flex flex-col justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ __('frontend.about_consultancy') }}</h2>
                <hr class="my-4">

                <h3 class="text-[#B9A572] font-medium text-[20px] mb-3">{{ $response['details']['lawfirm_name'] }}</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                   {!! $response['details']['about'] !!}
                </p>


                <h5 class="font-medium mt-4">{{ __('frontend.contact') }}</h5>
                <ul class="text-sm flex gap-1.5 flex-col mt-2">
                    <li>
                        {{ __('frontend.email') }}: <a href="mailto:{{ $response['details']['email'] }}">{{ $response['details']['email'] }}</a>
                    </li>
                    <li>
                        {{ __('frontend.phone') }}: <a href="tel:{{ $response['details']['phone'] }}">{{ $response['details']['phone'] }}</a>
                    </li>
                    <li>
                        {{ __('frontend.location') }}:{{ $response['details']['location'] }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('ads')
    @php
        $ads = getActiveAd('lawfirm_jobs', 'web');
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
            $('#phone').on('input', function () {
                this.value = this.value.replace(/[^0-9+]/g, '');
            });
 
            $.validator.addMethod("fileSize", function(value, element, param) {
                if (value === "") return true;
                if (element.files.length === 0) return false; 
                return element.files[0].size <= param;
            }, "File size must be less than {0} bytes.");

            $("#jobApplyForm").validate({
                ignore: [],
                rules: {
                    full_name: { required: true, maxlength: 100 },
                    email: { required: true },
                    phone: { required: true, maxlength: 15 },
                    position: { required: true },
                    "resume": {
                        required: true,
                        extension: "pdf,doc,docx",
                        fileSize: 104857600
                    }
                },
                messages: {
                    full_name: {
                        required: "{{ __('messages.full_name_required') }}",
                        maxlength: "{{ __('frontend.maxlength100') }}"
                    },
                    email: "{{ __('messages.email_required') }}",
                    phone: {
                        required: "{{ __('messages.phone_required') }}",
                        maxlength: "{{ __('frontend.maxlength15') }}"
                    },
                    position: "{{ __('messages.position_required') }}",
                    "resume": {
                        required: "{{ __('messages.resume_required') }}",
                        extension: "{{ __('messages.resume_mimes') }}",
                        fileSize: "{{ __('messages.resume_max') }}"
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