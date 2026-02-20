@php

    $device = request()->header('User-Agent') && preg_match('/Mobile|Android|iPhone/i', request()->header('User-Agent'))
        ? 'mobile'
        : 'web';
    $ads = getActiveAd($page, $device ?? 'web');
@endphp
 
@if ($ads && $ads->files->isNotEmpty()) 
    <div class="relative w-full mb-12 px-[50px]">
        @php
            $file = $ads->files->first();
        @endphp

        <a href="{{ $ads->cta_url ?? '#' }}" target="_blank" title="{{ $ads->cta_text ?? 'View More' }}">
            @if($file->file_type === 'video')
                <video id="adVideo{{ $ads->id }}" class="w-full object-fill ad-video" autoplay muted loop playsinline>
                    <source src="{{ asset($file->file_path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @else
                <img src="{{ asset($file->file_path) }}" class="w-full ad-video object-fill" alt="Ad Image">
            @endif
        </a>

        @if($file->file_type === 'video')
            <button 
                onclick="toggleMute('adVideo{{ $ads->id }}', this)" 
                class="absolute bottom-2 bg-gray-800 bg-opacity-50 text-white px-2 py-1 rounded hover:bg-opacity-80 z-10" style="right: 4rem;">
                <!-- Unmute Icon -->
                {{-- <svg id="unmuteIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9v6h4l5 5V4l-5 5H9" />
                </svg>

                <!-- Mute Icon -->
                <svg id="muteIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white-500 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9v6h4l5 5V4l-5 5H9" />
                    <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2"/>
                </svg> --}}

                <svg id="unmuteIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5L6 9H3v6h3l5 4V5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.54 8.46a5 5 0 010 7.07"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.07 4.93a9 9 0 010 14.14"/>
                </svg>

                <svg id="muteIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 5L6 9H3v6h3l5 4V5z"/>
                    <line x1="16" y1="10" x2="21" y2="15"/>
                    <line x1="16" y1="15" x2="21" y2="10"/>
                </svg>
            </button>
        @endif
    </div>
@endif