<div class="option-dropdown" tabindex="0">
    <div class="option-dropdown__toggler bg-background text-muted">
        <span>
            <img src="{{ asset("images/flags/$language->flag.png") }}" alt="" class="w-[20px]" />
        </span>
        <span>{{ $language->name }}</span>
    </div>

    <div class="option-dropdown__options">
        <ul>
            @foreach ($languages as $language)
                <li>
                    <a href='{{ url()->current() . '?lang_key=' . $language->code . '&translate' }}'
                        class="option-dropdown__option">
                        <span>
                            <img src="{{ asset("images/flags/$language->flag.png") }}" alt="" class="w-[20px]" />
                        </span>
                        <span>{{ $language->name }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
