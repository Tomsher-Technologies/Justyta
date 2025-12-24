@extends('layouts.web_login', ['title' => 'Home'])

@section('content')

@if($page && $page->sections->count())
@foreach($page->sections->sortBy('order') as $section)
@continue(!$section->status)

@switch($section->section_key)

@case('home_hero')
@include('frontend.sections.home.hero', ['section' => $section])
@break

@case('home_banner')
@include('frontend.sections.home.banner', ['section' => $section])
@break

@case('home_services')
@include('frontend.sections.home.services', ['section' => $section])
@break

@case('home_quote')
@include('frontend.sections.home.quote', ['section' => $section])
@break

@case('home_new_era')
@include('frontend.sections.home.new-era', ['section' => $section])
@break

@case('home_impact')
@include('frontend.sections.home.impact', ['section' => $section])
@break

@case('home_reimagined')
@include('frontend.sections.home.reimagined', ['section' => $section])
@break

@case('home_app_download')
@include('frontend.sections.home.app-download', ['section' => $section])
@break

@case('home_news')
@include('frontend.sections.home.news', ['section' => $section])
@break

@endswitch
@endforeach
@endif

@endsection