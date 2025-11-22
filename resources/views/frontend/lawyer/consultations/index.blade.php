@extends('layouts.web_lawyer', ['title' => __('frontend.consultations')])

@section('content')
    <div class="bg-white rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-medium text-gray-900">{{ __('frontend.consultations') }}</h2>
        
        </div>

        <hr class="my-4 border-[#DFDFDF]" />
        <form method="GET" id="filterForm" action="{{ route('lawyer.consultations.index') }}" autocomplete="off">
            <div class="grid grid-cols-1 md:grid-cols-12 items-end gap-4 mb-8">

                <div class="relative col-span-3">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="simple-search" value="{{ request('keyword') }}" name="keyword"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-3.5"
                        placeholder="{{ __('frontend.search_ref_no') }}" required />
                </div>

                <div class="col-span-3">
                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900">{{ __('frontend.date') }}</label>
                
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 date-range-picker" name="daterange" placeholder="{{ __('frontend.from_date_to_date') }}" value="{{ request('daterange') }}">
                </div>

                <div class="col-span-3">
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900">{{ __('frontend.case_type') }}</label>
                
                    <select name="specialities" id="select-tag" class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"  data-placeholder="{{ __('frontend.case_type') }}">
                        <option value="">{{ __('frontend.case_type') }}</option>
                        @foreach($dropdowns['specialities']->options as $option)
                            <option value="{{ $option->id }}" {{ request()->specialities == $option->id ? 'selected' : '' }}>
                                {{ $option->getTranslation('name', app()->getLocale()) ?? 'Unnamed' }}
                            </option>
                        @endforeach
                    </select>

                </div>
               
            </div>
        </form>
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

                    @forelse($consultations as $key => $assignment)
                        @php
                            $consultation = $assignment->consultation;
                        @endphp
                        <tr  class="border-b text-[#4D4D4D]">
                            <td class="px-6 py-4  text-center">
                                {{ $key + 1 + ($consultations->currentPage() - 1) * $consultations->perPage() }}
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

            <div class="mt-6">
                {{ $consultations->appends(request()->input())->links() }}
            </div>
            
        </div>
    </div>
@endsection

@section('script')
     <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterForm = document.getElementById('filterForm');

            filterForm.querySelectorAll('select').forEach(function (el) {
                el.addEventListener('change', function () {
                    filterForm.submit();
                });
            });

            $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
                filterForm.submit();
            });

            $('.date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                filterForm.submit();
            });


            let typingTimer;
            const keywordInput = document.getElementById('simple-search');
            keywordInput.addEventListener('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    filterForm.submit();
                }, 500); 
            });

        });

    </script>
@endsection
