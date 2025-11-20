@extends('layouts.web_default', ['title' =>  __('frontend.law_firm_jobs') ])

@section('content')
<div class="grid grid-cols-1 gap-6">
    <div class=" bg-white p-10 rounded-[20px] border !border-[#FFE9B1] h-[calc(100vh-150px)]">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-xl font-semibold text-gray-800">{{ __('frontend.law_firm_jobs') }}</h1>

            <form class="w-[80%]">
                <label for="default-search"
                    class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">{{ __('frontend.search') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-2 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" id="default-search" name="keyword" value="{{ request()->keyword }}"
                        class="block w-full p-4 ps-12 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 "
                        placeholder="{{ __('frontend.search_job_title_position') }}" required />
                    <button type="submit"
                        class="text-white absolute end-22.5 bottom-2.5 bg-[#07683B] hover:bg-[#07683B]-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 cursor-pointer">
                        {{ __('frontend.find_jobs') }}
                    </button>

                    <a href="{{ route('user-lawfirm-jobs') }}"
                        class="text-black absolute end-2.5 bottom-2.5 bg-[#c4b07e] hover:bg-[#c4b07e]-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 ">
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