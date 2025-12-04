@extends('layouts.web_lawyer', ['title' => __('frontend.profile')])

@section('content')
    <div class="bg-white rounded-lg p-6">
      
        
        <div class="bg-white rounded-2xl  p-6 pb-12">

        <div class="grid grid-cols-1 border-b border-gray-200 pb-8 mb-8">
            <div class="flex items-center gap-6 col-span-12">
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
                    <p class="text-base text-gray-500">{{ $lawyer->lawfirm?->law_firm_name ?? '-' }}</p>
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
    </div>
   
@endsection

@section('script')
    <script>
        
    </script>
@endsection
