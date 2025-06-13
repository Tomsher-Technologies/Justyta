@extends('layouts.admin_default', ['title' => 'Add News'])

@section('content')
    <div class="container-fluid">
        <div class="row mt-4 mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="card card-horizontal card-default card-md mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 h4">Add News</h5>
                    </div>
                    <div class="card-body pb-md-30">
                        <form class="form-horizontal row" autocomplete="off" action="{{ route('news.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="col-md-6 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">Image <span
                                        class="text-danger">*</span></label>
                                <input type="file" name="image" accept="image/*"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                    value="{{ old('image') }}" onchange="previewImage(event)" />

                                <img id="imagePreview" src="#" alt="Image Preview" class="mt-3 img-thumbnail d-none"
                                    style="max-height: 150px;" />
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="col-form-label color-dark fw-500 align-center">News Date <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="news_date"
                                    class="form-control ih-small ip-gray radius-xs b-light px-15 alldatepicker"
                                    value="{{ old('news_date') }}" />
                                @error('news_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-3">
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
                                                <label class="col-form-label color-dark fw-500 align-center">Title
                                                    ({{ $lang->name }})
                                                    @if ($lang->code == 'en')
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    name="translations[{{ $lang->code }}][title]"
                                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                    value="{{ old('translations.' . $lang->code . '.title', '') }}">
                                                @error("translations.$lang->code.title")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500 align-center">Description
                                                    ({{ $lang->name }})
                                                    @if ($lang->code == 'en')
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $lang->code }}][description]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    class="texteditor form-control ip-gray radius-xs b-light px-15 @error('translations.' . $lang->code . '.description') is-invalid @enderror"
                                                    rows="10">{{ old('translations.' . $lang->code . '.description', '') }}</textarea>

                                                @error("translations.$lang->code.description")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>


                                            <div class="form-group mt-5">
                                                <h6>SEO Details</h6>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500 align-center">Meta Title
                                                    ({{ $lang->name }})
                                                </label>
                                                <input type="text" name="translations[{{ $lang->code }}][meta_title]"
                                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                    @if ($lang->rtl == 1) dir="rtl" @endif
                                                    value="{{ old('translations.' . $lang->code . '.meta_title', '') }}">
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500">Meta Description
                                                    ({{ $lang->name }})
                                                </label>
                                                <textarea name="translations[{{ $lang->code }}][meta_description]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    class="form-control ip-gray radius-xs b-light px-15" rows="5">{{ old('translations.' . $lang->code . '.meta_description') }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500 align-center">Meta Keywords
                                                    ({{ $lang->name }})
                                                </label>
                                                <textarea name="translations[{{ $lang->code }}][meta_keywords]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    class="form-control ip-gray radius-xs b-light px-15" rows="3">{{ old('translations.' . $lang->code . '.meta_keywords', '') }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500 align-center">Twitter Title
                                                    ({{ $lang->name }})
                                                </label>
                                                <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    name="translations[{{ $lang->code }}][twitter_title]"
                                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                    value="{{ old('translations.' . $lang->code . '.twitter_title', '') }}">
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500">Twitter Description
                                                    ({{ $lang->name }})
                                                </label>
                                                <textarea name="translations[{{ $lang->code }}][twitter_description]"
                                                    @if ($lang->rtl == 1) dir="rtl" @endif class="form-control ip-gray radius-xs b-light px-15"
                                                    rows="5">{{ old('translations.' . $lang->code . '.twitter_description') }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500 align-center">OG Title
                                                    ({{ $lang->name }})
                                                </label>
                                                <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    name="translations[{{ $lang->code }}][og_title]"
                                                    class="form-control ih-small ip-gray radius-xs b-light px-15"
                                                    value="{{ old('translations.' . $lang->code . '.og_title', '') }}">
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label color-dark fw-500">OG Description
                                                    ({{ $lang->name }})
                                                </label>
                                                <textarea name="translations[{{ $lang->code }}][og_description]"
                                                    @if ($lang->rtl == 1) dir="rtl" @endif class="form-control ip-gray radius-xs b-light px-15"
                                                    rows="5">{{ old('translations.' . $lang->code . '.og_description') }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>

                            <div class="col-md-12 text-right mt-4 form-group d-flex flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                                <a href="{{ route('news.index') }}"
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
    

    <style>
      
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

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.classList.add('d-none');
            }
        }
    </script>
@endsection
