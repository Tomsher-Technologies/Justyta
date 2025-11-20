@extends('layouts.admin_default', ['title' => 'Edit Page Details'])

@section('content')
    <div class="container-fluid">
        <div class="row mt-4 mb-4">
            <div class="col-lg-12 mx-auto">
                <div class="card card-horizontal card-default card-md">
                    <div class="card-header">
                        <h5 class="mb-0 h4">Update Content : <strong>{{ $page->name }}</strong></h5>
                    </div>
                    <div class="card-body pb-md-30">
                        <form class="form-horizontal row" autocomplete="off" action="{{ route('pages.update', $page->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @if ($page->slug === 'user_app_home')
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label color-dark fw-500">Quick Links<span class="text-danger">*</span>
                                        </label>
                                        <select name="service_id[]" multiple class="select2 form-control" data-live-search="true">
                                            <option value="">Select Service</option>
                                            @foreach($services as $serv)
                                                <option value="{{ $serv->id }}" {{ (in_array($serv->id, json_decode($page->content))) ? 'selected' : '' }}>{{ $serv->getTranslation('title', 'en') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12">
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
                                                $trans = $page->translations->firstWhere('lang', $lang->code);
                                            @endphp
                                            <div class="tab-pane fade @if ($loop->first) show active @endif"
                                                id="lang-{{ $lang->code }}" role="tabpanel"
                                                aria-labelledby="tab-{{ $lang->code }}">

                                                @if ($page->slug === 'payment-page' || $page->slug === 'consultancy_waiting_page')
                                                    <div class="form-group">
                                                        <label class="col-form-label color-dark fw-500">Title
                                                            ({{ $lang->name }})
                                                            @if ($lang->code == 'en')
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                                            name="translations[{{ $lang->code }}][title]"
                                                            class="form-control" value="{{ $trans->title ?? '' }}"
                                                            @if ($lang->code == 'en') required @endif>
                                                    </div>
                                                @endif

                                                @if ($page->slug === 'payment-page' || $page->slug === 'translation_request_success' || $page->slug === 'lawfirm_signup' || $page->slug === 'company_retainership')
                                                    <div class="form-group">
                                                        <label class="col-form-label color-dark fw-500">
                                                            @if($page->slug === 'company_retainership')
                                                                Form Info
                                                            @else
                                                                Description
                                                            @endif
                                                            
                                                            ({{ $lang->name }})
                                                            @if ($lang->code == 'en')
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <textarea name="translations[{{ $lang->code }}][description]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                            class="form-control ip-gray radius-xs b-light px-15 @error('translations.' . $lang->code . '.description') is-invalid @enderror"
                                                            rows="5" @if ($lang->code == 'en') required @endif>{{ old('translations.' . $lang->code . '.description', $trans->description ?? '') }}</textarea>

                                                        @error("translations.$lang->code.description")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endif

                                                @if ($page->slug != 'payment-page' || $page->slug != 'lawfirm_signup')
                                                    <div class="form-group">
                                                        <label class="col-form-label color-dark fw-500">Content
                                                            ({{ $lang->name }})
                                                            @if ($lang->code == 'en')
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <textarea name="translations[{{ $lang->code }}][content]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                            @if ($lang->code == 'en') required @endif class="form-control ip-gray radius-xs b-light px-15"
                                                            rows="5">{{ old('translations.' . $lang->code . '.content', $trans->content ?? '') }}</textarea>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            @endif

                            <div class="col-md-12 text-right mt-3 form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Update Content</button>
                                <a href="{{ route('pages.index') }}"
                                    class="btn btn-secondary btn-square btn-sm ml-2">Cancel</a>
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

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Loop through all tab panes
            let firstErrorTab = null;

            document.querySelectorAll('.tab-pane').forEach(function(pane) {
                // Check if this tab pane has any validation error
                if (pane.querySelector('.is-invalid')) {
                    firstErrorTab = pane;
                    return false; // break loop
                }
            });

            // If any error found, activate the corresponding tab
            if (firstErrorTab) {
                let id = firstErrorTab.id;
                let tabTrigger = document.querySelector(`a[data-toggle="tab"][href="#${id}"]`);
                if (tabTrigger) {
                    $(tabTrigger).tab('show');
                }
            }
        });
    </script>
@endsection
