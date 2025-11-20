@extends('layouts.admin_default', ['title' => 'All Translators'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Translators</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">

                        @can('add_translator')
                            <div class="action-btn">
                                <a href="{{ route('translators.create') }}" class="btn btn-sm btn-primary btn-add">
                                    <i class="la la-plus"></i> Add New Translator</a>
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

                            <form method="GET" action="{{ route('translators.index') }}" autocomplete="off">
                                <div class="row mb-2">
                                    <div class="col-md-3 input-group  mb-1">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15"
                                            placeholder="Search name, email, phone or reference no.">
                                    </div>
                                    <div class="col-md-3 input-group  mb-1">
                                        
                                        <select name="language_id" class="form-control select2 ih-small ip-gray radius-xs b-light px-15" id="select-tag2"  data-placeholder="Select Language" >
                                            <option value="">Select Language</option>
                                            @foreach($languages as $option)
                                                <option value="{{ $option->id }}"  {{ request('language_id') == $option->id ? 'selected' : '' }}>
                                                    {{ $option->name ?? 'Unnamed' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2 input-group  mb-1">
                                        <select name="status"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15">
                                            <option value="">Select Status</option>
                                            <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 input-group  mb-1">
                                        <select name="type"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15">
                                            <option value="">Select Type</option>
                                            <option value="inhouse" {{ request()->type == 'inhouse' ? 'selected' : '' }}>In-house</option>
                                            <option value="external" {{ request()->type == 'external' ? 'selected' : '' }}>External</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-1 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('translators.index') }}"
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
                                            <th width="25%">Name</th>
                                            <th class="text-center">Type</th>
                                            <th width="30%">Languages</th>
                                            <th class="text-center">Total Translations</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @can('view_translator')
                                            @if ($translators->isNotEmpty())
                                                @foreach ($translators as $key => $trans)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $key + 1 + ($translators->currentPage() - 1) * $translators->perPage() }}
                                                        </td>
                                                        <td class="text-center">{{ $trans->ref_no }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if ($trans->image)
                                                                    <img src="{{ asset(getUploadedImage($trans->image)) }}"
                                                                        alt="{{ $trans->name }}" class="list-avatar">
                                                                @endif
                                                                {{ $trans->name }}
                                                                <i class="fas fa-info-circle text-primary ml-2 popover-toggle"
                                                                    tabindex="0" data-toggle="popover" data-placement="bottom"
                                                                    data-html="true" data-trigger="manual"
                                                                    title='<div class="popover-title">Contact Info</div>'
                                                                    data-content='
                                                                        <div class="custom-popover">
                                                                            <div class="popover-item"><i class="fas fa-envelope"></i> {{ $trans->email }}</div>
                                                                            <div class="popover-item"><i class="fas fa-phone"></i> {{ $trans->phone }}</div>
                                                                        </div>
                                                                    '></i>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($trans->type === 'inhouse')
                                                                In-House
                                                            @elseif($trans->type === 'external')
                                                                External
                                                            @else
                                                                Unknown
                                                            @endif
                                                        </td>
                                                        {{-- <td class="text-center"> {{ $trans->email ?? 'N/A' }}</td>
                                                        <td class="text-center"> {{ $trans->phone ?? 'N/A' }}</td> --}}
                                                        <td>
                                                             @php
                                                                $grouped = $trans->languageRates
                                                                    ->groupBy('to_language_id')
                                                                    ->map(function ($group) {
                                                                        $to = $group->first()->toLanguage->name ?? '';
                                                                        $fromLanguages = $group->pluck('fromLanguage.name')->unique()->filter()->values();
                                                                        return [
                                                                            'to' => $to,
                                                                            'from_list' => $fromLanguages
                                                                        ];
                                                                    });
                                                            @endphp
                                                            @foreach ($grouped as $entry)
                                                                <strong>{{ $entry['to'] }}</strong>: {{ $entry['from_list']->implode(', ') }}<br>
                                                            @endforeach
                                                        </td>
                                                        {{-- <td>
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach ($trans->languages as $lang)
                                                                    <li> {{ $lang->getTranslatedName('en') }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </td> --}}
                                                        <td class="text-center"> 0</td>
                                                        <td class="text-center">
                                                            @can('edit_translator')
                                                                <div class="atbd-switch-wrap">
                                                                    <div
                                                                        class="custom-control custom-switch switch-secondary switch-sm ">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="switch-s1_{{ $key }}"
                                                                            onchange="update_status(this)"
                                                                            value="{{ $trans->user->id }}" <?php if ($trans->user->banned == 0) {
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
                                                                @can('edit_translator')
                                                                    <a href="{{ route('translators.edit', $trans->id) }}"
                                                                        title="Edit Translator">
                                                                        <span data-feather="edit"></span>
                                                                    </a>
                                                                @endcan

                                                                @can('view_translator_pricing')
                                                                    <a href="{{ route('translator-pricing', ['id' => base64_encode($trans->id)]) }}"
                                                                            title="Edit Translator Pricing">
                                                                        <span data-feather="credit-card"></span>
                                                                    </a>
                                                                @endcan
                                                            </div>
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
                                    @can('view_translator')
                                        {{ $translators->appends(request()->input())->links('pagination::bootstrap-5') }}
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
                    toastr.success('Translator status updated successfully');
                    // setTimeout(function() {
                    //     window.location.reload();
                    // }, 3000);

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
