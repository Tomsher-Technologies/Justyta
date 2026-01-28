@extends('layouts.web_lawyer', ['title' => __('frontend.dashboard')])

@section('content')
    <!-- Consultancy Form -->
    <h2 class="text-xl font-medium text-gray-800 mb-4">
        {{ __('frontend.dashboard') }}
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <a href="{{ route('lawyer.consultations.index') }}">
            <div class="bg-white rounded-lg p-5 flex items-start justify-between space-x-4 border border-[#FFE9B1] h-full">
                <div>
                    <h3 class="text-black text-md">{{ __('frontend.todays_consultations') }}</h3>
                    <h4 class="font-semibold text-[24px]">{{ $acceptedConsultationsToday ?? 0 }}</h4>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" viewBox="0 0 35 35" fill="none">
                    <g clip-path="url(#clip0_390_9523)">
                        <path
                            d="M0.908071 31.5372C0.789417 31.7206 0.794811 31.958 0.924251 32.136C1.02673 32.2816 1.19392 32.3679 1.36651 32.3679C1.40426 32.3679 1.44201 32.3679 1.48516 32.3571L6.44166 31.2568C7.8709 31.9849 9.47273 32.3679 11.0799 32.3679C16.6729 32.3679 21.2303 27.8587 21.3165 22.2816C23.7436 22.9396 26.4025 22.6915 28.6569 21.548L33.6134 22.643C33.6512 22.6537 33.6889 22.6537 33.7321 22.6537C33.9046 22.6537 34.0718 22.5674 34.1743 22.4218C34.2984 22.2438 34.3038 22.0065 34.1905 21.8231L32.168 18.6246C33.5271 16.8501 34.2714 14.6548 34.2714 12.4002C34.2714 6.74757 29.6763 2.1521 24.024 2.1521C18.3718 2.1521 13.8737 6.66127 13.7874 12.233C12.9245 11.9957 12.0184 11.8608 11.0799 11.8608C5.41692 11.8662 0.821777 16.4617 0.821777 22.1144C0.821777 24.3636 1.56606 26.5588 2.93058 28.3388L0.908071 31.5372ZM24.0132 3.23624C29.0668 3.23624 33.1819 7.35167 33.1819 12.4056C33.1819 14.5361 32.4377 16.6127 31.0785 18.247C30.9275 18.425 30.9113 18.6839 31.0354 18.8781L32.5725 21.3053L28.6893 20.4477C28.5652 20.4207 28.4304 20.4369 28.3171 20.4962C26.1652 21.6397 23.571 21.8609 21.2626 21.1381C20.8959 17.2492 18.3394 13.986 14.8445 12.5944C14.8445 12.5297 14.8391 12.4649 14.8391 12.4002C14.8391 7.34628 18.9543 3.23085 24.0078 3.23085L24.0132 3.23624ZM1.90045 22.1144C1.90045 17.0604 6.01558 12.945 11.0692 12.945C16.1227 12.945 20.2379 17.0604 20.2379 22.1144C20.2379 27.1683 16.1227 31.2837 11.0692 31.2837C9.56981 31.2837 8.08124 30.9116 6.75987 30.205C6.67897 30.1618 6.59267 30.1403 6.50638 30.1403C6.46862 30.1403 6.42548 30.1403 6.38772 30.1511L2.5099 31.0087L4.04701 28.5815C4.17105 28.3819 4.15487 28.1284 4.00925 27.9504C2.65013 26.3107 1.90584 24.2341 1.90584 22.1036L1.90045 22.1144Z"
                            fill="#B9A572" />
                        <path
                            d="M11.0688 26.9687C11.5156 26.9687 11.8778 26.6065 11.8778 26.1596C11.8778 25.7128 11.5156 25.3506 11.0688 25.3506C10.622 25.3506 10.2598 25.7128 10.2598 26.1596C10.2598 26.6065 10.622 26.9687 11.0688 26.9687Z"
                            fill="#B9A572" />
                        <path
                            d="M11.0687 17.7993C11.9587 17.7993 12.6868 18.5275 12.6868 19.4175C12.6868 20.3074 11.9587 21.0356 11.0687 21.0356C10.7721 21.0356 10.5294 21.2783 10.5294 21.575V24.2718H11.6081V22.0604C12.8378 21.8123 13.7654 20.7227 13.7654 19.4175C13.7654 17.9288 12.5573 16.7206 11.0687 16.7206C9.58018 16.7206 8.37207 17.9288 8.37207 19.4175H9.45074C9.45074 18.5275 10.1788 17.7993 11.0687 17.7993Z"
                            fill="#B9A572" />
                        <path d="M19.6982 8.63H28.3276V9.70875H19.6982V8.63Z" fill="#B9A572" />
                        <path d="M19.6982 11.8662H28.3276V12.945H19.6982V11.8662Z" fill="#B9A572" />
                        <path d="M21.8555 15.1025H28.3275V16.1813H21.8555V15.1025Z" fill="#B9A572" />
                    </g>
                    <defs>
                        <clipPath id="clip0_390_9523">
                            <rect width="34.5175" height="34.52" fill="white" transform="translate(0.282227)" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
        </a>

        <a href="{{ route('lawyer.consultations.index') }}">
            <div class="bg-white rounded-lg p-5 flex items-start justify-between space-x-4 border border-[#FFE9B1] h-full">
                <div>
                    <h3 class="text-black text-md">{{ __('frontend.total_accepted_consultations') }}</h3>
                    <h4 class="font-semibold text-[24px]">{{ $totalAcceptedConsultations ?? 0 }}</h4>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" viewBox="0 0 35 35" fill="none">
                    <g clip-path="url(#clip0_390_9523)">
                        <path
                            d="M0.908071 31.5372C0.789417 31.7206 0.794811 31.958 0.924251 32.136C1.02673 32.2816 1.19392 32.3679 1.36651 32.3679C1.40426 32.3679 1.44201 32.3679 1.48516 32.3571L6.44166 31.2568C7.8709 31.9849 9.47273 32.3679 11.0799 32.3679C16.6729 32.3679 21.2303 27.8587 21.3165 22.2816C23.7436 22.9396 26.4025 22.6915 28.6569 21.548L33.6134 22.643C33.6512 22.6537 33.6889 22.6537 33.7321 22.6537C33.9046 22.6537 34.0718 22.5674 34.1743 22.4218C34.2984 22.2438 34.3038 22.0065 34.1905 21.8231L32.168 18.6246C33.5271 16.8501 34.2714 14.6548 34.2714 12.4002C34.2714 6.74757 29.6763 2.1521 24.024 2.1521C18.3718 2.1521 13.8737 6.66127 13.7874 12.233C12.9245 11.9957 12.0184 11.8608 11.0799 11.8608C5.41692 11.8662 0.821777 16.4617 0.821777 22.1144C0.821777 24.3636 1.56606 26.5588 2.93058 28.3388L0.908071 31.5372ZM24.0132 3.23624C29.0668 3.23624 33.1819 7.35167 33.1819 12.4056C33.1819 14.5361 32.4377 16.6127 31.0785 18.247C30.9275 18.425 30.9113 18.6839 31.0354 18.8781L32.5725 21.3053L28.6893 20.4477C28.5652 20.4207 28.4304 20.4369 28.3171 20.4962C26.1652 21.6397 23.571 21.8609 21.2626 21.1381C20.8959 17.2492 18.3394 13.986 14.8445 12.5944C14.8445 12.5297 14.8391 12.4649 14.8391 12.4002C14.8391 7.34628 18.9543 3.23085 24.0078 3.23085L24.0132 3.23624ZM1.90045 22.1144C1.90045 17.0604 6.01558 12.945 11.0692 12.945C16.1227 12.945 20.2379 17.0604 20.2379 22.1144C20.2379 27.1683 16.1227 31.2837 11.0692 31.2837C9.56981 31.2837 8.08124 30.9116 6.75987 30.205C6.67897 30.1618 6.59267 30.1403 6.50638 30.1403C6.46862 30.1403 6.42548 30.1403 6.38772 30.1511L2.5099 31.0087L4.04701 28.5815C4.17105 28.3819 4.15487 28.1284 4.00925 27.9504C2.65013 26.3107 1.90584 24.2341 1.90584 22.1036L1.90045 22.1144Z"
                            fill="#B9A572" />
                        <path
                            d="M11.0688 26.9687C11.5156 26.9687 11.8778 26.6065 11.8778 26.1596C11.8778 25.7128 11.5156 25.3506 11.0688 25.3506C10.622 25.3506 10.2598 25.7128 10.2598 26.1596C10.2598 26.6065 10.622 26.9687 11.0688 26.9687Z"
                            fill="#B9A572" />
                        <path
                            d="M11.0687 17.7993C11.9587 17.7993 12.6868 18.5275 12.6868 19.4175C12.6868 20.3074 11.9587 21.0356 11.0687 21.0356C10.7721 21.0356 10.5294 21.2783 10.5294 21.575V24.2718H11.6081V22.0604C12.8378 21.8123 13.7654 20.7227 13.7654 19.4175C13.7654 17.9288 12.5573 16.7206 11.0687 16.7206C9.58018 16.7206 8.37207 17.9288 8.37207 19.4175H9.45074C9.45074 18.5275 10.1788 17.7993 11.0687 17.7993Z"
                            fill="#B9A572" />
                        <path d="M19.6982 8.63H28.3276V9.70875H19.6982V8.63Z" fill="#B9A572" />
                        <path d="M19.6982 11.8662H28.3276V12.945H19.6982V11.8662Z" fill="#B9A572" />
                        <path d="M21.8555 15.1025H28.3275V16.1813H21.8555V15.1025Z" fill="#B9A572" />
                    </g>
                    <defs>
                        <clipPath id="clip0_390_9523">
                            <rect width="34.5175" height="34.52" fill="white" transform="translate(0.282227)" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
        </a>

        <a href="{{ route('lawyer.consultations.index') }}">
            <div class="bg-white rounded-lg p-5 flex items-start justify-between space-x-4 border border-[#FFE9B1] h-full">
                <div>
                    <h3 class="text-black text-md">{{ __('frontend.total_rejected_consultations') }}</h3>
                    <h4 class="font-semibold text-[24px]">{{ $totalRejections ?? 0 }}</h4>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" viewBox="0 0 35 35" fill="none">
                    <g clip-path="url(#clip0_390_9523)">
                        <path
                            d="M0.908071 31.5372C0.789417 31.7206 0.794811 31.958 0.924251 32.136C1.02673 32.2816 1.19392 32.3679 1.36651 32.3679C1.40426 32.3679 1.44201 32.3679 1.48516 32.3571L6.44166 31.2568C7.8709 31.9849 9.47273 32.3679 11.0799 32.3679C16.6729 32.3679 21.2303 27.8587 21.3165 22.2816C23.7436 22.9396 26.4025 22.6915 28.6569 21.548L33.6134 22.643C33.6512 22.6537 33.6889 22.6537 33.7321 22.6537C33.9046 22.6537 34.0718 22.5674 34.1743 22.4218C34.2984 22.2438 34.3038 22.0065 34.1905 21.8231L32.168 18.6246C33.5271 16.8501 34.2714 14.6548 34.2714 12.4002C34.2714 6.74757 29.6763 2.1521 24.024 2.1521C18.3718 2.1521 13.8737 6.66127 13.7874 12.233C12.9245 11.9957 12.0184 11.8608 11.0799 11.8608C5.41692 11.8662 0.821777 16.4617 0.821777 22.1144C0.821777 24.3636 1.56606 26.5588 2.93058 28.3388L0.908071 31.5372ZM24.0132 3.23624C29.0668 3.23624 33.1819 7.35167 33.1819 12.4056C33.1819 14.5361 32.4377 16.6127 31.0785 18.247C30.9275 18.425 30.9113 18.6839 31.0354 18.8781L32.5725 21.3053L28.6893 20.4477C28.5652 20.4207 28.4304 20.4369 28.3171 20.4962C26.1652 21.6397 23.571 21.8609 21.2626 21.1381C20.8959 17.2492 18.3394 13.986 14.8445 12.5944C14.8445 12.5297 14.8391 12.4649 14.8391 12.4002C14.8391 7.34628 18.9543 3.23085 24.0078 3.23085L24.0132 3.23624ZM1.90045 22.1144C1.90045 17.0604 6.01558 12.945 11.0692 12.945C16.1227 12.945 20.2379 17.0604 20.2379 22.1144C20.2379 27.1683 16.1227 31.2837 11.0692 31.2837C9.56981 31.2837 8.08124 30.9116 6.75987 30.205C6.67897 30.1618 6.59267 30.1403 6.50638 30.1403C6.46862 30.1403 6.42548 30.1403 6.38772 30.1511L2.5099 31.0087L4.04701 28.5815C4.17105 28.3819 4.15487 28.1284 4.00925 27.9504C2.65013 26.3107 1.90584 24.2341 1.90584 22.1036L1.90045 22.1144Z"
                            fill="#B9A572" />
                        <path
                            d="M11.0688 26.9687C11.5156 26.9687 11.8778 26.6065 11.8778 26.1596C11.8778 25.7128 11.5156 25.3506 11.0688 25.3506C10.622 25.3506 10.2598 25.7128 10.2598 26.1596C10.2598 26.6065 10.622 26.9687 11.0688 26.9687Z"
                            fill="#B9A572" />
                        <path
                            d="M11.0687 17.7993C11.9587 17.7993 12.6868 18.5275 12.6868 19.4175C12.6868 20.3074 11.9587 21.0356 11.0687 21.0356C10.7721 21.0356 10.5294 21.2783 10.5294 21.575V24.2718H11.6081V22.0604C12.8378 21.8123 13.7654 20.7227 13.7654 19.4175C13.7654 17.9288 12.5573 16.7206 11.0687 16.7206C9.58018 16.7206 8.37207 17.9288 8.37207 19.4175H9.45074C9.45074 18.5275 10.1788 17.7993 11.0687 17.7993Z"
                            fill="#B9A572" />
                        <path d="M19.6982 8.63H28.3276V9.70875H19.6982V8.63Z" fill="#B9A572" />
                        <path d="M19.6982 11.8662H28.3276V12.945H19.6982V11.8662Z" fill="#B9A572" />
                        <path d="M21.8555 15.1025H28.3275V16.1813H21.8555V15.1025Z" fill="#B9A572" />
                    </g>
                    <defs>
                        <clipPath id="clip0_390_9523">
                            <rect width="34.5175" height="34.52" fill="white" transform="translate(0.282227)" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
        </a>

        <a href="#">
            <div class="bg-white rounded-lg p-5 flex items-start justify-between space-x-4 border border-[#FFE9B1] h-full">
                <div>
                    <h3 class="text-black text-md">{{ __('frontend.todays_login_hours') }}</h3>
                    <h4 class="font-semibold text-[24px]"  id="activeHours">00:00:00</h4>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" viewBox="0 0 35 35" fill="none">
                    <g clip-path="url(#clip0_390_9523)">
                        <path
                            d="M0.908071 31.5372C0.789417 31.7206 0.794811 31.958 0.924251 32.136C1.02673 32.2816 1.19392 32.3679 1.36651 32.3679C1.40426 32.3679 1.44201 32.3679 1.48516 32.3571L6.44166 31.2568C7.8709 31.9849 9.47273 32.3679 11.0799 32.3679C16.6729 32.3679 21.2303 27.8587 21.3165 22.2816C23.7436 22.9396 26.4025 22.6915 28.6569 21.548L33.6134 22.643C33.6512 22.6537 33.6889 22.6537 33.7321 22.6537C33.9046 22.6537 34.0718 22.5674 34.1743 22.4218C34.2984 22.2438 34.3038 22.0065 34.1905 21.8231L32.168 18.6246C33.5271 16.8501 34.2714 14.6548 34.2714 12.4002C34.2714 6.74757 29.6763 2.1521 24.024 2.1521C18.3718 2.1521 13.8737 6.66127 13.7874 12.233C12.9245 11.9957 12.0184 11.8608 11.0799 11.8608C5.41692 11.8662 0.821777 16.4617 0.821777 22.1144C0.821777 24.3636 1.56606 26.5588 2.93058 28.3388L0.908071 31.5372ZM24.0132 3.23624C29.0668 3.23624 33.1819 7.35167 33.1819 12.4056C33.1819 14.5361 32.4377 16.6127 31.0785 18.247C30.9275 18.425 30.9113 18.6839 31.0354 18.8781L32.5725 21.3053L28.6893 20.4477C28.5652 20.4207 28.4304 20.4369 28.3171 20.4962C26.1652 21.6397 23.571 21.8609 21.2626 21.1381C20.8959 17.2492 18.3394 13.986 14.8445 12.5944C14.8445 12.5297 14.8391 12.4649 14.8391 12.4002C14.8391 7.34628 18.9543 3.23085 24.0078 3.23085L24.0132 3.23624ZM1.90045 22.1144C1.90045 17.0604 6.01558 12.945 11.0692 12.945C16.1227 12.945 20.2379 17.0604 20.2379 22.1144C20.2379 27.1683 16.1227 31.2837 11.0692 31.2837C9.56981 31.2837 8.08124 30.9116 6.75987 30.205C6.67897 30.1618 6.59267 30.1403 6.50638 30.1403C6.46862 30.1403 6.42548 30.1403 6.38772 30.1511L2.5099 31.0087L4.04701 28.5815C4.17105 28.3819 4.15487 28.1284 4.00925 27.9504C2.65013 26.3107 1.90584 24.2341 1.90584 22.1036L1.90045 22.1144Z"
                            fill="#B9A572" />
                        <path
                            d="M11.0688 26.9687C11.5156 26.9687 11.8778 26.6065 11.8778 26.1596C11.8778 25.7128 11.5156 25.3506 11.0688 25.3506C10.622 25.3506 10.2598 25.7128 10.2598 26.1596C10.2598 26.6065 10.622 26.9687 11.0688 26.9687Z"
                            fill="#B9A572" />
                        <path
                            d="M11.0687 17.7993C11.9587 17.7993 12.6868 18.5275 12.6868 19.4175C12.6868 20.3074 11.9587 21.0356 11.0687 21.0356C10.7721 21.0356 10.5294 21.2783 10.5294 21.575V24.2718H11.6081V22.0604C12.8378 21.8123 13.7654 20.7227 13.7654 19.4175C13.7654 17.9288 12.5573 16.7206 11.0687 16.7206C9.58018 16.7206 8.37207 17.9288 8.37207 19.4175H9.45074C9.45074 18.5275 10.1788 17.7993 11.0687 17.7993Z"
                            fill="#B9A572" />
                        <path d="M19.6982 8.63H28.3276V9.70875H19.6982V8.63Z" fill="#B9A572" />
                        <path d="M19.6982 11.8662H28.3276V12.945H19.6982V11.8662Z" fill="#B9A572" />
                        <path d="M21.8555 15.1025H28.3275V16.1813H21.8555V15.1025Z" fill="#B9A572" />
                    </g>
                    <defs>
                        <clipPath id="clip0_390_9523">
                            <rect width="34.5175" height="34.52" fill="white" transform="translate(0.282227)" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
        </a>

    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <div class="lg:col-span-2 bg-white rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-medium text-gray-900">
                    {{ __('frontend.no_of_completed_consultations') }}
                </h2>
                @php
                    $currentYear = now()->year;
                    $minYear = 2024; // stop at 2025
                    $endYear = max($currentYear - 9, $minYear);
                @endphp

                <select id="consultation-year"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    @for ($y = $currentYear; $y >= $endYear; $y--)
                        <option value="{{ $y }}" {{ isset($year) && $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

            </div>
            <div id="consultationChart" class="w-full h-72"></div>
        </div>

        <div class="bg-white rounded-lg p-6">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-medium text-gray-900"> {{ __('frontend.notifications') }}</h2>
                <a href="{{ route('lawyer.notifications.index') }}" class="text-[#B9A572]">{{ __('frontend.view_all') }}</a>
            </div>

            @forelse($notifications as $notification)
                <div class="flex items-center justify-between border-b pb-3 mb-3">
                    <div>
                        <h4 class="text-[#3B3A3A] text-[16px] font-medium mb-0 leading-none">
                            {{ $notification['message'] }}
                        </h4>
                        <span class="text-[#7B7B7B] text-xs">{{ $notification['time'] }}</span>
                    </div>

                    <a href="javascript:void(0)" class="delete-single" data-id="{{ $notification['id'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25"
                            fill="none">
                            <path
                                d="M3.37402 6.55859H21.374M19.374 6.55859V20.5586C19.374 21.5586 18.374 22.5586 17.374 22.5586H7.37402C6.37402 22.5586 5.37402 21.5586 5.37402 20.5586V6.55859M8.37402 6.55859V4.55859C8.37402 3.55859 9.37402 2.55859 10.374 2.55859H14.374C15.374 2.55859 16.374 3.55859 16.374 4.55859V6.55859M10.374 11.5586V17.5586M14.374 11.5586V17.5586"
                                stroke="#C9C9C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            @empty
                <div class="text-center py-4 text-gray-500">
                    {{ __('frontend.no_notifications') }}
                </div>
            @endforelse

        </div>
    </div>
    <div class="bg-white rounded-lg p-6">
        <h2 class="text-xl font-medium text-gray-900 mb-4">
            {{ __('frontend.recent_consultations') }}
        </h2>
        <div class="relative overflow-x-auto sm:rounded-lg w-[240px] xl:w-full">
            <table class="w-full border">
                <thead class="text-md font-normal">
                    <tr class="bg-[#07683B] text-white font-normal">
                        <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.sl_no') }}</th>
                        <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.ref_no') }}</th>
                        <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.date') }}</th>
                        <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.lawyer') }}</th>
                        <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.duration') }}</th>
                        <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.client_name') }}</th>
                        {{-- <th class="px-6 py-5 font-semibold text-center" >{{ __('frontend.amount') }}</th> --}}
                        <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.status') }}</th>
                        <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-[#4D4D4D]">
                    @php
                        $i = 0;
                    @endphp
                    @forelse($consultations as $key =>$assignment)
                        @php
                            $consultation = $assignment->consultation;
                        @endphp
                        <tr  class="border-b text-[#4D4D4D]">
                            <td class="px-6 py-4  text-center">
                                {{ $key + 1  }}
                            </td>

                            <td class="px-6 py-4  text-center">
                                {{ $consultation->ref_code ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                {{ date('d, M Y h:i A', strtotime($consultation->created_at)) }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                {{ $assignment->lawyer?->full_name ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                {{ $consultation->duration ?? 0 }} <small>Mins</small>
                            </td>

                            <td class="px-6 py-4 text-center">
                                {{ $consultation->user?->name ?? '—' }}
                            </td>
                            
                            {{-- <td class="px-6 py-4 text-center">
                                @if($assignment->status == 'accepted')
                                    AED {{ number_format($consultation->lawyer_amount, 2) }}
                                @else
                                    AED 0.00
                                @endif
                            </td> --}}
                            <td class="px-6 py-4 text-center">
                                @php
                                    $status = $assignment->status ?? '';
                                    $bgColor = ($status == 'accepted') ? '#90EE90' : (($status == 'rejected') ?  '#FF0000' :  'blue');
                                    $textColor = ($status == 'accepted') ? '#000000' : (($status == 'rejected') ?  '#fff' :  '#fff');
                                @endphp
                                <span class="px-3 py-1 rounded text-sm font-medium" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                    {{ ucwords(str_replace('_', ' ', $status)) ?? ucwords($status) }}
                                </span>
                            </td>
                        
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('lawyer.consultations.show', $assignment->id) }}" class="flex items-center gap-0.5">
                                    <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-width="1.7" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                        <path stroke="currentColor" stroke-width="1.7" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                    <span>View</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center">{{ __('frontend.no_data_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
@endsection

@section('script')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        let totalSeconds = 0;
        let isOnline = false;
        let timer = null;

        function formatTime(seconds) {
            const hrs  = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);

            return (
                String(hrs).padStart(2, '0') + ':' +
                String(mins).padStart(2, '0') + ':' +
                String(secs).padStart(2, '0')
            );
        }

        function startTimer() {
            if (!isOnline || timer) return;

            console.log('⏱ Timer started');

            timer = setInterval(() => {
                totalSeconds++;
                document.getElementById('activeHours').innerText =
                    formatTime(totalSeconds);
            }, 1000);
        }

        fetch('/lawyer/dashboard/active-hours')
            .then(res => res.json())
            .then(data => {
                console.log('API data:', data);

                totalSeconds = parseInt(data.seconds, 10);
                isOnline = data.is_online === true || data.is_online === 1;

                document.getElementById('activeHours').innerText =
                    formatTime(totalSeconds);

                startTimer();
            })
            .catch(err => console.error('Active hours error:', err));


        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const monthlyData = @json($monthlyData);

        const chartData = monthNames.map((month, index) => ({
            name: month,
            y: monthlyData[index + 1] || 0,
            color: '#FFE9B1'
        }));

        function initializeChart() {
            Highcharts.chart('consultationChart', {
                chart: {
                    type: 'column',
                    backgroundColor: '#FFFFFF',
                    style: { fontFamily: 'inherit' }
                },
                title: { text: '', align: 'left' },
                xAxis: {
                    type: 'category',
                    labels: { style: { fontSize: '13px', color: '#666' } },
                    lineColor: '#E5E5E5',
                    tickColor: '#E5E5E5'
                },
                yAxis: {
                    title: { text: '' },
                    min: 0,
                    allowDecimals: false,
                    gridLineColor: '#F5F5F5',
                    labels: { style: { fontSize: '13px', color: '#666' } }
                },
                legend: { enabled: false },
                plotOptions: {
                    column: {
                        borderRadius: 8,
                        pointPadding: 0.1,
                        groupPadding: 0.1,
                        borderWidth: 0,
                        states: {
                            hover: { color: '#B9A572', brightness: 0 },
                            inactive: { opacity: 1 }
                        },
                        dataLabels: {
                            enabled: true,
                            inside: true,
                            verticalAlign: 'top',
                            y: -20,
                            formatter: function () {
                                return this.y ? this.y : null;
                            },
                            style: {
                                fontSize: '12px',
                                color: '#666',
                                textOutline: 'none'
                            }
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#FFFFFF',
                    borderColor: '#B9A572',
                    borderRadius: 8,
                    headerFormat: '<span style="font-size:11px; font-weight:bold">{point.key}</span><br>',
                    pointFormat: '<b>{point.y}</b> Completed Consultations'
                },
                series: [{
                    name: 'Consultations',
                    data: chartData
                }],
                credits: { enabled: false }
            });
        }

        document.getElementById('consultation-year').addEventListener('change', function () {
            window.location.href = `?consultation_year=${this.value}`;
        });

        document.addEventListener('DOMContentLoaded', initializeChart);

        document.querySelectorAll('.delete-single').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.getAttribute('data-id');

                Swal.fire({
                    title: '{{ __('frontend.are_you_sure') }}',
                    text: "{{ __('frontend.action_cannot_undone') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('frontend.yes_delete') }}',
                    cancelButtonText: '{{ __('frontend.cancel') }}'
                }).then(result => {

                    if (result.isConfirmed) {

                        fetch("{{ route('lawyer.notifications.delete.selected') }}", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                notification_ids: [id] // send single ID as array
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message);
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                toastr.error(data.message);
                            }
                        })
                        .catch(err => {
                            toastr.error("{{ __('frontend.server_error') }}");
                            console.error(err);
                        });

                    }
                });
            });
        });


    </script>

@endsection
