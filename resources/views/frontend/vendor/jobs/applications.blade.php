@extends('layouts.web_vendor_default', ['title' => __('frontend.applications')])

@section('content')

<div class="bg-white rounded-lg p-6">
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-medium text-gray-900">{{ __('frontend.applications') }} ({{ $jobPost->getTranslation('title',$lang) }})
            </h2>
        </div>
        <div>
            <a href="{{ Session::has('jobs_last_url') ? Session::get('jobs_last_url') : route('jobs.index') }}"
                class="inline-flex items-center px-4 py-2 text-black bg-[#c4b07e] hover:bg-[#c4b07e]-800 focus:ring-4 focus:ring-green-300 font-medium rounded-full text-base dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                {{ __('frontend.go_back') }}
                <svg class="w-4 h-4 ms-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10" aria-hidden="true">
                    <path stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5H1m0 0l4-4M1 5l4 4" />
                </svg>
            </a>
        </div>
    </div>
    
    <hr class="my-4 border-[#DFDFDF]">

    <div class="relative overflow-x-auto sm:rounded-lg">
        <table class="w-full border">
            <thead class="text-md font-normal">
                <tr class="bg-[#07683B] text-white font-normal">
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.sl_no') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.full_name') }}</th>
                    <th class="px-6 py-5 font-semibold text-center" >{{ __('frontend.email') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.phone') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.current_position') }}</th>
                    <th class="px-6 py-5 font-semibold text-start">{{ __('frontend.cv') }}</th>
                </tr>
            </thead>
            <tbody class="text-[#4D4D4D]">
                @forelse ($applications as $key => $application)
                    <tr class="border-b even:bg-[#EEF4F1]">
                        <td class="px-6 py-4  text-center">
                            {{ $key + 1 + ($applications->currentPage() - 1) * $applications->perPage() }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $application->full_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $application->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $application->phone ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $application->currentPostion?->getTranslatedName('title', app()->getLocale()) ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($application->resume_path != null)
                                <a href="{{ asset($application->resume_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ basename($application->resume_path) }}">
                                    </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('frontend.no_data_found') }}</td>
                    </tr>
                @endforelse
                

            </tbody>
        </table>

        <div class="mt-6">
            {{ $applications->appends(request()->input())->links() }}
        </div>
        
    </div>
</div>




@endsection