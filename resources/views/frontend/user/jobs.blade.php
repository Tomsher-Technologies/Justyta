@extends('layouts.web_default', ['title' =>  __('frontend.law_firm_jobs') ])

@section('content')
<div class="grid grid-cols-1 gap-6">
    <div class=" bg-white p-4 xl:p-10 rounded-[20px] border !border-[#FFE9B1] h-[calc(100vh-150px)]">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-xl font-semibold text-gray-800">{{ __('frontend.law_firm_jobs') }}</h1>

            <form class="w-[80%]">
                <div class="flex items-center gap-3">
                    
                    <!-- Search Box -->
                    <div class="flex-1 relative">
                        <input type="search" id="default-search" name="keyword" 
                            value="{{ request()->keyword }}"
                            class="block w-full p-4 ps-12 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ __('frontend.search_job_title') }}" required />

                        <div class="absolute inset-y-0 start-2 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <button type="submit"
                        class="text-white bg-[#07683B] hover:bg-green-700 focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-4 py-2">
                        {{ __('frontend.find_jobs') }}
                    </button>

                    <!-- Reset Button -->
                    <a href="{{ route('user-lawfirm-jobs') }}"
                        class="text-black bg-[#c4b07e] hover:bg-[#a8956b] focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-4 py-2">
                        {{ __('frontend.reset') }}
                    </a>

                </div>
            </form>


        </div>

        @if(!empty($jobPosts[0]))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($jobPosts as $job)
                    <a href="{{ route('user.job.details',['id' => base64_encode($job->id)]) }}">
                        <div class="bg-yellow-50 bg-opacity-50 rounded-lg p-6 shadow-sm border border-yellow-100">
                            <h3 class="text-lg font-medium mb-2">{{ $job->getTranslation('title',$lang) ?? NULL }}</h3>
                            <span
                                class="bg-[#E7F6EA] text-[#0BA02C] text-xs font-medium me-2 px-2.5 py-0.5 rounded-full uppercase">{{ __('messages.'.$job->type) }}</span>
                            <div class="flex items-center mt-3 gap-1 text-[#767F8C] text-sm">
                                <svg class="w-6 h-6 text-[#767F8C]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z" />
                                </svg>
                                {{ $job->location->getTranslation('name', $lang) ?? NULL }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="pagination mt-4">
                {{ $jobPosts->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center w-full mt-10">
                <span class="text-lg">{{ __('frontend.no_jobs_found') }}</span>
            </div>
        @endif
        
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
                    🔇
                </button>
            @endif
        </div>
    @endif
@endsection