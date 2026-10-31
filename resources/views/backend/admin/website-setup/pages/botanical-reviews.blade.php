@extends('layouts.admin')
@section('title', 'Customer Reviews | '.getSetting('systemName'))
@section('content')
    <div class="py-6 space-y-3">
        <h1 class="text-2xl font-bold">Customer reviews</h1>
        <p>Manage the photos, names, roles and customer wording shown on your homepage. Only enter a star rating when the customer provided one.</p>
        <a href="{{ route('admin.botanical-home.edit') }}" class="underline">← Homepage headings and visibility</a>
    </div>
    <div class="init-accordion accordion-container card mb-6">
        <x-backend.forms.homepage-theme-one-reviews-form />
        <x-backend.forms.dynamic-sections-form langKey="{{ config('app.default_language') }}" />
    </div>
@endsection
