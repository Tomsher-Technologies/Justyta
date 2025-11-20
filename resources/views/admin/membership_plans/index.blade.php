@extends('layouts.admin_default',['title' => 'All Membership Plans'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title">All Membership Plans</h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    
                    {{-- <div class="action-btn">
                        <a href="{{ route('membership-plans.create') }}" class="btn btn-sm btn-primary btn-add">
                            <i class="la la-plus"></i> Add New Plan</a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @can('view_plan')
                            @foreach($plans as $plan)
                                <div class="col-xxl-6 col-lg-6 col-sm-6 mb-30">
                                    <div class="card h-100">
                                        <div class="card-body p-30">
                                            @if($plan->icon)
                                                <img src="{{ asset(getUploadedImage($plan->icon)) }}" class="card-img-top p-3" style="height: 120px; object-fit: contain;" alt="{{ $plan->title }}">
                                            @endif
                                            <div class="pricing d-flex align-items-center">
                                                <span class="fw-600 fs-20 text-secondary">{{ strtoupper($plan->title) }}</span>
                                                {!! $plan->is_active ? '<span class="badge badge-success rounded-pill ml-4">Active</span>' : '<span class="badge badge-danger rounded-pill ml-4">Inactive</span>' !!}
                                            </div>
                                            <div class="pricing__price rounded">
                                                <p class="pricing_value display-5 color-dark d-flex align-items-center text-capitalize fw-600 mb-1">
                                                {{ number_format($plan->amount, 2) }} AED
                                                </p>
                                            </div>
                                            <div class="pricing__features">
                                                <ul>
                                                    <li>
                                                        <span class="fa fa-check"></span>{{ $plan->live_online ? 'Access To Live Online Consultancy Platform' : '-' }}
                                                    </li>

                                                    <li>
                                                        <span class="fa fa-check"></span>{{ $plan->live_online ? 'Regular Online Consultancy' : '-' }}
                                                    </li>
                                                    <li>
                                                        <span class="fa fa-check"></span>{{ $plan->specific_law_firm_choice ? 'Specific Law firm Choice' : '-' }}
                                                    </li>
                                                    <li>
                                                        <span class="fa fa-check"></span> {{ $plan->annual_legal_contract ? 'Annual Legal Consultancy Contracts' : '-' }}
                                                    </li>

                                                    <li>
                                                        <span class="fa fa-check"></span>Up to {{ $plan->member_count }} users access
                                                    </li>
                                                    {{-- <li>
                                                        <span class="fa fa-check"></span>{{ $plan->en_ar_price }} AED En to AR Translation/page 
                                                    </li>
                                                    <li>
                                                        <span class="fa fa-check"></span>{{ $plan->for_ar_price }} AED Foreign Language to AR Translation/page 
                                                    </li> --}}
                                                    <li>
                                                        <span class="fa fa-check"></span>{{ $plan->job_post_count === 0 ? 'Unlimited' : $plan->job_post_count }} Job Posts / month
                                                    </li>
                                                    <li>
                                                        <span class="fa fa-check"></span>
                                                        @if ($plan->annual_free_ad_days != 0)
                                                            {{ $plan->annual_free_ad_days }} Days Free Advertisement/Annual
                                                        @else
                                                            -
                                                        @endif
                                                        
                                                    </li>
                                                    <li>
                                                        <span class="fa fa-check"></span>{{ $plan->unlimited_training_applications ? 'Unlimited Training Applications' : '-' }}
                                                    </li>
                                                    <li>
                                                        <span class="fa fa-check"></span>
                                                        @if($plan->welcome_gift === 'premium')
                                                            Premium Welcome Gift
                                                        @elseif($plan->welcome_gift === 'special')
                                                            Special Welcome Gift
                                                        @else
                                                            No Welcome Gift
                                                        @endif
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="card-footer text-end d-flex">
                                            @can('edit_plan')
                                                <a href="{{ route('membership-plans.edit', $plan->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            @endcan

                                            @can('view_plan_pricing')
                                                <a href="{{ route('plan-pricing', base64_encode($plan->id)) }}" class="btn btn-sm btn-secondary ml-2">Translation Pricing</a>
                                            @endcan
                                        </div>
                                        
                                    </div><!-- End: .card -->
                                </div>
                            @endforeach
                        @endcan
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $plans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection