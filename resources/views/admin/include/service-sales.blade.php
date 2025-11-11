<table class="table table-bordered table-basic mb-0">
    <thead>
        <tr class="userDatatable-header">
            <th class="text-center">#</th>
            <th class="text-center">Reference Code</th>
            <th class="text-center">User</th>
            @if($selectedService === 'legal-translation')
                <th class="text-center">Translator</th>
            @endif
            <th class="text-center">Amount</th>
            <th class="text-center">Payment Status</th>
            <th class="text-center">Request Status</th>
            <th class="text-center">Request Date</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @php
            if($selectedService === 'legal-translation') {
                $statusClass = [
                    'pending' => 'badge-gray',
                    'under_review' => 'badge-info',
                    'ongoing' => 'badge-warning',
                    'completed' => 'badge-success',
                    'rejected' => 'badge-danger',
                ];
            }else{
                $statusClass = [
                    'pending' => 'badge-gray',
                    'ongoing' => 'badge-warning',
                    'completed' => 'badge-success',
                    'rejected' => 'badge-danger',
                ];
            }
            
        @endphp
        @forelse($serviceRequests as $key => $serviceReq)
            <tr>
                <td class="text-center">{{ $serviceRequests->firstItem() + $key }}</td>
                <td class="text-center">{{ $serviceReq->reference_code ?? '' }}</td>
                <td>
                    {{ $serviceReq->user?->name ?? '—' }}

                    <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual"
                    title='<div class="popover-title">User Info</div>'
                    data-content='
                            <div class="custom-popover">
                                <div class="popover-item"><i class="fas fa-user"></i> {{ $serviceReq->user?->name }}</div>
                                <div class="popover-item"><i class="fas fa-envelope"></i> {{ $serviceReq->user?->email }}</div>
                                <div class="popover-item"><i class="fas fa-phone"></i> {{ $serviceReq->user?->phone }}</div>
                            </div>
                        '></i>
                </td>
                @if($selectedService === 'legal-translation')
                    <td class="text-center">
                        {{ $serviceReq->legalTranslation?->assignedTranslator?->name ?? '—' }}
                    </td>
                @endif
                <td class="text-center"><small>AED </small>{{ $serviceReq->amount ?? 0 }}</td>
                <td class="text-center">
                    @if($serviceReq->payment_status == 'success')
                        <span class="badge badge-success">Paid</span>
                    @elseif($serviceReq->payment_status == 'partial')
                        <span class="badge badge-warning">Partial</span>
                    @else
                        <span class="badge badge-danger">Unpaid</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge badge-pill {{ $statusClass[$serviceReq->status] ?? 'badge-secondary' }}">
                        {{ ucfirst($serviceReq->status) }}
                    </span>
                </td>
                <td class="text-center">{{ date('d, M Y h:i A', strtotime($serviceReq->submitted_at)) }}</td>
                <td class="text-center">
                    @if($selectedService === 'legal-translation')
                        <a href="{{ route('translation-request-details', base64_encode($serviceReq->id)) }}">
                            <span data-feather="eye"></span>
                        </a>
                    
                    @else
                        <a href="{{ route('service-request-details', base64_encode($serviceReq->id)) }}">
                            <span data-feather="eye"></span>
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center">No service requests found.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="mt-4">
    {{ $serviceRequests->appends(request()->input())->links('pagination::bootstrap-5') }}
</div>
