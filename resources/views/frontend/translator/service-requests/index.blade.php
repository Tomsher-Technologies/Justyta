@extends('layouts.web_translator', ['title' => __('frontend.service_requests')])

@section('content')
    <div class="bg-white rounded-lg p-6">
        <h2 class="text-xl font-medium text-gray-900 mb-4">{{ __('frontend.translation') }}</h2>

        <hr class="my-4 border-[#DFDFDF]" />

        <form method="GET" action="{{ route('translator.service.index') }}" class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-12 items-end gap-4">
                <div class="relative col-span-3">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="simple-search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-3.5"
                        placeholder="{{ __('frontend.search_here') }}" value="{{ request('search') }}" />
                </div>
                <div class="col-span-2">
                    <label for="date_from"
                        class="block mb-2 text-sm font-medium text-gray-900">{{ __('frontend.date_from_and_to') }}</label>
                    <input type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 date-range"
                        id="date_range" name="date_range" placeholder="{{ 'date' }}" data-time-picker="false"
                        data-format="YYYY-MM-DD" data-separator=" - " autocomplete="off"
                        value="{{ request('date_range') ? request('date_range') : '' }}">

                </div>
                <div class="col-span-2">
                    <label for="language_pair"
                        class="block mb-2 text-sm font-medium text-gray-900">{{ __('frontend.language_pairs') }}</label>
                    <select name="language_pair" id="language_pair"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option value="all"
                            {{ request('language_pair') == 'all' || !request('language_pair') ? 'selected' : '' }}>
                            {{ __('frontend.all') }}</option>
                        @if (isset($languagePairs))
                            @foreach ($languagePairs as $pair)
                                <option value="{{ $pair['combined'] }}"
                                    {{ request('language_pair') == $pair['combined'] ? 'selected' : '' }}>
                                    {{ $pair['combined'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-span-2">
                    <label for="status"
                        class="block mb-2 text-sm font-medium text-gray-900">{{ __('frontend.select_status') }}</label>
                    <select name="status" id="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>
                            {{ __('frontend.all') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            {{ __('frontend.pending') }}</option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>
                            {{ __('frontend.under_review') }}</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>
                            {{ __('frontend.ongoing') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            {{ __('frontend.completed') }}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                            {{ __('frontend.rejected') }}</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <button type="submit"
                        class="bg-[#07683B] text-white h-[50px] w-full px-6 py-3.5 text-center rounded-lg block">{{ __('frontend.filter') }}</button>
                </div>
                <div class="col-span-1">
                    <a href="{{ route('translator.service.index') }}"
                        class="bg-white text-[#07683B]  border-2 h-[50px] w-full px-6 py-3.5 text-center rounded-lg block">{{ __('frontend.reset') }}</a>
                </div>
            </div>
        </form>
        <div class="relative overflow-x-auto sm:rounded-lg">
            <table class="w-full border">
                <thead class="text-md font-normal">
                    <tr class="bg-[#07683B] text-white font-normal">
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            {{ __('frontend.ref_no') }}
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            {{ __('frontend.date_and_time') }}
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            {{ __('frontend.document_language') }}
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            {{ __('frontend.translation_language') }}
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            {{ __('frontend.no_of_pages') }}
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            {{ __('frontend.status') }}
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            {{ __('frontend.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($serviceRequests->count() > 0)
                        @foreach ($serviceRequests as $index => $request)
                            <tr class="{{ $index % 2 == 0 ? 'bg-[#EEF4F1]' : '' }} border-b text-[#4D4D4D]">
                                <td scope="row" class="px-6 py-4">{{ $request['reference_code'] }}</td>
                                <td class="px-6 py-4">{{ $request['date_time'] }}</td>
                                <td class="px-6 py-4">{{ $request['document_language'] }}</td>
                                <td class="px-6 py-4">{{ $request['translation_language'] }}</td>
                                <td class="px-6 py-4">{{ $request['no_of_pages'] }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-medium
                                    {{ $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $request['status'] === 'under_review' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $request['status'] === 'ongoing' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $request['status'] === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $request['status'] === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ in_array($request['status'], ['pending', 'under_review', 'ongoing', 'completed', 'rejected']) ? '' : 'bg-gray-100 text-gray-800' }}">
                                        {{ __('frontend.' . $request['status']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('translator.service.details', $request['service_request_id']) }}"
                                        class="flex items-center gap-0.5">
                                        <svg class="w-6 h-6 text-[#4D4D4D]" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-width="1.7"
                                                d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                            <path stroke="currentColor" stroke-width="1.7"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span>{{ __('frontend.view') }}</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                {{ __('frontend.no_service_requests_found') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $serviceRequests->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
