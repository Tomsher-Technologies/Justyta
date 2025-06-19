@extends('layouts.admin_default')

@section('content')
    <div class="container mt-5">
        <h4 class="mb-3">Dropdown Types</h4>
        @can('manage_dropdown_option')
            @if ($dropdowns->isEmpty())
                <div class="alert alert-info">No dropdown types found.</div>
            @else
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    @foreach ($dropdowns as $dropdown)
                        <div class="col mb-3">
                            <a href="{{ route('dropdown-options.index', $dropdown->slug) }}" class="text-decoration-none">
                                <div class="card h-100 shadow-sm border-primary hover-shadow">
                                    <div
                                        class="card-body d-flex flex-column justify-content-center align-items-center text-center dropdown-card">
                                        <h6 class="card-title fw-bold">{{ $dropdown->name }}</h6>
                                        {{-- <p class="card-text text-muted mb-2 text-uppercase">{{ $dropdown->slug }}</p> --}}
                                        <button class="btn btn-secondary  btn-xs">View Options</button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        @endcan
    </div>
@endsection

@section('style')
    <style>
        .hover-shadow:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: box-shadow 0.3s ease-in-out;
        }

        a.text-decoration-none:hover {
            text-decoration: none;
        }

        .dropdown-card {
            padding: 1rem !important;
        }
    </style>
@endsection
