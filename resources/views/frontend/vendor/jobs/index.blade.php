@extends('layouts.web_vendor_default', ['title' => __('frontend.add_job')])

@section('content')
<div class="bg-white rounded-lg p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-medium text-gray-900">{{ __('frontend.jobs') }}</h2>
        @if (isVendorCanCreateJobs())
            <a href="{{ route('jobs.create') }}" class="bg-[#07683B] text-white px-6 py-2.5 text-center rounded-full">{{ __('frontend.add_job') }}</a>
        @endif
    </div>

    <hr class="my-4 border-[#DFDFDF]" />
    <form method="GET" id="filterForm" action="{{ route('jobs.index') }}" autocomplete="off">
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
    </form>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <table class="w-full border">
            <thead class="text-md font-normal">
                <tr class="bg-[#07683B] text-white font-normal">
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.sl_no') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.ref_no') }}</th>
                    <th class="px-6 py-5 font-semibold text-start" width="25%">{{ __('frontend.title') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.posted_on') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.deadline') }}</th>
                    <th class="px-6 py-5 font-semibold text-center">{{ __('frontend.applications') }}</th>
                    <th class="px-6 py-5 font-semibold text-start">{{ __('frontend.status') }}</th>
                    <th class="px-6 py-5 font-semibold text-start">{{ __('frontend.actions') }}</th>
                </tr>
            </thead>
            <tbody class="text-[#4D4D4D]">
                @forelse ($job_posts as $key => $job)
                    <tr class="border-b even:bg-[#EEF4F1]">
                        <td class="px-6 py-4  text-center">
                            {{ $key + 1 + ($job_posts->currentPage() - 1) * $job_posts->perPage() }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $job->ref_no ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $job->getTranslation('title', app()->getLocale()) ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $job->job_posted_date ? \Carbon\Carbon::parse($job->job_posted_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $job->deadline_date ? \Carbon\Carbon::parse($job->deadline_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('jobs.applications', ['id' => base64_encode($job->id)]) }}" class="text-[#07683B]">
                                {{ $job->applications()->count() }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label for="switch-s1_{{ $key }}" class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" class="sr-only peer" id="switch-s1_{{ $key }}" onchange="update_status(this)" value="{{ $job->id }}" <?php if ($job->status == 1) { echo 'checked'; } ?>/>
                                    <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 transition-all">
                                    </div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all peer-checked:translate-x-5">
                                    </div>
                                </div>
                            </label>
                        </td>
                        <td class="px-6 py-4 flex text-center items-center gap-3">
                            <a href="{{ route('jobs.details',['id' => base64_encode($job->id)]) }}" class="flex items-center gap-0.5 text-[#4D4D4D]">
                                <svg class="w-7 h-7 mt-1" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>

                            <a href="{{ route('jobs.edit', base64_encode($job->id)) }}" class="flex items-center gap-0.5 text-[#4D4D4D]">
                                <svg class="w-5 h-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" 
                                    stroke-width="2"
                                    d="M09 7H4a2 3 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-06M16.5 3.5a2.121 2.1 0 013 3L10 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>

                            <button class="flex items-center gap-0.5 text-red-500 delete-selected" id="" data-id="{{ base64_encode($job->id) }}">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">{{ __('frontend.no_data_found') }}</td>
                    </tr>
                @endforelse
                

            </tbody>
        </table>

        <div class="mt-6">
            {{ $job_posts->appends(request()->input())->links() }}
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

        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('jobs.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success("{{ __('frontend.job_status_updated_successfully') }}");
                    // setTimeout(function() {
                    //     window.location.reload();
                    // }, 2000);

                } else {
                    toastr.error("{{ __('frontend.something_went_wrong') }}");
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                }
            });
        }

        $(document).on('click', '.delete-selected', function () {
            let jobId = this.getAttribute('data-id');

            Swal.fire({
                    title: '{{ __("frontend.are_you_sure") }}',
                    text: "{{ __('frontend.action_cannot_undone') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __("frontend.yes_delete") }}',
                    cancelButtonText: '{{ __("frontend.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('jobs.delete') }}", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                job_id: jobId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message);
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                toastr.error('Error: ' + (data.message || '{{ __("frontend.unable_delete_job") }}'));
                            }
                        })
                        .catch(err => {
                            toastr.error("{{ __('frontend.server_error') }}");
                            console.error(err);
                        });
                    }
                });
        });

    </script>
@endsection