@extends('layouts.admin_default')

@section('content')
    <div class="container-fluid">
        <div class="row mt-4 mb-4">
            <div class="col-lg-10 offset-lg-1">
                <div class="card card-default card-md mb-4">
                    <div class="card-header">
                        <h6>Edit Membership Plan</h6>
                    </div>
                    <div class="card-body pb-md-30">
                        <form action="{{ route('membership-plans.update', $plan->id) }}" autocomplete="off" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <!-- Title -->
                                <div class="col-md-12 mb-3">
                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs custom-lang-tabs w-100" id="langTabs" role="tablist"
                                        style="display: flex; flex-wrap: wrap;">
                                        @foreach ($languages as $lang)
                                            <li class="nav-item flex-fill text-center">
                                                <a class="nav-link @if ($loop->first) active @endif"
                                                    id="tab-{{ $lang->code }}" data-toggle="tab"
                                                    href="#lang-{{ $lang->code }}" role="tab"
                                                    aria-controls="lang-{{ $lang->code }}"
                                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                    <span class="flag-icon flag-icon-{{ $lang->flag }} mr-1"></span>
                                                    {{ $lang->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <!-- Tab Contents -->
                                    <div class="tab-content custom-tab-content" id="langTabsContent">
                                        @foreach ($languages as $lang)
                                            @php
                                                $trans = $plan->translations->firstWhere('lang', $lang->code);
                                            @endphp
                                            <div class="tab-pane fade @if ($loop->first) show active @endif"
                                                id="lang-{{ $lang->code }}" role="tabpanel"
                                                aria-labelledby="tab-{{ $lang->code }}">
                                                <div class="form-group">
                                                    <label class="col-form-label color-dark fw-500 align-center">Title
                                                        ({{ $lang->name }})
                                                        @if ($lang->code == 'en')
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                                        name="translations[{{ $lang->code }}][title]"
                                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                        value="{{ old('translations.' . $lang->code . '.title', $trans->title ?? '') }}">
                                                    @error("translations.$lang->code.title")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                                <!-- Icon -->
                                <div class="col-md-6 mb-25">
                                    <label class="col-form-label color-dark fw-500 align-center">Icon <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="icon"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                    <img src="{{ asset(getUploadedImage($plan->icon)) }}" alt="Plan Icon" class="mt-2"
                                        style="max-width: 80px;">
                                    @error('icon')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Amount -->
                                <div class="col-md-6 mb-25">
                                    <label class="col-form-label color-dark fw-500 align-center">Amount (Plan Price/Year)
                                        <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount"
                                        value="{{ old('amount', $plan->amount) }}"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                    @error('amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Member Count -->
                                <div class="col-md-6 mb-25">
                                    <label class="col-form-label color-dark fw-500 align-center">Member Count (Max Users
                                        Access) <span class="text-danger">*</span></label>
                                    <input type="number" name="member_count"
                                        value="{{ old('member_count', $plan->member_count) }}"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                    @error('member_count')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Job Posts -->
                                <div class="col-md-6 mb-25">
                                    <label class="col-form-label color-dark fw-500 align-center">Job Posts / Month <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="job_post_count"
                                        value="{{ old('job_post_count', $plan->job_post_count) }}"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                    @error('job_post_count')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Free Ad Days -->
                                <div class="col-md-6 mb-25">
                                    <label class="col-form-label color-dark fw-500 align-center">Annual Free Advertisement
                                        Days <span class="text-danger">*</span></label>
                                    <input type="number" name="annual_free_ad_days"
                                        value="{{ old('annual_free_ad_days', $plan->annual_free_ad_days) }}"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                    @error('annual_free_ad_days')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Welcome Gift -->
                                <div class="col-md-6 mb-25">
                                    <label class="col-form-label color-dark fw-500 align-center">Welcome Gift <span
                                            class="text-danger">*</span></label>
                                    <select name="welcome_gift"
                                        class="form-select form-control ih-medium ip-gray radius-xs b-light px-15">
                                        <option value="no"
                                            {{ old('welcome_gift', $plan->welcome_gift) == 'no' ? 'selected' : '' }}>No
                                        </option>
                                        <option value="special"
                                            {{ old('welcome_gift', $plan->welcome_gift) == 'special' ? 'selected' : '' }}>
                                            Special</option>
                                        <option value="premium"
                                            {{ old('welcome_gift', $plan->welcome_gift) == 'premium' ? 'selected' : '' }}>
                                            Premium</option>
                                    </select>
                                    @error('welcome_gift')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Radios -->
                                @php
                                    $radios = [
                                        'live_online' => 'Access to Live Online Consultancy',
                                        'specific_law_firm_choice' => 'Specific Law Firm Choice',
                                        'annual_legal_contract' => 'Annual Legal Consultancy Contracts',
                                        'unlimited_training_applications' => 'Unlimited Training Applications',
                                        'is_active' => 'Active',
                                    ];
                                @endphp

                                @foreach ($radios as $field => $label)
                                    <div class="col-md-6 mb-3">
                                        <label
                                            class="col-form-label color-dark fw-500 align-center">{{ $label }}</label>
                                        <div class="radio-horizontal-list d-flex">
                                            <div class="radio-theme-default custom-radio ">
                                                <input class="radio" type="radio" name="{{ $field }}"
                                                    id="{{ $field }}_yes" value="1"
                                                    {{ old($field, $plan->$field) == '1' ? 'checked' : '' }}>
                                                <label for="{{ $field }}_yes">
                                                    <span class="radio-text">Yes</span>
                                                </label>
                                            </div>

                                            <div class="radio-theme-default custom-radio ">
                                                <input class="radio" type="radio" name="{{ $field }}"
                                                    id="{{ $field }}_no" value="0"
                                                    {{ old($field, $plan->$field) == '0' ? 'checked' : '' }}>
                                                <label for="{{ $field }}_no">
                                                    <span class="radio-text">No</span>
                                                </label>
                                            </div>
                                        </div>
                                        @error($field)
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endforeach

                                <!-- Submit -->
                                <div class="col-md-12 mb-3 d-flex flex-wrap align-items-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                    <a href="{{ route('membership-plans.index') }}"
                                        class="btn btn-secondary btn-square btn-sm ml-2">Cancel</a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/flag-icons.min.css') }}"> --}}

    <style>
        .ck-editor__editable_inline {
            min-height: 400px;
            /* or 300px, etc. */
        }

        .flag-icon {
            margin-right: 6px;
            vertical-align: middle;
        }

        .custom-lang-tabs {
            /* margin-top: 20px; */
            border-bottom: 0;
            background: #f1f1f1;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }

        .custom-lang-tabs .nav-link {
            width: 100%;
            border: none;
            background: transparent;
            color: #555;
            border-radius: 0;
            transition: background-color 0.3s ease;
            padding: 12px 0;
        }

        .custom-lang-tabs .nav-link:hover {
            background-color: #e2e6ea;
        }

        .custom-lang-tabs .nav-link.active {
            background-color: #d3be89cf;
            color: #000;
            /* border-color: #c4b07f; */
            font-weight: 500;
        }

        .custom-tab-content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            background-color: #fff;
        }
    </style>
@endsection
