@extends('layouts.web_vendor_default', ['title' => $lawyer->getTranslation('full_name', app()->getLocale()) ?? __('frontend.lawyer')])

@section('content')
    <div class="bg-white rounded-2xl  p-8 pb-12">

        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-semibold text-gray-800">{{ __('frontend.lawyer_profile') }}</h2>
            <span class="text-gray-600 text-sm"> <b>{{ __('frontend.last_login') }} :</b>
                {{ $lawyer->user?->last_login_at != null ? date('d M Y, h:i A', strtotime($lawyer->user?->last_login_at)) : '' }}</span>
            <div class="flex">
                <a href="{{ route('vendor.edit.lawyers', base64_encode($lawyer->id)) }}"
                    class=" flex text-white bg-[#07683B] rounded-full py-2.5 px-6"
                    title="{{ __('frontend.update_lawyer_information') }}">
                
                    {{ __('frontend.edit') }}
                </a>

                <a href="{{ Session::has('lawyers_last_url') ? Session::get('lawyers_last_url') : route('vendor.lawyers') }}" class="ml-2 flex text-black bg-[#ccb478] rounded-full px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-6 text-gray-700 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18" />
                    </svg>
                    {{ __('frontend.go_back') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-200 pb-8 mb-8">
            <div class="flex items-center gap-6 col-span-4">
                <img class="w-24 h-24 rounded-full object-cover shadow-md"
                    src="{{ asset(getUploadedUserImage($lawyer->profile_photo)) }}"
                    alt="{{ $lawyer->getTranslation('full_name', app()->getLocale()) }}">
                <div>
                    <h2 class="text-2xl font-medium text-gray-900 flex items-center gap-2">
                        {{ $lawyer->getTranslation('full_name', app()->getLocale()) }}
                        @if($lawyer->user->banned == 1)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                {{ __('frontend.inactive') }}
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ __('frontend.active') }}
                            </span>
                        @endif
                    </h2>
                    <p class="text-base text-gray-500">{{ $lawyer->ref_no }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 col-span-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 flex flex-col items-start justify-center">
                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('frontend.total_hours_logged') }}</p>
                    <p class="text-2xl font-bold text-[#B9A572]">0 <span
                            class="text-xl font-semibold">{{ __('frontend.hours') }}</span>
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 flex flex-col items-start justify-center">
                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('frontend.total_cases') }}</p>
                    <p class="text-2xl font-bold text-[#B9A572]">0</p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 flex flex-col items-start justify-center">
                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('frontend.cases_closed') }}</p>
                    <p class="text-2xl font-bold text-[#B9A572]">0</p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 flex flex-col items-start justify-center">
                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('frontend.cases_rejected') }}</p>
                    <p class="text-2xl font-bold text-[#B9A572]">0</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <div class="border-r">
                <h3 class="text-xl font-medium text-[#07683B] mb-6">{{ __('frontend.profile_information') }}</h3>
                <div class="space-y-6">
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.gender') }} :</p>
                        <p class="basis-3/5 text-gray-800">&nbsp; {{ ucfirst($lawyer->gender) }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.preferred_working_hours') }} :</p>
                        <p class="basis-3/5 text-gray-800">&nbsp; {{ ucfirst($lawyer->working_hours) }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.date_of_birth') }} :</p>
                        <p class="basis-3/5 text-gray-800">&nbsp; {{ date('d M Y', strtotime($lawyer->date_of_birth)) }}
                        </p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.emirate') }} :</p>
                        <p class="basis-3/5 text-gray-800">&nbsp;
                            {{ $lawyer->emirate?->getTranslation('name', app()->getLocale()) }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.nationality') }} :</p>
                        <p class="basis-3/5 text-gray-800">&nbsp;
                            {{ $lawyer->nationalityCountry?->getTranslation('name', app()->getLocale()) }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.years_of_experience') }} :</p>
                        <p class="basis-3/5 text-gray-800">&nbsp;
                            {{ $lawyer->yearsExperienceOption?->getTranslation('name', app()->getLocale()) }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.specialities') }} :</p>
                        <p class="basis-3/5 text-gray-800">
                            @foreach ($lawyer->specialities as $si => $speciality)
                                {{ $speciality->dropdownOption?->getTranslation('name', app()->getLocale()) ?? '' }}
                                {{ $si != count($lawyer->specialities) - 1 ? ', ' : '' }}
                            @endforeach
                        </p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.languages_spoken') }} :</p>
                        <p class="basis-3/5 text-gray-800">
                            @foreach ($lawyer->languages as $la => $language)
                                {{ $language->dropdownOption?->getTranslation('name', app()->getLocale()) ?? '' }}
                                {{ $la != count($lawyer->languages) - 1 ? ', ' : '' }}
                            @endforeach
                        </p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.email') }} :</p>
                        <p class="basis-3/5 text-blue-600 hover:underline">{{ $lawyer->email }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.phone_number') }} :</p>
                        <p class="basis-3/5 text-gray-800">{{ $lawyer->phone }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-medium text-[#07683B] mb-6">{{ __('frontend.documents') }}</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-gray-600 font-medium mb-2">{{ __('frontend.emirates_id') }} :</p>
                        <div class="flex flex-wrap gap-3">

                            @php
                                $emirate_id_frontfile = $lawyer->emirate_id_front;
                                $emirate_id_front = basename($emirate_id_frontfile);
                                $emirate_id_frontextension = strtolower(
                                    pathinfo($emirate_id_front, PATHINFO_EXTENSION),
                                );
                            @endphp

                            <div class="inline-flex items-center ">
                                <span class="text-[#4D1717] ">
                                    @if (in_array($emirate_id_frontextension, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                        <a href="{{ asset($emirate_id_frontfile) }}" target="_blank"
                                            data-fancybox="gallery">
                                            <img src="{{ asset($emirate_id_frontfile) }}" alt="{{ $emirate_id_front }}"
                                                style="width: 80px; height: auto; cursor: zoom-in;">
                                        </a>
                                    @elseif($emirate_id_frontextension === 'pdf')
                                        <a href="{{ asset($emirate_id_frontfile) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $emirate_id_front }}">
                                        </a>
                                    @else
                                        <a href="{{ asset($emirate_id_frontfile) }}" download>
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75"  alt="{{ $emirate_id_front }}">
                                        </a>
                                    @endif
                                </span>
                            </div>

                            @php
                                $emirate_id_backfile = $lawyer->emirate_id_back;
                                $emirate_id_back = basename($emirate_id_backfile);
                                $emirate_id_backextension = strtolower(pathinfo($emirate_id_back, PATHINFO_EXTENSION));
                            @endphp

                            <div class="inline-flex items-center ">
                                <span class="text-[#4D1717] ">
                                    @if (in_array($emirate_id_backextension, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                        <a href="{{ asset($emirate_id_backfile) }}" target="_blank"
                                            data-fancybox="gallery">
                                            <img src="{{ asset($emirate_id_backfile) }}" alt="{{ $emirate_id_back }}"
                                                style="width: 80px; height: auto; cursor: zoom-in;">
                                        </a>
                                    @elseif($emirate_id_backextension === 'pdf')
                                        <a href="{{ asset($emirate_id_backfile) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $emirate_id_back }}">
                                        </a>
                                    @else
                                        <a href="{{ asset($emirate_id_backfile) }}" download>
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $emirate_id_back }}">
                                        </a>
                                    @endif
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 self-center">{{ __('frontend.expiry_date') }}:
                                {{ date('d M Y', strtotime($lawyer->emirate_id_expiry)) }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-gray-600 font-medium mb-2">{{ __('frontend.passport') }} :</p>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $passportfile = $lawyer->passport;
                                $passport = basename($passportfile);
                                $passportextension = strtolower(pathinfo($passport, PATHINFO_EXTENSION));
                            @endphp

                            <div class="inline-flex items-center ">
                                <span class="text-[#4D1717] ">
                                    @if (in_array($passportextension, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                        <a href="{{ asset($passportfile) }}" target="_blank" data-fancybox="gallery">
                                            <img src="{{ asset($passportfile) }}" alt="{{ $passport }}"
                                                style="width: 80px; height: auto; cursor: zoom-in;">
                                        </a>
                                    @elseif($passportextension === 'pdf')
                                        <a href="{{ asset($passportfile) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $passport }}">
                                        </a>
                                    @else
                                        <a href="{{ asset($passportfile) }}" download>
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $passport }}">
                                        </a>
                                    @endif
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 self-center">{{ __('frontend.expiry_date') }}:
                                {{ date('d M Y', strtotime($lawyer->passport_expiry)) }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-gray-600 font-medium mb-2">{{ __('frontend.residence_visa') }} :</p>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $residence_visafile = $lawyer->residence_visa;
                                $residence_visa = basename($residence_visafile);
                                $residence_visaextension = strtolower(pathinfo($residence_visa, PATHINFO_EXTENSION));
                            @endphp

                            <div class="inline-flex items-center ">
                                <span class="text-[#4D1717] ">
                                    @if (in_array($residence_visaextension, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                        <a href="{{ asset($residence_visafile) }}" target="_blank"
                                            data-fancybox="gallery">
                                            <img src="{{ asset($residence_visafile) }}" alt="{{ $residence_visa }}"
                                                style="width: 80px; height: auto; cursor: zoom-in;">
                                        </a>
                                    @elseif($residence_visaextension === 'pdf')
                                        <a href="{{ asset($residence_visafile) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $residence_visa }}">
                                        </a>
                                    @else
                                        <a href="{{ asset($residence_visafile) }}" download>
                                           <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $residence_visa }}">
                                        </a>
                                    @endif
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 self-center">{{ __('frontend.expiry_date') }}:
                                {{ date('d M Y', strtotime($lawyer->residence_visa_expiry)) }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-gray-600 font-medium mb-2">{{ __('frontend.bar_card_legal_certificate') }} :</p>
                        <div class="flex flex-wrap gap-3">
                             @php
                                $bar_cardfile = $lawyer->bar_card;
                                $bar_card = basename($bar_cardfile);
                                $bar_cardextension = strtolower(pathinfo($bar_card, PATHINFO_EXTENSION));
                            @endphp
                            
                            <div class="inline-flex items-center ">
                                <span class="text-[#4D1717] "> 
                                    @if(in_array($bar_cardextension, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                        <a href="{{ asset($bar_cardfile) }}" target="_blank"  data-fancybox="gallery">
                                            <img src="{{ asset($bar_cardfile) }}" alt="{{ $bar_card }}" style="width: 80px; height: auto; cursor: zoom-in;">
                                        </a>
                                    @elseif($bar_cardextension === 'pdf')
                                        <a href="{{ asset($bar_cardfile) }}" target="_blank" class="btn btn-sm btn-primary">
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $bar_card }}">
                                        </a>
                                    @else
                                        <a href="{{ asset($bar_cardfile) }}" download>
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $bar_card }}">
                                        </a>
                                    @endif
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 self-center">{{ __('frontend.expiry_date') }}:
                                {{ date('d M Y', strtotime($lawyer->bar_card_expiry)) }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-gray-600 font-medium mb-2">{{ __('frontend.ministry_of_justice_card') }}:</p>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $practicing_lawyer_cardfile = $lawyer->practicing_lawyer_card;
                                $practicing_lawyer_card = basename($practicing_lawyer_cardfile);
                                $practicing_lawyer_cardextension = strtolower(pathinfo($practicing_lawyer_card, PATHINFO_EXTENSION));
                            @endphp

                            <div class="inline-flex items-center ">
                                <span class="text-[#4D1717] ">
                                    @if (in_array($practicing_lawyer_cardextension, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                        <a href="{{ asset($practicing_lawyer_cardfile) }}" target="_blank" data-fancybox="gallery">
                                            <img src="{{ asset($practicing_lawyer_cardfile) }}" alt="{{ $practicing_lawyer_card }}"
                                                style="width: 80px; height: auto; cursor: zoom-in;">
                                        </a>
                                    @elseif($practicing_lawyer_cardextension === 'pdf')
                                        <a href="{{ asset($practicing_lawyer_cardfile) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $practicing_lawyer_card }}">
                                        </a>
                                    @else
                                        <a href="{{ asset($practicing_lawyer_cardfile) }}" download>
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-12 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="{{ $practicing_lawyer_card }}">
                                        </a>
                                    @endif
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 self-center">{{ __('frontend.expiry_date') }}:
                                {{ date('d M Y', strtotime($lawyer->practicing_lawyer_card_expiry)) }}</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>


    </div>

    <div class="bg-white rounded-lg p-6 mt-5">
        <h2 class="text-xl font-medium text-gray-900 mb-4">Consultation History</h2>
        <div class="relative overflow-x-auto sm:rounded-lg">
            <table class="w-full border">
                <thead class="text-md font-normal">
                    <tr class="bg-[#07683B] text-white font-normal">
                        <th scope="col" class="px-6 py-5 font-semibold text-start">Ref. No</th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">Date and Time</th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">Lawyer Name</th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">Duration</th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">Case Type</th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">Client Name</th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b text-[#4D4D4D]">
                        <td scope="row" class="px-6 py-4">
                            REF-001234
                        </td>
                        <td class="px-6 py-4">
                            2025-05-21 10:30 AM
                        </td>
                        <td class="px-6 py-4">
                            John Davis
                        </td>
                        <td class="px-6 py-4">
                            00:15:00
                        </td>
                        <td class="px-6 py-4">
                            Divorce
                        </td>
                        <td class="px-6 py-4">
                            William Brooks
                        </td>
                        <td class="px-6 py-4">

                            <a href="#" class="flex items-center gap-0.5">
                                <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>View</span>

                            </a>

                        </td>

                    </tr>
                    <tr class="bg-[#EEF4F1] border-b  text-[#4D4D4D]">
                        <td scope="row" class="px-6 py-4">
                            REF-001234
                        </td>
                        <td class="px-6 py-4">
                            2025-05-21 10:30 AM
                        </td>
                        <td class="px-6 py-4">
                            John Davis
                        </td>
                        <td class="px-6 py-4">
                            00:15:00
                        </td>
                        <td class="px-6 py-4">
                            Divorce
                        </td>
                        <td class="px-6 py-4">
                            William Brooks
                        </td>
                        <td class="px-6 py-4">

                            <a href="#" class="flex items-center gap-0.5">
                                <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>View</span>

                            </a>

                        </td>
                    </tr>
                    <tr class="border-b text-[#4D4D4D]">
                        <td scope="row" class="px-6 py-4">
                            REF-001234
                        </td>
                        <td class="px-6 py-4">
                            2025-05-21 10:30 AM
                        </td>
                        <td class="px-6 py-4">
                            John Davis
                        </td>
                        <td class="px-6 py-4">
                            00:15:00
                        </td>
                        <td class="px-6 py-4">
                            Divorce
                        </td>
                        <td class="px-6 py-4">
                            William Brooks
                        </td>
                        <td class="px-6 py-4">

                            <a href="#" class="flex items-center gap-0.5">
                                <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>View</span>

                            </a>

                        </td>

                    </tr>
                    <tr class="bg-[#EEF4F1] border-b  text-[#4D4D4D]">
                        <td scope="row" class="px-6 py-4">
                            REF-001234
                        </td>
                        <td class="px-6 py-4">
                            2025-05-21 10:30 AM
                        </td>
                        <td class="px-6 py-4">
                            John Davis
                        </td>
                        <td class="px-6 py-4">
                            00:15:00
                        </td>
                        <td class="px-6 py-4">
                            Divorce
                        </td>
                        <td class="px-6 py-4">
                            William Brooks
                        </td>
                        <td class="px-6 py-4">

                            <a href="#" class="flex items-center gap-0.5">
                                <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>View</span>

                            </a>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
