@extends('layouts.admin_default', ['title' => 'Add FAQ'])

@section('content')
    <div class="container-fluid">
        <div class="row mt-4 mb-4">
            <div class="col-lg-12 mx-auto">
                <div class="card card-horizontal card-default card-md mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 h4">Add New FAQ</h5>
                    </div>
                    <div class="card-body pb-md-30">
                        <form class="form-horizontal row" autocomplete="off" action="{{ route('faqs.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                           <div class="col-md-6 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">Sort Order</label>
                                <input type="number" name="sort_order" id="typeSort" value="{{ old('sort_order') }}"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">Status</label>
                                <select name="status" id="typeStatus"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15">
                                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-12 ">
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
                                        <div class="tab-pane fade @if ($loop->first) show active @endif"
                                            id="lang-{{ $lang->code }}" role="tabpanel"
                                            aria-labelledby="tab-{{ $lang->code }}">
                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500 align-center">Question
                                                    ({{ $lang->name }})
                                                    @if ($lang->code == 'en')
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    name="translations[{{ $lang->code }}][question]"
                                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                    value="{{ old('translations.' . $lang->code . '.question', '') }}">
                                                @error("translations.$lang->code.question")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500 align-center">Answer
                                                    ({{ $lang->name }})
                                                    @if ($lang->code == 'en')
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $lang->code }}][answer]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    class="texteditor form-control ip-gray radius-xs b-light px-15 @error('translations.' . $lang->code . '.answer') is-invalid @enderror"
                                                    rows="10">{{ old('translations.' . $lang->code . '.answer', '') }}</textarea>

                                                @error("translations.$lang->code.answer")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    @endforeach

                                </div>
                            </div>

                            <div class="col-md-12 text-right mt-4 form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ route('faqs.index') }}"
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

@section('script')
    <script src="{{ asset('assets/js/tinymce/tinymce.min.js') }}"></script>

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

            // Initialize CKEditor on all .texteditor fields
            document.querySelectorAll('.texteditor').forEach(function(el) {
                tinymce.init({
                    target: el,
                    directionality: el.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr',
                    height: 400,
                    license_key: 'gpl',
                    toolbar: 'undo redo | bold italic underline removeformat | alignleft aligncenter alignright | link | bullist numlist | outdent indent | blockquote | table | code preview',
                    plugins: 'preview directionality code lists link table advlist',
                    menubar: true,
                    // Optional: remove status bar for a cleaner look
                    statusbar: true,
                    // Accept GPL license to remove evaluation warning:
                
                });
            });

        });

       
    </script>
@endsection
