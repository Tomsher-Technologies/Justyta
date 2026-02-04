@extends('layouts.web_vendor_default', ['title' => __('frontend.consultations')])

@section('content')
<div class="bg-white rounded-lg p-6 min-h-[calc(100vh-150px)]">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-medium text-gray-900">{{ __('frontend.consultations') }}</h2>
        <a href="{{ Session::has('last_page_consultations') ? Session::get('last_page_consultations') : route('vendor.consultations.index') }}" class="inline-flex items-center mt-3 xl:mt-0 px-4 py-2 text-white bg-[#c4b07e] hover:bg-[#c4b07e]-800 focus:ring-4 focus:ring-green-300 font-medium rounded-full text-base dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
            <svg class="w-4 h-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10"
                aria-hidden="true">
                <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 5H1m0 0l4-4M1 5l4 4" />
            </svg>
        {{ __('frontend.go_back') }}
            
        </a>
    </div>

    <hr class="my-4 border-[#DFDFDF]" />
    
    <div class="relative overflow-x-auto sm:rounded-lg w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Left: Consultation Details -->
            <div class="col-span-1 border-r pr-8">
                <div class="space-y-6">
                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.ref_no') }} :</p>
                        <p class="text-gray-800">{{ $consultation->ref_code ?? '-' }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.status') }} :</p>
                        @php
                            $statusClass = [
                                'reserved' => ['bg' => '#808080', 'text' => '#ffffff'],
                                'waiting_lawyer' => ['bg' => '#ADD8E6', 'text' => '#000000'],
                                // 'assigned' => ['bg' => '#FFF44F', 'text' => '#000000'],
                                'accepted' => ['bg' => '#90EE90', 'text' => '#000000'],
                                'rejected' => ['bg' => '#FF0000', 'text' => '#ffffff'],
                                'completed' => ['bg' => '#008000', 'text' => '#ffffff'],
                                'cancelled' => ['bg' => '#A52A2A', 'text' => '#ffffff'],
                                'no_lawyer_available' => ['bg' => '#FFA07A', 'text' => '#000000'],
                                'in_progress' => ['bg' => '#FFD580', 'text' => '#000000'],
                                'on_hold' => ['bg' => '#FFA500', 'text' => '#000000'],
                            ];

                            $status = $assignment->status ?? '';
                            $bgColor = $statusClass[$status]['bg'] ?? '#e0e0e0';
                            $textColor = $statusClass[$status]['text'] ?? '#000000';
                        @endphp
                        <p class="text-gray-800">
                            <span class="px-3 py-1 rounded text-sm font-medium"
                                style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                {{ ucwords(str_replace('_', ' ', __('frontend.'.$status))) }}
                            </span>
                        </p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.client_name') }} :</p>
                        <p class="text-gray-800">{{ $consultation->user?->name ?? '-' }}</p>
                    </div>

                    {{-- @if($assignment->status == 'accepted') --}}
                        <div class="flex items-center">
                            <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.lawyer') }} :</p>
                            <p class="text-gray-800">{{ $assignment->lawyer?->getTranslation('full_name', getActiveLanguage()) ?? '-' }}</p>
                        </div>
                    {{-- @endif --}}
                    

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.applicant_type') }} :</p>
                        <p class="text-gray-800">{{ ucfirst(__('frontend.'.$consultation->applicant_type) ?? '-') }}</p>
                    </div>

                     <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.litigation_type') }} :</p>
                        <p class="text-gray-800">{{ ucfirst(__('frontend.'.$consultation->litigation_type) ?? '-') }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.consultant_type') }} :</p>
                        <p class="text-gray-800">{{ ucfirst(__('frontend.'.$consultation->consultant_type) ?? '-') }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.case_type') }} :</p>
                        <p class="text-gray-800">{{ $consultation->caseType?->getTranslation('name', getActiveLanguage()) ?? '-' }}</p>
                    </div>

                    
                </div>
            </div>
            <div class="col-span-1 border-r pr-8">
                <div class="space-y-6">
                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.case_stage') }} :</p>
                        <p class="text-gray-800">{{ $consultation->caseStage?->getTranslation('name', getActiveLanguage()) ?? '-' }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.you_represent') }} :</p>
                        <p class="text-gray-800">{{ $consultation->youRepresent?->getTranslation('name', getActiveLanguage()) ?? '-' }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.language') }} :</p>
                        <p class="text-gray-800">{{ ucfirst($consultation->languageValue?->getTranslation('name', getActiveLanguage()) ?? '-') }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.emirate') }} :</p>
                        <p class="text-gray-800">{{ $consultation->emirate?->getTranslation('name', getActiveLanguage()) ?? '-' }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.duration') }} :</p>
                        <p class="text-gray-800">{{ $consultation->duration }} {{ __('frontend.mins') }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.amount') }} :</p>
                        <p class="text-gray-800">
                            @if($assignment->status == 'accepted')
                                AED {{ number_format($consultation->lawyer_amount, 2) }}
                            @else
                                AED 0.00
                            @endif
                            
                        </p>
                    </div>

                    @if($assignment->status == 'accepted')
                        <div class="flex items-center">
                            <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.meeting_start_time') }} :</p>
                            <p class="text-gray-800">{{ $consultation->meeting_start_time ?? '-' }}</p>
                        </div>

                        <div class="flex items-center">
                            <p class="basis-1/2 text-gray-600 font-medium">{{ __('frontend.meeting_end_time') }} :</p>
                            <p class="text-gray-800">{{ $consultation->meeting_end_time ?? '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
    <style>
       
    </style>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            

        });

    </script>
@endsection