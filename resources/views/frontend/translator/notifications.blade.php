@extends('layouts.web_translator', ['title' => __('frontend.notifications')])

@section('content')
    <div class="container">
        <h2 class="text-xl font-bold mb-4">{{ __('frontend.all_notifications') }}</h2>
        <div class="bg-white rounded-lg p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-medium text-gray-900">{{ __('frontend.notifications') }}</h2>
                <div class="text-end mt-4 flex items-center gap-4">
                    <button id="delete-selected" class="bg-red-600 text-white p-4 py-1.5 rounded-md text-sm hover:bg-red-700">
                        {{ __('frontend.delete_selected') }}
                    </button>

                    <button class="bg-red-500/20 text-red-500 p-4 py-1.5 rounded-md text-sm"
                        id="delete-all">{{ __('frontend.clear_all') }}</button>
                </div>
            </div>


            <hr class="my-4 border-[#DFDFDF]">


            <div class="relative overflow-x-auto sm:rounded-lg w-[240px] xl:w-full">
                <table class="w-full border">
                    <thead class="text-md font-normal">
                        <tr class="bg-[#07683B] text-white font-normal">
                            <th scope="col" class="px-6 py-5 font-semibold text-start">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 text-[#cdb57a] bg-gray-100 border-gray-300 rounded-sm focus:ring-[#cdb57a] focus:ring-2">
                            </th>
                            <th scope="col" class="px-6 py-5 font-semibold text-start">#</th>
                            <th scope="col" class="px-6 py-5 font-semibold text-start">{{ __('frontend.message') }}</th>
                            <th scope="col" class="px-6 py-5 font-semibold text-start">{{ __('frontend.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $key => $notification)
                            <tr class="border-b text-[#4D4D4D]">
                                <td class="px-6 py-4">
                                    <input type="checkbox"
                                        class="notif-checkbox w-4 h-4 text-[#cdb57a] bg-gray-100 border-gray-300 rounded-sm focus:ring-[#cdb57a] focus:ring-2"
                                        value="{{ $notification['id'] }}">
                                </td>
                                <td class="px-6 py-4">
                                    {{ $key + 1 + ($paginatedNot->currentPage() - 1) * $paginatedNot->perPage() }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $notification['message'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $notification['time'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="items-center text-center p-12">
                                    {{ __('frontend.no_details_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
        <div class="mt-6">
            {{ $paginatedNot->links() }}
        </div>
    </div>
@endsection


@section('ads')
    @php
        $ads = getActiveAd('notifications', 'web');
    @endphp

    @if ($ads && $ads->files->isNotEmpty())
        <div class="relative w-full mb-12 px-[50px]">
            @php
                $file = $ads->files->first();
            @endphp

            <a href="{{ $ads->cta_url ?? '#' }}" target="_blank" title="{{ $ads->cta_text ?? 'View More' }}">
                @if($file->file_type === 'video')
                    <video id="adVideo{{ $ads->id }}" class="w-full object-cover"  style="height: 500px;" autoplay muted loop playsinline>
                        <source src="{{ asset($file->file_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ asset($file->file_path) }}" class="w-full h-80 object-cover" alt="Ad Image">
                @endif
            </a>

            @if($file->file_type === 'video')
                <button 
                    onclick="toggleMute('adVideo{{ $ads->id }}', this)" 
                    class="absolute bottom-2 bg-gray-800 bg-opacity-50 text-white px-3 py-1 rounded hover:bg-opacity-80 z-10" style="right: 4rem;">
                    🔇
                </button>
            @endif
        </div>
    @endif
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
@endsection

@section('script')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.notif-checkbox').forEach(cb => cb.checked = this.checked);
        });

        document.querySelectorAll('.notif-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked) {
                    document.getElementById('select-all').checked = false;
                } else {
                    const allChecked = Array.from(document.querySelectorAll('.notif-checkbox')).every(cb =>
                        cb.checked);
                    document.getElementById('select-all').checked = allChecked;
                }
            });
        });

        document.getElementById('delete-selected').addEventListener('click', function() {
            let selectedIds = [];
            document.querySelectorAll('.notif-checkbox:checked').forEach(cb => {
                selectedIds.push(cb.value);
            });

            if (selectedIds.length === 0) {
                toastr.error('{{ __('frontend.select_notifications') }}');
                return;
            }

            Swal.fire({
                title: '{{ __('frontend.are_you_sure') }}',
                text: "{{ __('frontend.action_cannot_undone') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('frontend.yes_delete') }}',
                cancelButtonText: '{{ __('frontend.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('translator.notifications.delete.selected') }}", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                notification_ids: selectedIds
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
                                toastr.error('Error: ' + (data.message ||
                                    '{{ __('frontend.unable_delete_notifications') }}'));
                            }
                        })
                        .catch(err => {
                            toastr.error("{{ __('frontend.server_error') }}");
                            console.error(err);
                        });
                }
            });
        });

        document.getElementById('delete-all').addEventListener('click', function() {

            Swal.fire({
                title: '{{ __('frontend.are_you_sure') }}',
                text: "{{ __('frontend.action_cannot_undone') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('frontend.yes_delete') }}',
                cancelButtonText: '{{ __('frontend.cancel') }}'
            }).then((result) => {

                if (result.isConfirmed) {
                    fetch("{{ route('translator.notifications.clear') }}", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            },
                            body: ''
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message);
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                toastr.error('Error: ' + (data.message ||
                                    '{{ __('frontend.unable_delete_notifications') }}'));
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
