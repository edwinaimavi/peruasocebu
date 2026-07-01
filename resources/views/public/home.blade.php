@extends('public.layouts.app')

@section('title', 'PERU ASOCEBU | Portal ganadero institucional')
@section('meta_description', 'PERU ASOCEBU: registro genealogico, trazabilidad y certificacion digital de ganado de raza.')

@section('content')
    @include('public.partials.home-hero')
    @include('public.partials.home-benefits')
    @include('public.partials.home-services')
    @include('public.partials.home-breeds', ['breeds' => $breeds ?? collect()])
    @include('public.partials.home-certification')
    @include('public.partials.home-blog-preview', ['latestPosts' => $latestPosts ?? collect()])
    @include('public.partials.home-contact')
@endsection
