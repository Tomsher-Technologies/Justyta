@extends('layouts.admin_default', ['title' => 'All Pages'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">All Pages</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="container py-4">
                            <div class="row">
                                @foreach ($pages as $page)
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm h-100 page-card">
                                            <div class="card-body d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-title mb-0">{{ $page->name }}</h6>
                                                </div>
                                                <a href="{{ route('pages.edit', $page->id) }}"
                                                    class="btn btn-primary btn-xs">
                                                    Edit
                                                </a>
                                            </div>
                                        </div>
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
    <style>
        .page-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .page-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('script')
    <script type="text/javascript"></script>
@endsection
