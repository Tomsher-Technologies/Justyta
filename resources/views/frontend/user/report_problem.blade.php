@extends('layouts.web_default', ['title' => __('frontend.report_a_problem')])

@section('content')
<div class="grid grid-cols-1 gap-6">
    <div class="bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            {{ __('frontend.report_a_problem') }}
        </h2>
        <p class="text-gray-600 mb-8">
            {{ $pageData['content'] }}
        </p>
        <hr class="mb-5" />

        <form method="POST" id="reportProblem" action="{{ route('user.report.problem.submit') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="subject" class="block mb-2 text-sm font-medium text-gray-900">
                        {{ __('frontend.subject') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" id="subject"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                        placeholder="Enter subject" value="{{ old('subject') }}" required />
                    @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="row-span-4">
                    <label for="message" class="block mb-2 text-sm font-medium text-gray-900">
                        {{ __('frontend.message') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="message" rows="12"
                        class="block p-3.5 w-full text-sm text-gray-900 bg-gray-50 mb-1 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Please provide as much details as possible..."
                        required>{{ old('message') }}</textarea>
                    <span class="text-[#717171] text-sm">0/1000</span>
                    @error('message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="image" class="block mb-2 text-sm font-medium text-gray-900">
                        {{ __('frontend.attachment') }}
                    </label>
                    <input
                        class="file-input block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                        id="image" name="image" type="file" name="attachments"
                        data-preview="image-preview" />
                    <div id="image-preview" class="mt-2 grid grid-cols-4 gap-2"></div>
                    @error('attachments') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">
                        {{ __('frontend.email') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                        placeholder="youremail@gmail.com" value="{{ old('email', Auth::guard('frontend')->user()->email) }}" required />
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit"
                class="uppercase text-white !bg-[#04502E] hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-12 py-3 text-center">
                {{ __('frontend.submit') }}
            </button>
        </form>

    </div>
</div>
@endsection

@section('ads')
    @php
        $ads = getActiveAd('report_problem', 'web');
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

        $("#reportProblem").validate({
            ignore: [],
            rules: {
                email: { required: true },
                subject: { required: true },
                message: { required: true },
                "image": {
                    extension: "pdf,jpg,jpeg,webp,png,svg,doc,docx",
                    fileSize: 500
                }
            },
            messages: {
                email: "{{ __('messages.email_required') }}",
                subject: "{{ __('messages.enter_subject') }}",
                message: "{{ __('messages.enter_message') }}",
        
                "image": {
                    extension: "{{ __('messages.image_file_mimes') }}",
                    fileSize: "{{ __('messages.image_file_max') }}"
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