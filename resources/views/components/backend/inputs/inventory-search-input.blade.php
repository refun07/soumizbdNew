@if ($label)
    <label class="theme-input-label !pt-0 input-required">
        {!! translate($label) !!}
    </label>
@endif
<div class="search-form relative flex-grow w-full">
    <input {{ $attributes->merge(['class' => 'theme-input ' . $class]) }} type="text" id="{{ $name }}" name="{{ $name }}"
        value="{!! $value !!}" placeholder="{{ translate($placeholder) }}"
        autocomplete="off" />
    <span
        class="text-primary dark:text-muted absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
</div>
