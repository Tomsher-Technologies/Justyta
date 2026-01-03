@extends('layouts.web_vendor_default', ['title' => __('frontend.membership_plans') ])

@section('content')
    <div class="flex justify-center items-center py-10 bg-gray-50"> 
        <section class="py-10 px-4 md:px-16 pt-0">
            <div class="mb-10 text-center">
                <h3 class="text-3xl font-medium text-gray-800">{{ __('frontend.membership_plans') }}</h3>
            </div>

            @php
                $latestSubscription = $vendor->latestSubscription;
                $currentPlan = $latestSubscription ? $latestSubscription->plan : null;

                $status = $latestSubscription?->status;
                if ($status == 'active') {
                    $statusColor = 'bg-green-600';
                    $statusName = 'Active';
                } elseif ($status == 'expired') {
                    $statusColor = 'bg-red-600';
                    $statusName = 'Expired';
                }elseif ($status == 'pending') {
                    $statusColor = 'bg-orange-600';
                    $statusName = 'Payment Pending';
                }else {
                    $statusColor = 'bg-gray-600';
                    $statusName = 'Cancelled';
                }
            @endphp
            
            <div class="mb-8 p-6 bg-white rounded-2xl shadow-md border border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6">
                    <div class="flex items-center gap-4">
                        @if ($currentPlan->icon)
                            <img src="{{ asset(getUploadedImage($currentPlan->icon)) }}" alt="{{ $currentPlan->getTranslation('title') }}" class="h-16 w-16 object-contain rounded-lg">
                        @endif
                        <div>
                            <h4 class="text-2xl font-bold text-gray-800">{{ __('frontend.current_plan') }}</h4>
                            <p class="text-lg font-semibold text-gray-700 mt-1">{{ $currentPlan->getTranslation('title') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center gap-3 md:gap-6">
                        <span class="inline-block px-4 py-1 rounded-full text-sm font-semibold text-white {{ $statusColor }} text-center">
                            {{ $statusName }}
                        </span>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-gray-700 text-sm">
                            <div>
                                <span class="font-medium">{{ __('frontend.sdate') }}:</span>
                                <span><b>{{ \Carbon\Carbon::parse($latestSubscription->subscription_start)->format('d M, Y') }}</b></span>
                            </div>
                            <div>
                                <span class="font-medium">{{ __('frontend.edate') }}:</span>
                                <span><b>{{ \Carbon\Carbon::parse($latestSubscription->subscription_end)->format('d M, Y') }}</b></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($plans as $plan)
                <div class="bg-white rounded-2xl border border-gray-300 p-6 flex flex-col h-full">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-medium text-gray-800">{{ $plan->getTranslation('title') }}
                            </h2>
                            <p class="text-2xl font-semibold text-gray-800 mb-0">
                                {{ number_format($plan->amount, 2) }} {{ __('frontend.AED') }}
                            </p>

                            @php
                                $vatValue =  ($plan->plain_amount != 0 && $plan->vat_amount != 0) ? ($plan->plain_amount * $plan->vat_amount) / 100 : 0;
                            @endphp

                            <span class="text-xs">{{ __('frontend.including_vat') }} {{ __('frontend.AED') }} {{ number_format($vatValue, 2) }}</span>
                           
                        </div>
                        @if ($plan->icon)
                        <img src="{{ asset(getUploadedImage($plan->icon)) }}"
                            alt="{{ $plan->getTranslation('title') }}" class="h-16 w-16 object-contain">
                        @endif
                    </div>

                    <!-- Features -->
                    <ul class="space-y-2 text-sm text-gray-600 flex-1">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                            </svg>
                            {{ $plan->live_online ? __('frontend.live_online_access') : '-' }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                            </svg>
                            {{ $plan->specific_law_firm_choice ? __('frontend.specific_law_firm_choice') : '-' }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                            </svg>
                            {{ $plan->annual_legal_contract ? __('frontend.annual_legal_contract') : '-' }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                            </svg>
                            {{ __('frontend.up_to') }} {{ $plan->member_count }} {{ __('frontend.user_access') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                            </svg>
                            {{ $plan->job_post_count === 0 ? __('frontend.unlimited_job_posts') : $plan->job_post_count . ' '. __('frontend.job_posts_month') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                            </svg>
                            @if ($plan->annual_free_ad_days != 0)
                            {{ $plan->annual_free_ad_days }} {{ __('frontend.free_ad_days') }}
                            @else
                            -
                            @endif
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                            </svg>
                            {{ $plan->unlimited_training_applications ? __('frontend.unlimited_training_applications') : '-' }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                            </svg>
                            @if ($plan->welcome_gift === 'premium')
                            {{ __('frontend.premium_welcome_gift') }}
                            @elseif($plan->welcome_gift === 'special')
                            {{ __('frontend.special_welcome_gift') }}
                            @else
                            {{ __('frontend.no_welcome_gift') }}
                            @endif
                        </li>
                    </ul>

                    <div class="mt-4">
                        
                        <form action="{{ route('vendor.subscribe.plan', $plan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-[#07683B] text-white py-2 rounded-xl transition">
                                {{ __('frontend.subscribe_now') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script>
        
    </script>
@endsection 

