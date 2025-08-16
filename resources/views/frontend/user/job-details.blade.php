@extends('layouts.web_default', ['title' => $jobPost['title'] ])

@section('content')
<div class="grid grid-cols-1 gap-6">
    <div class=" bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-medium mb-2">{{ $jobPost['title'] }}</h2>
                <span
                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full uppercase">{{ $jobPost['type'] }}</span>
            </div>
            <div>
                @if (!$hasApplied)
                     <a  href="{{ route('user.job.details.apply',['id' => base64_encode($jobPost['id'])]) }}"
                    class="inline-flex items-center px-4 py-2 text-white bg-[#07683B] hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-full text-base dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                        {{ __('frontend.apply_now') }}
                        <svg class="w-4 h-4 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 5h12m0 0L9 1m4 4L9 9" />
                        </svg>
                    </a>
                @else
                    <div class="inline-flex items-center px-4 py-2 text-white bg-gray-400 cursor-not-allowed rounded-full text-base">
                        {{ __('frontend.already_applied') ?? 'Already Applied' }}
                    </div>
                @endif
               
                <a href="{{ Session::has('jobs_last_url') ? Session::get('jobs_last_url') : route('user-lawfirm-jobs') }}"
                    class="inline-flex items-center px-4 py-2 text-black bg-[#c4b07e] hover:bg-[#c4b07e]-800 focus:ring-4 focus:ring-green-300 font-medium rounded-full text-base dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                    {{ __('frontend.go_back') }}
                    <svg class="w-4 h-4 ms-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10" aria-hidden="true">
                        <path stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5H1m0 0l4-4M1 5l4 4" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-2/3  break-words">
                <h2 class="text-xl font-semibold mb-3">{{ __('frontend.job_description') }}</h2>
                {!! $jobPost['description'] !!}
            </div>

            <div class="lg:w-1/3 space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6 grid grid-cols-2 gap-5 items-center">
                    <div class="mb-2">
                        <p class="text-gray-600 font-medium">{{ __('frontend.salary_range') }}</p>
                        <p class="text-xl font-medium text-[#07683B] my-2 break-words">{{ $jobPost['salary'] }}</p>
                        {{-- <p class="text-sm text-gray-500">Yearly salary</p> --}}
                    </div>


                    <div class="mb-2 text-center">
                        <svg class="text-center m-auto" xmlns="http://www.w3.org/2000/svg" width="39" height="39"
                            viewBox="0 0 39 39" fill="none">
                            <path d="M14.832 27.3691L5.33203 29.7441V8.36914L14.832 5.99414" stroke="#B9A572"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M24.332 32.1191L14.832 27.3691V5.99414L24.332 10.7441V32.1191Z" stroke="#B9A572"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M24.332 10.7441L33.832 8.36914V29.7441L24.332 32.1191" stroke="#B9A572"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="text-gray-600 font-semibold my-2 text-[16px] text-black">{{ __('frontend.job_location') }}</p>
                        <p class="text-xl mb-0 font-normal text-[#767F8C] break-words">{{ $jobPost['location'] }}</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <h3 class="text-lg font-medium mb-4">{{ __('frontend.job_overview') }}</h3>
                    <div class="grid grid-cols-3 gap-4 text-gray-700 text-sm">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33"
                                fill="none">
                                <path
                                    d="M26.582 5.05664H6.58203C6.02975 5.05664 5.58203 5.50436 5.58203 6.05664V26.0566C5.58203 26.6089 6.02975 27.0566 6.58203 27.0566H26.582C27.1343 27.0566 27.582 26.6089 27.582 26.0566V6.05664C27.582 5.50436 27.1343 5.05664 26.582 5.05664Z"
                                    stroke="#B9A572" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M22.582 3.05664V7.05664" stroke="#B9A572" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.582 3.05664V7.05664" stroke="#B9A572" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M5.58203 11.0566H27.582" stroke="#B9A572" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <p class="text-[#767F8C] my-1 uppercase">{{ __('frontend.job_posted') }}</p>
                            <p class="text-[#18191C] text-[15px] font-medium">{{ ($jobPost['job_posted_date']) ? date('d M, Y', strtotime($jobPost['job_posted_date'])) : '' }}</p>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33"
                                fill="none">
                                <path
                                    d="M16.248 27.0566C22.3232 27.0566 27.248 22.1318 27.248 16.0566C27.248 9.98151 22.3232 5.05664 16.248 5.05664C10.1729 5.05664 5.24805 9.98151 5.24805 16.0566C5.24805 22.1318 10.1729 27.0566 16.248 27.0566Z"
                                    stroke="#B9A572" stroke-width="2" stroke-miterlimit="10" />
                                <path d="M16.248 16.0562L21.1978 11.1064" stroke="#B9A572" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M13.248 1.05664H19.248" stroke="#B9A572" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="text-[#767F8C] my-1 uppercase">{{ __('frontend.apply_deadline') }}</p>
                            <p class="text-[#18191C] text-[15px] font-medium">
                                {{ ($jobPost['deadline_date']) ? date('d M, Y', strtotime($jobPost['deadline_date'])) : '' }}
                            </p>
                        </div>
                        
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33"
                                fill="none">
                                <g clip-path="url(#clip0_315_5538)">
                                    <path
                                        d="M27.582 9.05664H5.58203C5.02975 9.05664 4.58203 9.50436 4.58203 10.0566V26.0566C4.58203 26.6089 5.02975 27.0566 5.58203 27.0566H27.582C28.1343 27.0566 28.582 26.6089 28.582 26.0566V10.0566C28.582 9.50436 28.1343 9.05664 27.582 9.05664Z"
                                        stroke="#B9A572" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M21.582 9.05664V7.05664C21.582 6.52621 21.3713 6.0175 20.9962 5.64243C20.6212 5.26735 20.1125 5.05664 19.582 5.05664H13.582C13.0516 5.05664 12.5429 5.26735 12.1678 5.64243C11.7927 6.0175 11.582 6.52621 11.582 7.05664V9.05664"
                                        stroke="#B9A572" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M28.5822 15.8457C24.9351 17.9558 20.7948 19.0637 16.5812 19.0571C12.3684 19.0637 8.22873 17.9561 4.58203 15.8468"
                                        stroke="#B9A572" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M15.082 15.0566H18.082" stroke="#B9A572" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_315_5538">
                                        <rect width="32" height="32" fill="white"
                                            transform="translate(0.582031 0.0566406)" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <p class="text-[#767F8C] my-1 uppercase">{{ __('frontend.job_type') }}</p>
                            <p class="text-[#18191C] text-[15px] font-medium uppercase">{{ $jobPost['type'] }}</p>
                        </div>
                    </div>
                </div>
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