@extends('layouts.admin_default', ['title' => 'Service Requests'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main row">
                    {{-- <h4 class="text-capitalize breadcrumb-title col-md-5">Service Request </h4> --}}
                    <div class="col-md-6 d-flex">
                        <div class='col-sm-6'>
                            <label class="col-form-label color-dark fw-500 align-center">Request Status</label>
                            <select id="statusSelect" class="form-control form-control-sm ip-gray radius-xs b-deep px-15" data-id="{{ $dataService['id'] }}">
                                @php
                                    $statuses = ['pending', 'ongoing', 'completed', 'rejected'];
                                @endphp
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ $dataService['status'] === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                                   
                        @if($dataService['payment_status'] != NULL)
                            <div class='col-sm-6'>
                                <label class="col-form-label color-dark fw-500 align-center">Payment Status</label>
                                <select id="paymentStatusSelect" class="form-control form-control-sm ip-gray radius-xs b-deep px-15" data-id="{{ $dataService['id'] }}">
                                    @php
                                        $paymentStatuses = ['pending','success'];
                                    @endphp
                                    @foreach($paymentStatuses as $payStatus)
                                        <option value="{{ $payStatus }}" {{ $dataService['payment_status'] === $payStatus ? 'selected' : '' }}>
                                            {{ ucfirst($payStatus) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                    </div>
                    <div class="col-md-6" style="text-align: -webkit-right;">
                        <a href="{{ Session::has('service_request_last_url') ? Session::get('service_request_last_url') : route('service-requests.index') }}" class="btn btn-sm btn-primary">← Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @php
                    use Illuminate\Support\Str;
                    $fieldLabels = [
                        'applicant_type'            => 'Applicant Type',
                        'litigation_type'           => 'Litigation Type',
                        'document_sub_type'         => 'Document Subtype',
                        'emirate_id'                => 'Emirate',
                        'case_type'                 => 'Case Type',
                        'you_represent'             => 'You Represent',
                        'about_case'                => 'About the Case',
                        'memo'                      => 'Memo',
                        'documents'                 => 'Documents',
                        'eid'                       => 'Emirates ID',
                        'trade_license'             => 'Trade License',
                        'applicant_place'           => 'Applicant Place',
                        'expert_report_type'        => 'Expert Report Type',
                        'expert_report_language'    => 'Expert Report Language',
                        'poa_type'                  => 'Power Of Attorney Type',
                        'id_number_authorized'      => 'ID Number Of Authorized',
                        'appointer_id'              => 'Appointer ID',
                        'authorized_id'             => 'Authorized ID'
                    ];
                @endphp

                <div class="row my-4">
                    {{-- Service Info --}}
                    <div class="card shadow-sm border-light mb-4 col-sm-12">
                        <div class="card-body">
                            
                            <div class="row">
                                <div class="col-sm-8">
                                    <h5 class="mb-4 text-primary font-weight-bold">Service Request Info</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-2"><strong>Service:</strong> {{ $dataService['service_name'] }}</div>
                                        <div class="col-md-6 mb-2"><strong>Reference Code:</strong> {{ $dataService['reference_code'] }}</div>
                                        <div class="col-md-6 mb-2"><strong>Status:</strong> 
                                            @php
                                                $statusClass = [
                                                    'pending'   => 'badge-gray',
                                                    'ongoing'   => 'badge-warning',
                                                    'completed' => 'badge-success',
                                                    'rejected'  => 'badge-danger',
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClass[$dataService['status']] ?? 'badge-secondary' }}">
                                                {{ ucfirst($dataService['status']) }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 mb-2"><strong>Submitted At:</strong> {{ $dataService['submitted_at'] }}</div>

                                        @if($dataService['payment_status'] != NULL)
                                            <div class="col-md-6 mb-2"><strong>Amount:</strong> AED {{ number_format($dataService['amount'], 2) }}</div>
                                            <div class="col-md-6 mb-2"><strong>Payment Status:</strong> 
                                                <span class="badge {{ ($dataService['payment_status'] == 'pending') ? 'badge-danger' : 'badge-success'}}">
                                                    {{ ($dataService['payment_status'] == 'pending') ? 'Unpaid' : 'Paid' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="card shadow-sm border-light col-sm-12 ml-2">
                                        <div class="card-body">
                                            <h5 class="mb-2 text-primary font-weight-bold">User Info</h5>
                                            <div class="row">
                                                <div class="col-md-12 mb-1"><strong>Name:</strong> {{ $dataService['user_name'] }}</div>
                                                <div class="col-md-12 mb-1"><strong>Email:</strong> {{ $dataService['user_email'] }}</div>
                                                <div class="col-md-12 mb-1"><strong>Phone:</strong> {{ $dataService['user_phone'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    

                    {{-- Service Details --}}
                    <div class="card shadow-sm border-light">
                        <div class="card-body">
                            <h5 class="mb-4 text-secondary font-weight-bold">Service Request Details</h5>
                            <div class="row">
                                @foreach($dataService['service_details'] as $key => $value)
                                    <div class="col-md-6 mb-4">
                                        <strong class="d-block mb-1 text-muted">{{ $fieldLabels[$key] ?? ucwords(str_replace('_', ' ', $key)) }}</strong>

                                        @if(is_array($value))
                                            <div class="row">
                                                @foreach($value as $index => $item)
                                                    @php $isImage = Str::endsWith($item, ['.png', '.jpg', '.jpeg', '.webp']); @endphp
                                                    <div class="col-6 col-lg-4 mb-2">
                                                        @if($isImage)
                                                            <a href="{{ $item }}" data-lightbox="gallery" data-title="Image {{ $index + 1 }}">
                                                                <img src="{{ $item }}" class="img-fluid rounded border" style="max-height: 150px;" alt="Image {{ $index + 1 }}">
                                                            </a>
                                                        @else
                                                            <a href="{{ $item }}" class="btn btn-sm btn-outline-primary w-100" target="_blank">
                                                                <i class="fas fa-file-download mr-1"></i>Download
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="p-2 rounded border">
                                                @if (Str::startsWith($value, '[') && Str::endsWith($value, ']'))
                                                    @php
                                                        $decodedValue = json_decode($value, true);
                                                    @endphp
                                                    {{ implode(', ', $decodedValue) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <!-- Lightbox2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2/dist/css/lightbox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
@endsection

@section('script')
    <!-- Lightbox2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2/dist/js/lightbox.min.js"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>

    <script type="text/javascript">
        lightbox.option({
            'resizeDuration'    : 200,
            'wrapAround'        : true,
            'disableScrolling'  : true
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $('#statusSelect').on('change', function() {

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to update the request status.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let serviceId   = $(this).data('id');
                        let status      = $('#statusSelect').val();
                        
                        $.ajax({  
                            url: "{{ route('update-service-request-status') }}", // your update route
                            type: 'POST',
                            data: {id : serviceId, status: status },
                            success: function(response) {
                                Swal.fire('Updated!', 'Request status updated successfully.', 'success');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 3000);
                            },
                            error: function() {
                                Swal.fire('Error!', 'Failed to update status.', 'error');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 3000);
                            }
                        });
                    }
                });
            });

            $('#paymentStatusSelect').on('change', function() {

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to update the payment status.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let serviceId       = $(this).data('id');
                        let paymentStatus   = $('#paymentStatusSelect').val();

                        $.ajax({
                            url: "{{ route('update-service-payment-status') }}", // your update route
                            type: 'POST',
                            data: {id : serviceId,payment_status: paymentStatus },
                            success: function(response) {
                                Swal.fire('Updated!', 'Payment status updated successfully.', 'success');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 3000);

                            },
                            error: function() {
                                Swal.fire('Error!', 'Failed to update status.', 'error');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 3000);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
