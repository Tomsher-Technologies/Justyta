@extends('layouts.web_default', ['title' => $response['details']['title'] ])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white p-10 rounded-[20px] border !border-[#FFE9B1] h-[calc(100vh-150px)]">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('frontend.apply_now') }}</h2>
        <hr class="mb-5">
        <form method="POST" action="{{ route('user.job.apply') }}" id="jobApplyForm" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
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
            class="bg-white p-10 rounded-[20px] border !border-[#FFE9B1] h-[calc(100vh-150px)] flex flex-col justify-between">
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
            $('#phone').on('input', function () {
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
            }, "File size must be less than {0}KB");

            $("#jobApplyForm").validate({
                ignore: [],
                rules: {
                    full_name: { required: true },
                    email: { required: true },
                    phone: { required: true },
                    position: { required: true },
                    "resume": {
                        required: true,
                        extension: "pdf,doc,docx",
                        fileSize: 2048
                    }
                },
                messages: {
                    full_name: "{{ __('messages.full_name_required') }}",
                    email: "{{ __('messages.email_required') }}",
                    phone: "{{ __('messages.phone_required') }}",
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