@extends('layouts.web_default', ['title' =>  $pageTitle ?? '' ])

@section('content')

    <div class="grid grid-cols-1 gap-6">
        <div class=" bg-white p-10 rounded-[20px] border !border-[#FFE9B1] ">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                @if($page == 'pending')
                    {{ __('frontend.pending_service') }}
                    @php 
                        $route = 'user.service.pending'; 
                        $detailsRoute = 'user.consultation.details'; 
                    @endphp
                @elseif ($page == 'history')
                    {{ __('frontend.service_history') }}
                    @php 
                        $route = 'user.service.history'; 
                        $detailsRoute = 'user.consultation.details'; 
                    @endphp
                @elseif ($page == 'payment')
                    {{ __('frontend.payment_history') }}
                    @php 
                        $route = 'user.service.payment'; 
                        $detailsRoute = 'user.consultation-payment.details'; 
                    @endphp
                @endif
            </h2>
            <hr class="mb-5">
            <div class="mb-6 border-b border-gray-200">
                <ul class="flex overflow-y-auto xl:overflow-auto -mb-px gap-3 md:gap-8  text-sm font-medium text-center" id="default-tab"
                    data-tabs-toggle="#default-tab-content" role="tablist">
                   
                    @foreach ($mainServices as $serv)
                        <li class="me-2  {{ (($page == 'pending' || $page == 'payment') && $serv['slug'] == 'online-live-consultancy') ? 'hidden' : ''  }}" role="presentation">
                            <a class="inline-block  text-[10px] leading-[13px] w-[140px] xl:w-auto xl:text-[14px] border-b-2 py-2.5 px-2 rounded-t-lg {{ $tab == $serv['slug'] ? 'bg-[#eadec7]' : '' }}" href="{{ route($route, ['tab' => $serv['slug']]) }}"
                            id="{{ $serv['slug'] }}" >{{ $serv['title'] }}</a>
                        </li>
                    @endforeach
    
                </ul>
            </div>

            <div id="default-tab-content">
                <div class=" rounded-lg " id="all-services" role="tabpanel" aria-labelledby="all-services-tab">
                    @if($consultations->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
                            @foreach($consultations as $request)
                            
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
                                    $paymentStatus = [
                                        'pending'   => '!bg-[#ea1616] !text-[#fff] dark:bg-gray-800 dark:text-gray-300',
                                        'success'   => '!bg-[#008000] !text-[#fff] dark:bg-green-900 dark:text-green-300',
                                        'failed'    => '!bg-[#ea1616] !text-[#fff] dark:bg-red-900 dark:text-red-300',
                                        'partial'   => '!bg-[#ffdb82] !text-[#000000] dark:bg-yellow-900 dark:text-yellow-300',
                                    ];
                                @endphp
                                <a href="{{ route($detailsRoute, ['id' => base64_encode($request->id)]) }}">
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                                            {{ __('frontend.consultation') }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-1">{{ __('frontend.application_reference_number') }} <span
                                                class="font-semibold">{{ $request->ref_code }}</span></p>
                                        <p class="text-sm text-gray-600 mb-4">{{ date('d M, Y h:i A', strtotime($request->created_at)) }}</p>
                                        
                                        @php
                                            $status = strtolower($request->status);
                                            $payStatus = strtolower($request->payment_status); 
                                            $bgColor = $statusClass[$status]['bg'] ?? '#e0e0e0';
                                            $textColor = $statusClass[$status]['text'] ?? '#000000';
                                        @endphp

                                        <span class=" text-xs font-medium px-4 py-1 rounded-full" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                            {{ ucfirst(__('frontend.'.$status)) }}
                                        </span>

                                        @if($payStatus != NULL)
                                            <span class="{{ $paymentStatus[$payStatus] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-4 py-1 rounded-full ml-2">
                                                @if ($payStatus == 'success')
                                                    {{ __('frontend.paid') }}
                                                @else
                                                    {{ __('frontend.un_paid') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">{{ __('frontend.no_data_found') }}</p>
                    @endif
                    <div class="mt-10">
                        {{ $consultations->links('pagination::tailwind') }}
                    </div>
                </div>
               
            </div>
        </div>
    </div>
@endsection

@section('ads')
    
    @php
        $ads = null;
    @endphp
    
    @if($page == 'pending')
        @php
            $ads = getActiveAd('pending_services', 'web');
        @endphp
    @elseif ($page == 'history')
        @php
            $ads = getActiveAd('service_history', 'web');
        @endphp
    @elseif ($page == 'payment')
        @php
            $ads = getActiveAd('payment_history', 'web');
        @endphp
    @endif

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