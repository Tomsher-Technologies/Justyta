@extends('layouts.web_translator', ['title' => 'Service Request Details'])

@section('content')
    <div class="bg-white rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-medium text-gray-900 mb-4">
                {{ __('frontend.recent_consultations') }}
            </h2>
            <button class="bg-green-700 hover:bg-green-800 text-white font-medium rounded-full px-8 py-2 transition"
                data-modal-target="default-modal" data-modal-toggle="default-modal">
                {{ __('frontend.update_status') }}
            </button>
        </div>

        <hr class="my-4 border-[#DFDFDF]" />

        <div class="grid grid-cols-2 gap-8">
            <div class="space-y-6 text-[18px]">
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.ref_no') }} :</span>
                    <span class="font-medium">{{ $details['reference_code'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.document_title') }} :</span>
                    <span class="font-medium">{{ $details['document_title'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.type_of_document') }} :</span>
                    <span class="font-medium">{{ $details['document_type'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.sub_document_type') }} :</span>
                    <span class="font-medium">{{ $details['sub_document_type'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.no_of_page') }} :</span>
                    <span class="font-medium">{{ $details['no_of_pages'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.date_time') }} :</span>
                    <span class="font-medium">{{ $details['created_at'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.document_language') }} :</span>
                    <span class="font-medium">{{ $details['document_language'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.translation_language') }} :</span>
                    <span class="font-medium">{{ $details['translation_language'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.priority_level') }} :</span>
                    <span class="font-medium">{{ ucfirst($details['priority']) }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.receive_by') }} :</span>
                    <span class="font-medium">{{ $details['receive_by'] }}</span>
                </div>
            </div>

            <div class="pl-8 border-l border-[#DFDFDF] flex flex-col justify-between">
                <div>
                    <div>
                        <span class="font-semibold text-[#23222B]">{{ __('frontend.status') }}</span>
                        <div class="mt-2 mb-6">
                            <span
                                class="bg-[#EDE5CF] text-[#B9A572] rounded-full px-6 py-2 font-medium text-base">{{ ucfirst($details['status']) }}</span>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <h3 class="mb-3 font-medium">{{ __('frontend.uploaded_documents') }}:</h3>
                        <div class="space-y-4">
                            @foreach ($details['service_details'] as $key => $files)
                                @if (is_array($files) && !empty($files))
                                    <div>
                                        <p class="text-gray-600 font-medium mb-2">{{ __('frontend.' . $key) }} :
                                        </p>
                                        <div class="flex flex-wrap gap-3">
                                            @foreach ($files as $index => $file)
                                                @php $isImage = Str::endsWith($file, ['.png', '.jpg', '.jpeg', '.webp']); @endphp

                                                @if ($isImage)
                                                    <a data-fancybox="gallery" href="{{ $file }}">
                                                        <img src="{{ $file }}"
                                                            class="h-28 object-cover rounded-lg border border-gray-300 hover:opacity-75"
                                                            alt="">
                                                    </a>
                                                @else
                                                    <a href="{{ $file }}" data-fancybox="gallery">
                                                        <img src="{{ asset('assets/images/file.png') }}"
                                                            class="h-28 object-cover rounded-lg border border-gray-300 hover:opacity-75"
                                                            alt="">
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    @php $timeline = $details['timeline'] ?? []; @endphp

                    <ol class="relative border-l-2 border-[#DFDFDF] ml-3 mt-8">
                        @foreach ($timeline as $step)
                            @php
                                $isCompleted = $step['completed'];
                                $dotClasses = $isCompleted
                                    ? 'bg-[#EDE5CF] border-[#C7B07A]'
                                    : 'bg-[#DFDFDF] border-[#DFDFDF]';
                                $textClasses = $isCompleted ? 'text-[#B9A572]' : 'text-[#C7C7C7]';
                            @endphp

                            <li class="mb-8 ml-6">
                                <span
                                    class="absolute -left-3 flex items-center justify-center w-4 h-4 rounded-full border-2 {{ $dotClasses }}"></span>
                                <h3 class="font-semibold {{ $textClasses }} text-lg">
                                    {{ $step['label'] }}
                                </h3>
                                @if ($isCompleted && !empty($step['date']))
                                    <time class="block text-sm text-[#B9A572]">{{ $step['date'] }}</time>
                                @endif
                            </li>
                        @endforeach
                    </ol>

                </div>
                <div class="flex justify-end mt-16">
                    <button class="bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-10 py-3 transition">
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
