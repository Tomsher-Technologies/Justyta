@extends('layouts.admin_default', ['title' => 'Online Consultation Requests'])

@section('content')

<div class="card">
    
    <div class="card-body">
    
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header  text-white d-flex justify-content-between align-items-center" style="background: #07683b">
                <h5 class="mb-0 text-white"><i class="fas fa-file-alt mr-2"></i> Consultation Details</h5>
                <a href="{{ Session::has('last_page_consultations') ? Session::get('last_page_consultations') : route('consultations.index') }}" class="btn btn-sm btn-secondary text-white">← Back</a>
            </div>

            <div class="card-body">
                <div class="row">
                    @php
                        $statusClass = [
                                        'reserved' => ['bg' => '#808080', 'text' => '#ffffff'],
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

                        $status = $consultation->status ?? 'reserved';
                        $bgColor = $statusClass[$status]['bg'] ?? '#e0e0e0';
                        $textColor = $statusClass[$status]['text'] ?? '#000000';
                    @endphp

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-info-circle mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Status</small>
                                <div>
                                    <span class="badge " style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                        {{ ucwords(str_replace('_', ' ', $status)) ?? ucwords($status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-user text-primary mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">User</small>
                                <div>{{ $consultation->user?->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-balance-scale text-info mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Lawyer</small>
                                <div>{{ $consultation->lawyer?->full_name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-id-badge text-secondary mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Applicant Type</small>
                                <div>{{ ucfirst($consultation->applicant_type ?? '-') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-balance-scale-right text-warning mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Litigation Type</small>
                                <div>{{ ucfirst($consultation->litigation_type ?? '-') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-user-tie text-success mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Consultant Type</small>
                                <div>{{ ucfirst($consultation->consultant_type ?? '-') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-gavel text-danger mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Case Type</small>
                                <div>{{ $consultation->caseType?->getTranslation('name', 'en') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-layer-group text-secondary mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Case Stage</small>
                                <div>{{ $consultation->caseStage?->getTranslation('name', 'en') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-user-check text-primary mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">You Represent</small>
                                <div>{{ $consultation->youRepresent?->getTranslation('name', 'en') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-language text-primary mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Language</small>
                                <div>{{ ucfirst($consultation->languageValue?->getTranslation('name', 'en') ?? '-') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-city text-warning mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Emirate</small>
                                <div>{{ $consultation->emirate?->getTranslation('name', 'en') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-clock text-info mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Duration</small>
                                <div>{{ $consultation->duration }} mins</div>
                            </div>
                        </div>
                    </div>

                    

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-dollar-sign text-success mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Total Amount</small>
                                <div>AED {{ number_format($consultation->amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-dollar-sign text-success mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Admin Amount</small>
                                <div>AED {{ number_format($consultation->admin_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-dollar-sign text-success mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Lawyer Amount</small>
                                <div>AED {{ number_format($consultation->lawyer_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="far fa-calendar-alt text-info mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Meeting Start</small>
                                <div>{{ $consultation->meeting_start_time ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="far fa-calendar-check text-success mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Meeting End</small>
                                <div>{{ $consultation->meeting_end_time ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    

                    {{-- <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                            <i class="fas fa-calendar text-secondary mr-3 fa-lg"></i>
                            <div>
                                <small class="text-muted">Created At</small>
                                <div>{{ $consultation->created_at?->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>


        <div class="table4  table-responsive">
            <h5 class="mt-4">Assignment History</h5>
            <table class="table table-bordered table-basic mt-2">
                <thead>
                    <tr class="userDatatable-header">
                        <th class="text-center">Sl No.</th>
                        <th class="text-center">Lawyer</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Assigned At</th>
                        <th class="text-center">Responded At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultation->assignments as $assign)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $assign->lawyer?->full_name }}</td>
                            <td class="text-center">{{ ucfirst($assign->status) }}</td>
                            <td class="text-center">{{ $assign->assigned_at ? \Carbon\Carbon::parse($assign->assigned_at)->format('d-M-Y h:i A') : '-' }}</td>
                            <td class="text-center">{{ $assign->responded_at ? \Carbon\Carbon::parse($assign->responded_at)->format('d-M-Y h:i A') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="table4  table-responsive">
            <h5 class="mt-4">Payment History</h5>
            <table class="table table-bordered table-basic mt-2">
                <thead>
                    <tr class="userDatatable-header">
                        <th class="text-center">Sl No.</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Duration</th>
                        <th class="text-center">Amount</th>
                        {{-- <th>Reference</th> --}}
                        <th class="text-center">Status</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultation->payments as $payment)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ ucfirst($payment->type) }}</td>
                            <td class="text-center">{{ $payment->duration }}</td>
                            <td class="text-center">{{ number_format($payment->amount, 2) }}</td>
                            {{-- <td>{{ $payment->payment_reference ?? '-' }}</td> --}}
                            <td class="text-center">
                                <span class="badge badge-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No payment history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection