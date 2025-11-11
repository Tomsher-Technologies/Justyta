@php
    $statusClass = [
                // 'reserved' => ['bg' => '#808080', 'text' => '#ffffff'],
                'waiting_lawyer' => ['bg' => '#ADD8E6', 'text' => '#000000'],
                'assigned' => ['bg' => '#FFF44F', 'text' => '#000000'],
                'accepted' => ['bg' => '#90EE90', 'text' => '#000000'],
                'rejected' => ['bg' => '#FF0000', 'text' => '#ffffff'],
                'completed' => ['bg' => '#008000', 'text' => '#ffffff'],
                'cancelled' => ['bg' => '#A52A2A', 'text' => '#ffffff'],
                'no_lawyer_available' => ['bg' => '#FFA07A', 'text' => '#000000'],
                'in_progress' => ['bg' => '#FFD580', 'text' => '#000000'],
                'on_hold' => ['bg' => '#FFA500', 'text' => '#000000'],
            ];
@endphp
<div class="d-flex flex-wrap mt-3">
    @foreach($statusClass as $status => $colors)
        <div class="d-flex align-items-center mr-4 mb-2">
            <div style="width:17px;
                        height:14px;
                        border-radius:15%;
                        background-color: {{ $colors['bg'] }};
                        border:1px solid #999;
                        margin-right:6px;"></div>
            <span style="font-size:13px; color:#444;">
                {{ ucfirst(str_replace('_', ' ', $status)) }}
            </span>
        </div>
    @endforeach
</div>

<table class="table table-bordered table-basic mb-0">
    <thead class="userDatatable-header">
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">Reference Code</th>
            <th class="text-center">User</th>
            <th class="text-center">Lawyer</th>
            <th class="text-center">Consultation Type</th>
            <th class="text-center">Status</th>
            <th class="text-center">Total Duration</th>
            <th class="text-center">Total Amount</th>
            <th class="text-center">Consultation Date</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        
        @forelse($consultations as $key => $consultation)
            <tr>
                <td class="text-center">{{ $consultations->firstItem() + $key }}</td>
                <td class="text-center">{{ $consultation->ref_code ?? '-' }}</td>
                <td class="text-center">
                    {{ $consultation->user?->name ?? '—' }}

                    <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual"
                    title='<div class="popover-title">User Info</div>'
                    data-content='
                            <div class="custom-popover">
                                <div class="popover-item"><i class="fas fa-user"></i> {{ $consultation->user?->name }}</div>
                                <div class="popover-item"><i class="fas fa-envelope"></i> {{ $consultation->user?->email }}</div>
                                <div class="popover-item"><i class="fas fa-phone"></i> {{ $consultation->user?->phone }}</div>
                            </div>
                        '></i>
                </td>
                <td class="text-center">{{ $consultation->lawyer?->full_name ?? '-' }}</td>
                <td class="text-center">{{ ucfirst($consultation->consultant_type) ?? '-' }}</td>
                <td class="text-center">
                    @php
                        $status = $consultation->status ?? '';
                        $bgColor = $statusClass[$status]['bg'] ?? '#e0e0e0';
                        $textColor = $statusClass[$status]['text'] ?? '#000000';
                    @endphp
                    <span class="badge " style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ ucwords(str_replace('_', ' ', $status)) ?? ucwords($status) }}
                    </span>
                </td>
                <td class="text-center">{{ $consultation->duration ?? 0 }} <small>Mins</small></td>
                <td class="text-center"><small>AED</small> {{ number_format($consultation->amount, 2) }}</td>
                <td class="text-center">{{ date('d, M Y h:i A', strtotime($consultation->created_at)) }}</td>
                <td class="text-center">
                    <a href="{{ route('consultations.show', $consultation->id) }}">
                        <span data-feather="eye"></span>
                    </a>
                </td>
            </tr>
        @empty
            <tr><td colspan="10" class="text-center">No consultations found.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="mt-4">
    {{ $consultations->appends(request()->input())->links('pagination::bootstrap-5') }}
</div>
