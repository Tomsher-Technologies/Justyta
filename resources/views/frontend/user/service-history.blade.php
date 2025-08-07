@extends('layouts.web_default', ['title' =>  $pageTitle ?? '' ])

@section('content')

    <div class="grid grid-cols-1 gap-6">
        <div class=" bg-white p-10 rounded-[20px] border !border-[#FFE9B1] ">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                @if($page == 'pending')
                    {{ __('frontend.pending_service') }}
                    @php 
                        $route = 'user.service.pending'; 
                        $detailsRoute = 'user.service.pending.details'; 
                    @endphp
                @elseif ($page == 'history')
                    {{ __('frontend.service_history') }}
                    @php 
                        $route = 'user.service.history'; 
                        $detailsRoute = 'user.service.history.details'; 
                    @endphp
                @elseif ($page == 'payment')
                    {{ __('frontend.payment_history') }}
                    @php 
                        $route = 'user.service.payment'; 
                        $detailsRoute = 'user.service.payment.details'; 
                    @endphp
                @endif
            </h2>
            <hr class="mb-5">
            <div class="mb-6 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px gap-8  text-sm font-medium text-center" id="default-tab"
                    data-tabs-toggle="#default-tab-content" role="tablist">
                   
                    @foreach ($mainServices as $serv)
                        <li class="me-2 {{ ($page == 'pending' && $serv['slug'] == 'online-live-consultancy') ? 'hidden' : ''  }}" role="presentation">
                            <a class="inline-block border-b-2 py-2.5 px-2 rounded-t-lg {{ $tab == $serv['slug'] ? 'bg-[#eadec7]' : '' }}" href="{{ route($route, ['tab' => $serv['slug']]) }}"
                            id="{{ $serv['slug'] }}" >{{ $serv['title'] }}</a>
                        </li>
                    @endforeach
    
                </ul>
            </div>

            <div id="default-tab-content">
                <div class=" rounded-lg " id="all-services" role="tabpanel" aria-labelledby="all-services-tab">
                    @if($serviceRequests->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
                            @foreach($serviceRequests as $request)
                            
                                @php
                                    $statusClass = [
                                        'pending'   => '!bg-[#bdbdbdb5] !text-[#444444] dark:bg-gray-800 dark:text-gray-300',
                                        'ongoing'   => '!bg-[#ffdb82] !text-[#000000] dark:bg-yellow-900 dark:text-yellow-300',
                                        'completed' => '!bg-[#42e1428c] !text-[#1B5E20] dark:bg-green-900 dark:text-green-300',
                                        'rejected'  => '!bg-[#fca6a6a1] !text-[#B71C1C] dark:bg-red-900 dark:text-red-300',
                                        
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
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $request->service->getTranslation('title',$lang) }}</h3>
                                        <p class="text-sm text-gray-600 mb-1">{{ __('frontend.application_reference_number') }} <span
                                                class="font-semibold">{{ $request->reference_code }}</span></p>
                                        <p class="text-sm text-gray-600 mb-4">{{ date('d M, Y h:i A', strtotime($request->submitted_at)) }}</p>
                                        
                                        @php
                                            $status = strtolower($request->status);
                                            $payStatus = strtolower($request->payment_status); 
                                        @endphp

                                        <span class="{{ $statusClass[$status] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-4 py-1 rounded-full">
                                            {{ ucfirst($status) }}
                                        </span>

                                        @if($payStatus != NULL)
                                            <span class="{{ $paymentStatus[$payStatus] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-4 py-1 rounded-full ml-2">
                                                @if ($payStatus == 'success')
                                                    {{ __('frontend.paid') }}
                                                @elseif ($payStatus == 'partial')
                                                    {{ __('frontend.partial') }}
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
                        <p class="text-center">{{ __('frontend.no_service_request') }}</p>
                    @endif
                    <div class="mt-10">
                        {{ $serviceRequests->links('pagination::tailwind') }}
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