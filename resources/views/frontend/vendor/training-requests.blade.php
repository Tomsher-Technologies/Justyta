@extends('layouts.web_vendor_default', ['title' => __('frontend.training_requests')])

@section('content')
<div class="bg-white rounded-lg p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-medium text-gray-900">{{ __('frontend.training_requests') }}</h2>
       
    </div>

    <hr class="my-4 border-[#DFDFDF]" />
    {{-- <form method="GET" id="filterForm" action="{{ route('jobs.index') }}" autocomplete="off">
        <div class="grid grid-cols-1 md:grid-cols-12 items-end gap-4 mb-8">
            <div class="relative col-span-6">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" id="simple-search" value="{{ request('keyword') }}" name="keyword"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-3.5"
                    placeholder="{{ __('frontend.search_job_title_ref_no') }}" required />
            </div>

            <div class="col-span-3">
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900">{{ __('frontend.status') }}</label>
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                    <option value="">{{ __('frontend.choose_option') }}</option>
                    <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>{{ __('frontend.active') }} </option>
                    <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>{{ __('frontend.inactive') }}</option>
                </select>
            </div>
        </div>
    </form> --}}
    <div class="relative overflow-x-auto sm:rounded-lg">
        <table class="w-full border">
            <thead class="text-md font-normal">
                <tr class="bg-[#07683B] text-white font-normal">
                    {{-- <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.sl_no') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.user') }}</th>
                    <th class="px-6 py-5 font-semibold text-start" width="25%">{{ __('frontend.emirate') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.position') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.residency_status') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.applications') }}</th>
                    <th class="px-6 py-5 font-semibold text-start">{{ __('frontend.status') }}</th>
                    <th class="px-6 py-5 font-semibold text-start">{{ __('frontend.actions') }}</th> --}}
                    <th class="px-6 py-5 font-semibold text-center">#</th>
                    <th class="px-6 py-5 font-semibold text-center">User Name</th>
                    <th class="px-6 py-5 font-semibold text-center">Emirate</th>
                    <th class="px-6 py-5 font-semibold text-center">Position</th>
                    <th class="px-6 py-5 font-semibold text-center">Residency Status</th>
                    <th class="px-6 py-5 font-semibold text-center">Starting Date</th>
                    <th class="px-6 py-5 font-semibold text-center w-20" width="20%">Documents</th>
                    {{-- <th class="text-center">Status</th> --}}
                    <th class="px-6 py-5 font-semibold text-center">Created</th>
                </tr>
            </thead>
            <tbody class="text-[#4D4D4D]">
                @php
                    $i = 0;
                @endphp
                @forelse($requests as $key => $req)
                    @php
                        $statusClass = [
                            'pending' => 'badge-gray',
                            'selected' => 'badge-success',
                            'rejected' => 'badge-danger',
                        ];
                    @endphp
                    <tr class="border-b even:bg-[#EEF4F1]">
                        <td class="px-6 py-4  text-center">
                            {{ $key + 1 + ($requests->currentPage() - 1) * $requests->perPage() }}
                        </td>
                       
                        <td class="text-center">
                            {{ $req->user?->name ?? ''}}

                            <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual"
                            title='<div class="popover-title">User Info</div>'
                            data-content='
                                    <div class="custom-popover">
                                        <div class="popover-item"><i class="fas fa-user"></i> {{ $req->user?->name }}</div>
                                        <div class="popover-item"><i class="fas fa-envelope"></i> {{ $req->user?->email }}</div>
                                        <div class="popover-item"><i class="fas fa-phone"></i> {{ $req->user?->phone }}</div>
                                    </div>
                                '></i>

                        </td>
                        <td>
                            {{ $req->emirate?->name }}
                        </td>

                        <td class="text-center">
                            {{ $req->positionOption?->getTranslation('name'); }}
                        </td>

                        <td class="text-center">
                            {{ $req->residencyStatusOption?->getTranslation('name'); }}
                        </td>

                        <td class="text-center">
                            {{ date('d, M Y', strtotime($req->start_date)) }}
                        </td>
                        
                            <td style="width: 25%">
                            @php
                                $documents = $req->documents;
                            @endphp
                            @foreach($documents as $file)
                                @php
                                    $i++;
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                @endphp

                                @if(in_array($ext, ['png', 'jpg', 'jpeg', 'webp']))
                                    <a href="{{ asset(getUploadedImage($file)) }}" data-lightbox="image{{ $i }}" target="_blank">
                                        <img src="{{ asset(getUploadedImage($file)) }}"  alt="doc" width="50" class="mb-1" />
                                    </a>
                                @elseif($ext == 'pdf')
                                    <a href="{{ asset(getUploadedImage($file)) }}" target="_blank" class="d-inline-block text-danger mr-2">
                                        <i class="fas fa-file-pdf fa-2x"></i>
                                    </a>
                                @elseif(in_array($ext, ['doc', 'docx']))
                                    <a href="{{ asset(getUploadedImage($file)) }}" target="_blank" class="d-inline-block text-primary mr-2">
                                        <i class="fas fa-file-word fa-2x"></i>
                                    </a>
                                @else
                                    <a href="{{ asset(getUploadedImage($file)) }}" target="_blank" class="d-inline-block text-secondary mr-2">
                                        <i class="fas fa-file fa-2x"></i>
                                    </a>
                                @endif
                            @endforeach
                        </td>
                        
                        {{-- <td class="text-center">
                            <span class="badge badge-pill {{ $statusClass[$req->status] ?? 'badge-secondary' }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td> --}}

                        <td class="text-center">{{ date('d, M Y h:i A', strtotime($req->created_at)) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">{{ __('frontend.no_data_found') }}</td>
                    </tr>
                @endforelse
                

            </tbody>
        </table>

        <div class="mt-6">
            {{ $requests->appends(request()->input())->links() }}
        </div>
        
    </div>
</div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
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