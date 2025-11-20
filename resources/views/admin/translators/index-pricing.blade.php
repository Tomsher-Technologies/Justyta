@extends('layouts.admin_default', ['title' => 'Translator Pricing'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Translator Pricing - {{ $translator->user->name ?? '' }}</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">

                        @can('add_translator_pricing')
                            <div class="action-btn d-flex">
                                <a href="{{ route('translator-pricing.create',['id' => base64_encode($translatorId)]) }}" class="btn btn-sm btn-primary btn-add">
                                    <i class="la la-plus"></i> Add New Pricing</a>
                                
                                <a href="{{ Session::has('translator_last_url') ? Session::get('translator_last_url') : route('translators.index') }}" class="btn btn-sm btn-secondary ml-2">← Back</a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table4  bg-white mb-30">

                            <form method="GET" action="{{ route('translator-pricing',['id' => base64_encode($translatorId)]) }}" autocomplete="off">
                                <div class="row mb-2">
                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="from_language_id" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" id="select-tag3"  data-placeholder="From Language" >
                                            <option value="">From Language</option>
                                            @foreach($languages as $option)
                                                <option value="{{ $option->id }}"  {{ request('from_language_id') == $option->id ? 'selected' : '' }}>
                                                    {{ $option->name ?? 'Unnamed' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="to_language_id" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" id="select-tag2"  data-placeholder="To Language" >
                                            <option value="">To Language</option>
                                            @foreach ($languages->whereIn('id', [1, 3]) as $lang)
                                                <option value="{{ $lang->id }}"  {{ request('to_language_id') == $lang->id ? 'selected' : '' }}>
                                                    {{ $lang->name ?? 'Unnamed' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="doc_type_id" id="doc_type_id" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" data-placeholder="Document Type">
                                            <option value="">Select</option>
                                            @foreach ($documentTypes as $doctype)
                                                <option value="{{ $doctype->id }}" {{ request('doc_type_id') == $doctype->id ? 'selected' : '' }}>
                                                    {{ $doctype->getTranslation('name','en')}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <select name="doc_subtype_id" id="doc_subtype_id" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" data-placeholder="Document Subtype">
                                            <option value="">Select</option>
                                            
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group mt-2 mb-1">
                                        <select name="status"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15">
                                            <option value="">Select Status</option>
                                            <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>

            

                                    <div class="col-md-3 mb-1 d-flex flex-wrap align-items-end mt-2">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('translator-pricing',['id' => base64_encode($translatorId)]) }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">#</th>
                                            <th class="text-center">From Language</th>
                                            <th class="text-center">To Langugae</th>
                                            <th class="text-center">Document Type</th>
                                            <th class="text-center">Document Subtype</th>
                                            <th class="text-center">Total Amount (AED)</th>
                                            <th class="text-left">Page Hours</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_translator_pricing')
                                            @if ($translatorPricing->isNotEmpty())
                                                @foreach ($translatorPricing as $key => $trans)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($translatorPricing->currentPage() - 1) * $translatorPricing->perPage() }}
                                                        </td>
                                                        <td class="text-center"> 
                                                            {{ $trans->fromLanguage->name ?? '-' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $trans->toLanguage->name ?? '-' }}
                                                        </td>
                                                        <td class="text-center"> 
                                                            {{ $trans->documentType->name ?? '-' }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $trans->documentSubType->name ?? '-' }}
                                                        </td>

                                                        <td class="text-center">
                                                            @foreach ($trans->deliveries as $delivery)
                                                                <small>
                                                                    {{ ucfirst($delivery->priority_type) }} - {{ ucfirst($delivery->delivery_type) }}:
                                                                </small>
                                                                    {{ number_format($delivery->total_amount, 2) }}

                                                                <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual" title='<div class="popover-title">Pricing Details (AED)</div>' data-content=' <div class="custom-popover">
                                                                        <div class="popover-item"><strong> {{ ucfirst($delivery->priority_type) }} - {{ ucfirst($delivery->delivery_type) }}</strong>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="popover-item">Delivery Amount : {{ number_format($delivery->delivery_amount, 2) }}</div>
                                                                        <div class="popover-item">Admin Amount : {{ number_format($delivery->admin_amount, 2) }}</div>
                                                                        <div class="popover-item">Translator Amount : {{ number_format($delivery->translator_amount, 2) }}</div>
                                                                        <div class="popover-item">Tax Amount(5%) : {{ number_format($delivery->tax, 2) }}</div>
                                                                        <div class="popover-item"><strong>Total Amount : {{ number_format($delivery->total_amount, 2) }}</strong></div>
                                                                    </div>'>
                                                                </i>
                                                                <br>
                                                            @endforeach
                                                        </td>
                                                        
                                                        <td class="text-left"> 
                                                            Normal Priority
                                                            <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual" title='<div class="popover-title">Normal Priority (Hours)</div>' data-content=' <div class="custom-popover">
                                                                   
                                                                    <div class="popover-item">Pages 1-10 : {{ number_format($trans->normal_hours_1_10, 1) }}</div>
                                                                    <div class="popover-item">Pages 11-20 : {{ number_format($trans->normal_hours_11_20, 1) }}</div>
                                                                    <div class="popover-item">Pages 21-30 : {{ number_format($trans->normal_hours_21_30, 1) }}</div>
                                                                    <div class="popover-item">Pages 31-50 : {{ number_format($trans->normal_hours_31_50, 1) }}</div>
                                                                    <div class="popover-item">Pages 50+ : {{ number_format($trans->normal_hours_above_50, 1) }}</div>
                                                                </div>'>
                                                            </i>

                                                            <br>
                                                            Urgent Priority
                                                            <i class="fas fa-info-circle text-primary ml-2 popover-toggle" tabindex="0" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="manual" title='<div class="popover-title">Urgent Priority (Hours)</div>' data-content=' <div class="custom-popover">
                                                                   
                                                                    <div class="popover-item">Pages 1-10 : {{ number_format($trans->urgent_hours_1_10, 1) }}</div>
                                                                    <div class="popover-item">Pages 11-20 : {{ number_format($trans->urgent_hours_11_20, 1) }}</div>
                                                                    <div class="popover-item">Pages 21-30 : {{ number_format($trans->urgent_hours_21_30, 1) }}</div>
                                                                    <div class="popover-item">Pages 31-50 : {{ number_format($trans->urgent_hours_31_50, 1) }}</div>
                                                                    <div class="popover-item">Pages 50+ : {{ number_format($trans->urgent_hours_above_50, 1) }}</div>
                                                                </div>'>
                                                            </i>

                                                        </td>
                                                        <td class="text-center">
                                                            @can('edit_translator_pricing')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $trans->id }}" <?php if ($trans->status == 1) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="custom-control-label"
                                                                            for="switch-s1_{{ $key }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endcan
                                                        </td>

                                                        <td class="text-center">

                                                            <div class="table-actions">
                                                                @can('edit_translator_pricing')
                                                                    <a href="{{ route('translator-pricing.edit', ['id' => base64_encode($trans->id), 'transId' => base64_encode($trans->translator_id)]) }}" title="Edit Translator Pricing">
                                                                        <span data-feather="edit"></span>
                                                                    </a>
                                                                @endcan

                                                                @can('delete_translator_pricing')
                                                                    <form id="delete-form-{{ $trans->id }}"
                                                                        action="{{ route('translator-pricing.destroy', $trans->id) }}"
                                                                        method="POST" style="display:none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>

                                                                    <a href="javascript:void(0)"
                                                                        onclick="confirmDelete({{ $trans->id }})"
                                                                        title="Delete Translator Pricing">
                                                                        <span data-feather="trash-2"></span>
                                                                    </a>
                                                                @endcan
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        <div class="atbd-empty__image">
                                                            <img src="{{ asset('assets/img/svg/1.svg') }}" alt="Empty">
                                                        </div>
                                                        <div class="atbd-empty__text">
                                                            <p class="">No Data Available</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endcan
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    @can('view_translator_pricing')
                                        {{ $translatorPricing->appends(request()->input())->links('pagination::bootstrap-5') }}
                                    @endcan
                                </div>
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
    <style>
        .popover-header {
            background-color: var(--secondary);
            /*#e2d8bf*/
            font-size: 13px;
        }

        .popover {
            background-color: #ffffff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            min-width: 200px;
        }

        .custom-popover {
            font-size: 14px;
            color: #333;
        }

        .popover-title {
            font-weight: 700;
            /* margin-bottom: 8px; */
            color: var(--primary);
            /* border-bottom: 1px solid #e9ecef;
                                                                                                padding-bottom: 4px; */
        }

        .custom-popover .popover-item i {
            color: var(--primary);
            margin-right: 8px;
        }
    </style>
@endsection

@section('script_first')
    <script src="{{ asset('assets/js/bootstrap/popper.js') }}"></script>
@endsection

@section('script')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('translator-pricing.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Translator pricing status updated successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    toastr.error('Something went wrong');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                }
            });
        }

        $(function() {
            $('.popover-toggle').popover();

            $('.popover-toggle').on('mouseenter focus', function() {
                $('.popover-toggle').not(this).popover('hide'); 
                $(this).popover('show');
            });

            $('.popover-toggle').on('mouseleave blur', function() {
                let _this = this;
                setTimeout(function() {
                    if (!$('.popover:hover').length) {
                        $(_this).popover('hide');
                    }
                }, 200);
            });

            $(document).on('mouseenter', '.popover', function() {
                clearTimeout(window._popoverTimeout);
            });

            $(document).on('mouseleave', '.popover', function() {
                $('[data-toggle="popover"]').popover('hide');
            });

            $(document).on('shown.bs.popover', function() {
                if (window.feather) feather.replace();
            });
        });

        function confirmDelete(id) {
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
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        const checkUrl = "{{ route('get-sub-doc-types', ['docTypeId' => '__docType__']) }}";

        function loadSubDocTypes(docTypeId, selectedSubTypeId = null) {
            if (!docTypeId) return;
            const routeUrl = checkUrl.replace('__docType__', docTypeId);

            $.ajax({
                url: routeUrl,
                method: 'GET',
                success: function (response) {
                    let subDocSelect = $('#doc_subtype_id');
                    subDocSelect.empty().append('<option value="">{{ __("frontend.choose_option") }}</option>');

                    let data = response.data;
                    data.forEach(function (sub) {
                        let selected = (selectedSubTypeId == sub.id) ? 'selected' : '';
                        subDocSelect.append(`<option value="${sub.id}" ${selected}>${sub.value}</option>`);
                    });
                }
            });
        }
        
        $('#doc_type_id').on('change', function () {
            loadSubDocTypes($(this).val());
        });

        let oldDocType = '{{ request("doc_type_id") }}';
        let oldSubDocType = '{{ request("doc_subtype_id") }}';

        if (oldDocType) {
            loadSubDocTypes(oldDocType, oldSubDocType);
        }

    </script>
@endsection
