@extends('layouts.admin_default', ['title' => 'All Notifications'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Notifications</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table4  bg-white mb-30">
                            <button id="delete-selected" class="btn btn-xs btn-danger mb-3">Delete Selected</button>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th class="text-center">Sl No.</th>
                                        <th>Message</th>
                                        <th class="text-center">Received At</th>
                                    </tr>
                                </thead>
                                <tbody style="color:#000;">
                                    @forelse($notifications as $key => $notification)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="notif-checkbox" value="{{ $notification->id }}">
                                            </td>
                                            <td class="text-center">{{ $key + 1 + ($notifications->currentPage() - 1) * $notifications->perPage() }}</td>
                                            <td>{{ $notification->data['message'] ?? '-' }}</td>
                                            
                                            <td class="text-center">{{ $notification->created_at->format('d, M Y h:i A') }}</td>
                                            
                                        </tr>
                                    @empty
                                        <tr><td colspan="5">No notifications found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                           
                            <div>
                                {{ $notifications->appends(request()->input())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
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
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.notif-checkbox').forEach(cb => cb.checked = this.checked);
        });

        document.getElementById('delete-selected').addEventListener('click', function () {
            let selectedIds = [];
            document.querySelectorAll('.notif-checkbox:checked').forEach(cb => {
                selectedIds.push(cb.value);
            });

            if (selectedIds.length === 0) {
                toastr.error('Please select at least one notification.');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('notifications.bulkDelete') }}", {
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
                            location.reload();
                        } else {
                            toastr.error('Error: ' + (data.message || 'Unable to delete notifications.'));
                        }
                    })
                    .catch(err => {
                        toastr.error('Server error.');
                        console.error(err);
                    });
                }
            });
        });
    </script>
@endsection