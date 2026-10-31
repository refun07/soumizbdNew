@extends('layouts.admin')
@section('title', 'Homepage Content | '.getSetting('systemName'))
@push('styles')
<style>.home-content-editor a { color: #314ba8; } .dark .home-content-editor a { color: #a7b9ff; }</style>
@endpush
@section('content')
<div class="home-content-editor">
    <div class="py-6 flex flex-wrap justify-between gap-4 items-center">
        <div><h1 class="text-2xl font-bold">Homepage content</h1><p class="text-muted mt-2">Edit the live botanical storefront. Save, then refresh the storefront to see changes.</p></div>
        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="text-primary underline">Preview storefront ↗</a>
    </div>
    <div class="card p-5 mb-6 space-y-2">
        <p>Product names, prices, images and stock come from <a class="text-primary underline" href="{{ route('admin.products.index') }}">Products</a>. Category names and images come from <a class="text-primary underline" href="{{ route('admin.categories.index') }}">Categories</a>.</p>
        <p><a class="text-primary underline" href="{{ route('admin.botanical-home.reviews') }}">Edit customer reviews and extra sections</a> · <a class="text-primary underline" href="{{ route('admin.theme.header.configure', ['headerId' => getSetting('defaultHeader', 'header-one')]) }}">Header navigation</a> · <a class="text-primary underline" href="{{ route('admin.theme.footer.configure') }}">Footer contact details, service text and social links</a> · <a class="text-primary underline" href="{{ route('admin.colorBranding.index') }}">Logo and branding</a></p>
    </div>
    @if ($errors->any())
        <div role="alert" class="card p-5 mb-6 text-red-600"><p class="font-bold">Please correct these fields:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('admin.botanical-home.update') }}" method="POST" class="space-y-4 mb-8">
        @csrf
        @foreach ($schema as $section => $definition)
            <details class="card p-5" @if($loop->first) open @endif>
                <summary class="text-lg font-semibold cursor-pointer">{{ $definition['label'] }}</summary>
                <div class="grid md:grid-cols-2 gap-5 pt-5">
                    @foreach ($definition['fields'] as $key => $field)
                        @php
                            $name = "content[$section][$key]";
                            $id = "home-$section-$key";
                            $value = old("content.$section.$key", $content[$section][$key]);
                        @endphp
                        <div class="min-w-0 space-y-2">
                            <label for="{{ $field['type'] === 'image' ? $name : $id }}" class="block font-medium">{{ $field['label'] }}</label>
                            @if ($field['type'] === 'boolean')
                                <input type="hidden" name="{{ $name }}" value="0">
                                <label class="flex items-center gap-3"><input id="{{ $id }}" type="checkbox" name="{{ $name }}" value="1" @checked($value)> Show this content</label>
                            @elseif ($field['type'] === 'textarea')
                                <textarea id="{{ $id }}" name="{{ $name }}" rows="3" maxlength="2000" class="theme-input w-full">{{ $value }}</textarea>
                            @elseif (in_array($field['type'], ['products', 'categories']))
                                <select id="{{ $id }}" name="{{ $name }}[]" multiple size="7" class="theme-input w-full" style="height:180px">
                                    @foreach ($field['type'] === 'products' ? $products : $categories as $item)
                                        <option value="{{ $item->id }}" @selected(in_array($item->id, $value ?? []))>{{ $item->name }} (#{{ $item->id }})</option>
                                    @endforeach
                                </select>
                                <button type="button" class="text-primary underline text-sm" onclick="document.getElementById('{{ $id }}').selectedIndex = -1">Clear selection</button>
                                <p class="text-xs text-muted">Hold Command (Mac) or Ctrl (Windows) to select or deselect. @if($field['type']==='products') Leave empty for automatic bestselling / newest products. @else Leave empty to show no featured categories. @endif Maximum 24 items.</p>
                            @elseif ($field['type'] === 'image')
                                <x-backend.inputs.file :labelInline="false" label="Choose replacement image" :name="$name" :value="e($value)" :isRequired="false" />
                                @php($preview = is_numeric($value) ? uploadedAsset($value) : $value)
                                @if($preview)<img src="{{ $preview }}" alt="Current {{ $definition['label'] }} image" style="max-width:240px;max-height:120px;object-fit:contain" loading="lazy">@endif
                                <p class="text-xs text-muted">Use Browse to upload or select an image from the media library. Server upload limit: {{ ini_get('upload_max_filesize') }} (application maximum: 10 MB). The image’s alt text is editable below.</p>
                            @elseif ($field['type'] === 'datetime')
                                <input id="{{ $id }}" name="{{ $name }}" type="datetime-local" value="{{ $value }}" class="theme-input w-full">
                                <p class="text-xs text-muted">Leave empty for no countdown. The section automatically hides when the configured time ends. Product prices and discounts are managed in Products.</p>
                            @elseif ($field['type'] === 'color')
                                <input id="{{ $id }}" name="{{ $name }}" type="color" value="{{ $value }}" class="w-20 h-11">
                            @else
                                <input id="{{ $id }}" name="{{ $name }}" type="{{ $field['type']==='number' ? 'number' : 'text' }}" value="{{ $value }}" class="theme-input w-full" @if($field['type']==='number') min="1" max="{{ $field['max'] }}" required @else maxlength="{{ $field['type']==='link' ? 2048 : 300 }}" @endif>
                            @endif
                        </div>
                    @endforeach
                </div>
            </details>
        @endforeach
        <div class="sticky bottom-0 card p-4 flex justify-end"><x-backend.inputs.button type="submit" buttonText="Save homepage content" /></div>
    </form>
</div>
@endsection
