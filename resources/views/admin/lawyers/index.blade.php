@extends('layouts.admin_default', ['title' => 'All Lawyers'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Lawyers</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">

                        @can('add_lawyer')
                            <div class="action-btn">
                                <a href="{{ route('lawyers.create') }}" class="btn btn-sm btn-primary btn-add">
                                    <i class="la la-plus"></i> Add New Lawyer</a>
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

                            <form method="GET" action="{{ route('lawyers.index') }}" autocomplete="off">
                                <div class="row mb-2">
                                    <div class="col-md-4 input-group  mb-1">
                                        <label class="col-md-12 col-form-label color-dark fw-500 align-center pl-0">Search with keyword</label>
                                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="col-md-12 form-control ih-small ip-gray radius-xs b-light px-15" placeholder="Search name, email, phone or reference no.">
                                    </div>
                                    <div class="col-md-3 input-group  mb-1">
                                        <label class="col-md-12 col-form-label color-dark fw-500 align-center pl-0">Law firm</label>
                                        <select name="lawfirm_id" class="col-md-12 form-control ih-small ip-gray radius-xs b-light px-15 select2" >
                                            <option value="">-- Select Law firm --</option>
                                            @foreach ($lawfirms as $lf)
                                                <option value="{{ $lf->id }}"
                                                    {{ request('lawfirm_id') == $lf->id ? 'selected' : '' }}>
                                                    {{ $lf->law_firm_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group  mb-1">
                                        <label class="col-md-12 col-form-label color-dark fw-500 align-center pl-0">Status</label>
                                        <select name="status"
                                            class="col-md-12 form-control ih-small ip-gray radius-xs b-light px-15">
                                            <option value="">--Select Status--</option>
                                            <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-1 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('lawyers.index') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">#</th>
                                            <th class="text-center">Reference No</th>
                                            <th width="25%">Lawyer Name</th>
                                            <th width="25%">Law Firm</th>
                                            <th class="text-center">No. Of Consultations</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_lawyer')
                                            @if ($lawyers->isNotEmpty())
                                                @foreach ($lawyers as $key => $lawyer)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($lawyers->currentPage() - 1) * $lawyers->perPage() }}
                                                        </td>
                                                        <td class="text-center">{{ $lawyer->ref_no }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if ($lawyer->profile_photo)
                                                                    <img src="{{ asset(getUploadedImage($lawyer->profile_photo)) }}"
                                                                        alt="{{ $lawyer->full_name }}" class="list-avatar">
                                                                @endif
                                                                {{ $lawyer->full_name }}
                                                                <i class="fas fa-info-circle text-primary ml-2 popover-toggle"
                                                                    tabindex="0" data-toggle="popover" data-placement="bottom"
                                                                    data-html="true" data-trigger="manual"
                                                                    title='<div class="popover-title">Lawyer Contact Info</div>'
                                                                    data-content='
                                                                        <div class="custom-popover">
                                                                            <div class="popover-item"><i class="fas fa-envelope"></i> {{ $lawyer->email }}</div>
                                                                            <div class="popover-item"><i class="fas fa-phone"></i> {{ $lawyer->phone }}</div>
                                                                        </div>
                                                                    '></i>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{ $lawyer->lawfirm->law_firm_name ?? '-' }}
                                                            <i class="fas fa-info-circle text-primary ml-2 popover-toggle"
                                                                tabindex="0" data-toggle="popover" data-placement="bottom"
                                                                data-html="true" data-trigger="manual"
                                                                title='<div class="popover-title">Law firm Owner Contact Info</div>'
                                                                data-content='
                                                                        <div class="custom-popover">
                                                                            <div class="popover-item"><i class="fas fa-user"></i> {{ $lawyer->lawfirm->owner_name }}</div>
                                                                            <div class="popover-item"><i class="fas fa-envelope"></i> {{ $lawyer->lawfirm->owner_email }}</div>
                                                                            <div class="popover-item"><i class="fas fa-phone"></i> {{ $lawyer->lawfirm->owner_phone }}</div>
                                                                        </div>
                                                                    '></i>
                                                        </td>
                                                       
                                                        <td class="text-center"> 0</td>
                                                        <td class="text-center">
                                                            @can('edit_lawyer')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $lawyer->user->id }}" <?php if ($lawyer->user->banned == 0) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="custom-control-label"
                                                                            for="switch-s1_{{ $key }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endcan
                                                        </td>
                                                        <td class="text-center">
                                                            @can('edit_lawyer')
                                                                <div class="table-actions">
                                                                    <a href="{{ route('lawyers.edit', $lawyer->id) }}"
                                                                        title="Edit lawyer">
                                                                        <span data-feather="edit"></span>
                                                                    </a>
                                                                </div>
                                                            @endcan

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="10" class="text-center">
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
                                    @can('view_staff')
                                        {{ $lawyers->appends(request()->input())->links('pagination::bootstrap-5') }}
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
<!-- Bootstrap Select CSS -->
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
    <!-- Bootstrap Select JS -->
    
@endsection

@section('script')
    
    <script type="text/javascript">
        function update_status(el) {
            if (el.checked) {
                var status = 0;
            } else {
                var status = 1;
            }
            $.post('{{ route('staff.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    toastr.success('Lawyer status updated successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);

                } else {
                    toastr.error('Something went wrong');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                }
            });
        }

        $(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select options'
            });

            $('.popover-toggle').popover();

            // Show on hover/focus
            $('.popover-toggle').on('mouseenter focus', function() {
                $('.popover-toggle').not(this).popover('hide'); // hide others
                $(this).popover('show');
            });

            // Hide on mouseleave or blur only if not hovering popover
            $('.popover-toggle').on('mouseleave blur', function() {
                let _this = this;
                setTimeout(function() {
                    if (!$('.popover:hover').length) {
                        $(_this).popover('hide');
                    }
                }, 200);
            });

            // Keep popover open on hover
            $(document).on('mouseenter', '.popover', function() {
                clearTimeout(window._popoverTimeout);
            });

            $(document).on('mouseleave', '.popover', function() {
                $('[data-toggle="popover"]').popover('hide');
            });

            // Re-render Feather if used
            $(document).on('shown.bs.popover', function() {
                if (window.feather) feather.replace();
            });
        });
    </script>
@endsection
