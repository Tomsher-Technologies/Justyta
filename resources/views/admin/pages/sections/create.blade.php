@extends('layouts.admin_default', ['title' => 'Add Section - ' . $page->name])

@section('content')
<div class="container-fluid">
    <div class="row mt-4 mb-4">
        <div class="col-lg-10 mx-auto">
            <div class="card card-horizontal card-default card-md mb-4">
                <div class="card-header">
                    <h5 class="mb-0 h4">Add Section for: {{ $page->name }}</h5>
                </div>
                <div class="card-body pb-md-30">
                    <form class="form-horizontal row" autocomplete="off" action="{{ route('pages.sections.store', $page) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="col-md-4 mb-3">
                            <label class="col-form-label color-dark fw-500">Section Type <span
                                    class="text-danger">*</span></label>
                            <select name="section_type" class="form-control ih-small ip-gray radius-xs b-light px-15" required>
                                <option value="">Select Type</option>
                                <option value="hero" {{ old('section_type') == 'hero' ? 'selected' : '' }}>Hero</option>
                                <option value="features" {{ old('section_type') == 'features' ? 'selected' : '' }}>Features</option>
                                <option value="services" {{ old('section_type') == 'services' ? 'selected' : '' }}>Services</option>
                                <option value="about" {{ old('section_type') == 'about' ? 'selected' : '' }}>About</option>
                                <option value="testimonial" {{ old('section_type') == 'testimonial' ? 'selected' : '' }}>Testimonial</option>
                                <option value="cta" {{ old('section_type') == 'cta' ? 'selected' : '' }}>Call to Action</option>
                                <option value="news" {{ old('section_type') == 'news' ? 'selected' : '' }}>News</option>
                                <option value="custom" {{ old('section_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('section_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="col-form-label color-dark fw-500">Section Key <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="section_key"
                                class="form-control ih-small ip-gray radius-xs b-light px-15"
                                value="{{ old('section_key') }}" required
                                placeholder="e.g., home_hero, home_services" />
                            <small class="text-muted">Unique identifier for this section</small>
                            @error('section_key')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="col-form-label color-dark fw-500">Order <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="order"
                                class="form-control ih-small ip-gray radius-xs b-light px-15"
                                value="{{ old('order', 0) }}" required />
                            @error('order')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="col-form-label color-dark fw-500">Image</label>
                            <input type="file" name="image" accept="image/*"
                                class="form-control ih-small ip-gray radius-xs b-light px-15"
                                onchange="previewImage(event)" />
                            <img id="imagePreview" src="#" alt="Image Preview" class="mt-3 img-thumbnail d-none"
                                style="max-height: 150px;" />
                            @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="col-form-label color-dark fw-500">Status</label>
                            <select name="status" class="form-control ih-small ip-gray radius-xs b-light px-15">
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-3">
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

                            <div class="tab-content custom-tab-content" id="langTabsContent">
                                @foreach ($languages as $lang)
                                <div class="tab-pane fade @if ($loop->first) show active @endif"
                                    id="lang-{{ $lang->code }}" role="tabpanel"
                                    aria-labelledby="tab-{{ $lang->code }}">

                                    <div class="form-group">
                                        <label class="col-form-label color-dark fw-500">Title ({{ $lang->name }})
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
                                        <label class="col-form-label color-dark fw-500">Subtitle ({{ $lang->name }})</label>
                                        <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                        name="translations[{{ $lang->code }}][subtitle]"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        value="{{ old('translations.' . $lang->code . '.subtitle', '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label color-dark fw-500">Description ({{ $lang->name }})</label>
                                        <textarea name="translations[{{ $lang->code }}][description]" @if ($lang->rtl == 1) dir="rtl" @endif
                                                    class="texteditor form-control ip-gray radius-xs b-light px-15"
                                                    rows="10">{{ old('translations.' . $lang->code . '.description', '') }}</textarea>
                                        @error("translations.$lang->code.description")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label color-dark fw-500">Button Text ({{ $lang->name }})</label>
                                        <input type="text" @if ($lang->rtl == 1) dir="rtl" @endif
                                        name="translations[{{ $lang->code }}][button_text]"
                                        class="form-control ih-small ip-gray radius-xs b-light px-15"
                                        value="{{ old('translations.' . $lang->code . '.button_text', '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label color-dark fw-500">Button Link ({{ $lang->name }})</label>
                                        <input type="text" name="translations[{{ $lang->code }}][button_link]"
                                            class="form-control ih-small ip-gray radius-xs b-light px-15"
                                            value="{{ old('translations.' . $lang->code . '.button_link', '') }}">
                                    </div>

                                    <div class="form-group mt-4">
                                        <label class="col-form-label color-dark fw-500 mb-2">Page Section Items ({{ $lang->name }})</label>
                                        <div class="repeater-container" data-lang="{{ $lang->code }}">
                                        </div>
                                        <button type="button" class="btn btn-info btn-sm add-repeater-item" data-lang="{{ $lang->code }}">+ Add Item</button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-12 text-right mt-4 form-group d-flex flex-wrap align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm ">Save</button>
                            <a href="{{ route('pages.sections.index', $page) }}"
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
    .custom-lang-tabs {
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
        let firstErrorTab = null;

        document.querySelectorAll('.tab-pane').forEach(function(pane) {
            if (pane.querySelector('.is-invalid')) {
                firstErrorTab = pane;
                return false;
            }
        });

        if (firstErrorTab) {
            let id = firstErrorTab.id;
            let tabTrigger = document.querySelector(`a[data-toggle="tab"][href="#${id}"]`);
            if (tabTrigger) {
                $(tabTrigger).tab('show');
            }
        }

        document.querySelectorAll('.texteditor').forEach(function(el) {
            tinymce.init({
                target: el,
                directionality: el.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr',
                height: 300,
                license_key: 'gpl',
                toolbar: 'undo redo | bold italic underline removeformat | alignleft aligncenter alignright | link | bullist numlist | outdent indent | blockquote | table | code',
                plugins: 'directionality code lists link table advlist',
                menubar: true,
                statusbar: true,
            });
        });

        document.querySelectorAll('.add-repeater-item').forEach(button => {
            button.addEventListener('click', function() {
                const lang = this.getAttribute('data-lang');
                const container = this.previousElementSibling;
                const index = container.querySelectorAll('.repeater-item').length;

                const template = `
                        <div class="repeater-item card mb-3 border">
                            <div class="card-body p-3 relative">
                                <button type="button" class="btn btn-danger btn-xs absolute top-0 right-0 m-2 remove-item">x</button>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label>Title</label>
                                        <input type="text" name="translations[${lang}][content][${index}][title]" class="form-control" value="">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label>URL</label>
                                        <input type="text" name="translations[${lang}][content][${index}][url]" class="form-control" value="">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Description</label>
                                        <textarea name="translations[${lang}][content][${index}][description]" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                container.insertAdjacentHTML('beforeend', template);
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.closest('.repeater-item').remove();
            }
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