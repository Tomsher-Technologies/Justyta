@extends('layouts.admin_default', ['title' => 'Edit Appearance'])

@section('content')
<div class="container-fluid px-4">

    <div class="row mb-3">
        <div class="col-12">
            <div class="breadcrumb-main d-flex justify-content-between align-items-center">
                <h4 class="text-capitalize breadcrumb-title mb-0">Menu Appearance</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card w-100">
                <div class="card-body">

                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form action="{{ route('menu-appearance.update') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="row">
                        @csrf

                        <div class="col-6 mb-3">
                            <label for="logo" class="col-form-label fw-500 color-dark">Site Logo</label>
                            <input type="file"
                                name="logo"
                                id="logo"
                                class="form-control">

                            @if(isset($settings['logo']))
                            <div class="mt-2">
                                <img src="{{ asset($settings['logo']) }}"
                                    alt="Logo"
                                    style="max-height:100px">
                            </div>
                            @endif

                            @error('logo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-6 mb-3">
                            <label for="email" class="col-form-label fw-500 color-dark">
                                Contact Email
                            </label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $settings['email'] ?? '' }}">

                            @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="col-form-label fw-500 color-dark">Footer Social Links</label>

                            @php
                            $footerLinks = isset($settings['footer_links'])
                            ? json_decode($settings['footer_links'], true)
                            : [];

                            if (empty($footerLinks)) {
                            $footerLinks = [['title' => '', 'icon' => '', 'url' => '']];
                            }
                            @endphp

                            <div id="footer-links-container">
                                @foreach($footerLinks as $index => $link)
                                <div class="row mb-2 footer-link-item">
                                    <div class="col-md-3">
                                        <input type="text"
                                            name="footer_links[{{ $index }}][title]"
                                            class="form-control"
                                            placeholder="Title"
                                            value="{{ $link['title'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text"
                                            name="footer_links[{{ $index }}][icon]"
                                            class="form-control"
                                            placeholder="Icon (SVG Code)"
                                            value="{{ $link['icon'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text"
                                            name="footer_links[{{ $index }}][url]"
                                            class="form-control"
                                            placeholder="Link URL"
                                            value="{{ $link['url'] ?? '' }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center justify-content-end">
                                        <button type="button" class="btn btn-danger btn-xs remove-item">×</button>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <button type="button"
                                class="btn btn-sm btn-secondary mt-2"
                                onclick="addFooterLink()">
                                + Add More Link
                            </button>
                        </div>

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
                                   
                                    <div class="tab-pane fade @if ($loop->first) show active @endif"
                                        id="lang-{{ $lang->code }}" role="tabpanel"
                                        aria-labelledby="tab-{{ $lang->code }}">

                                        <div class="row mt-2">
                                            <div class="col-6 mb-3">
                                                <label for="footer_copyright_{{ $lang->code }}" class="col-form-label fw-500 color-dark">
                                                    Footer Copyright ({{ $lang->name }})
                                                </label>
                                                <input type="text" name="footer_copyright_{{ $lang->code }}" id="footer_copyright_{{ $lang->code }}" class="form-control" value="{{ $settings['footer_copyright_' . $lang->code] ?? '' }}">

                                                @error('footer_copyright_' . $lang->code)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-6 mb-3">
                                                <label for="address_{{ $lang->code }}" class="col-form-label fw-500 color-dark">
                                                    Contact Address ({{ $lang->name }})
                                                </label>
                                                <textarea type="text" name="address_{{ $lang->code }}" id="address_{{ $lang->code }}" class="form-control" rows="3">{{ $settings['address_' . $lang->code] ?? '' }}</textarea>

                                                @error('address_' . $lang->code)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label for="shop_description_{{ $lang->code }}" class="col-form-label fw-500 color-dark">
                                                    Shop Description ({{ $lang->name }})
                                                </label>
                                                <textarea name="shop_description_{{ $lang->code }}"
                                                    id="shop_description_{{ $lang->code }}"
                                                    class="form-control"
                                                    rows="4">{{ $settings['shop_description_' . $lang->code] ?? '' }}</textarea>

                                                @error('shop_description_' . $lang->code)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>


                                        

                                        {{-- @if ($page->slug === 'payment-page' || $page->slug === 'consultancy_waiting_page')
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
                                        @endif --}}

                                        <div class="col-12">
                                            <hr>
                                            <h6><u>Block Headings</u></h6>
                                            
                                            <div class="row mt-2">
                                                <div class="col-6 mb-3">
                                                    <label for="block_heading_1_{{ $lang->code }}" class="col-form-label fw-500 color-dark">
                                                        Block Heading 1 ({{ $lang->name }})
                                                    </label>
                                                    <input type="text" name="block_heading_1_{{ $lang->code }}" id="block_heading_1_{{ $lang->code }}" class="form-control" value="{{ $settings['block_heading_1_' . $lang->code] ?? '' }}">

                                                    @error('block_heading_1_' . $lang->code)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label for="block_heading_2_{{ $lang->code }}" class="col-form-label fw-500 color-dark">
                                                        Block Heading 2 ({{ $lang->name }})
                                                    </label>
                                                    <input type="text" name="block_heading_2_{{ $lang->code }}" id="block_heading_2_{{ $lang->code }}" class="form-control" value="{{ $settings['block_heading_2_' . $lang->code] ?? '' }}">

                                                    @error('block_heading_2_' . $lang->code)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label for="block_heading_3_{{ $lang->code }}" class="col-form-label fw-500 color-dark">
                                                        Block Heading 3 ({{ $lang->name }})
                                                    </label>
                                                    <input type="text" name="block_heading_3_{{ $lang->code }}" id="block_heading_3_{{ $lang->code }}" class="form-control" value="{{ $settings['block_heading_3_' . $lang->code] ?? '' }}">

                                                    @error('block_heading_3_' . $lang->code)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label for="block_heading_4_{{ $lang->code }}" class="col-form-label fw-500 color-dark">
                                                        Block Heading 4 ({{ $lang->name }})
                                                    </label>
                                                    <input type="text" name="block_heading_4_{{ $lang->code }}" id="block_heading_4_{{ $lang->code }}" class="form-control" value="{{ $settings['block_heading_4_' . $lang->code] ?? '' }}">

                                                    @error('block_heading_4_' . $lang->code)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                       
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        
                        <div class="col-12 d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Update Settings
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    function addFooterLink() {
        const container = document.getElementById('footer-links-container');
        const index = container.querySelectorAll('.footer-link-item').length;

        const html = `
            <div class="row mb-2 footer-link-item">
                 <div class="col-md-3">
                    <input type="text"
                           name="footer_links[${index}][title]"
                           class="form-control"
                           placeholder="Title">
                </div>
                <div class="col-md-4">
                    <input type="text"
                           name="footer_links[${index}][icon]"
                           class="form-control"
                           placeholder="Icon (SVG Code)">
                </div>
                <div class="col-md-4">
                    <input type="text"
                           name="footer_links[${index}][url]"
                           class="form-control"
                           placeholder="Link URL">
                </div>
                <div class="col-md-1 d-flex align-items-center justify-content-end">
                    <button type="button" class="btn btn-danger btn-xs remove-item">×</button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.footer-link-item').remove();
        }
    });
</script>
@endsection