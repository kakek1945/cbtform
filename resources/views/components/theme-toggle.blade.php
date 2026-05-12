@props(['class' => ''])

<button
    {{ $attributes->merge([
        'class' => trim('theme-toggle inline-flex size-10 items-center justify-center rounded-md border border-[#d0d7de] bg-white text-[#24292f] shadow-sm transition hover:bg-[#f6f8fa] '.$class),
        'type' => 'button',
        'aria-label' => 'Toggle dark mode',
        'title' => 'Toggle dark mode',
    ]) }}
>
    <x-icon name="moon" class="theme-toggle-icon-moon size-4" />
    <x-icon name="sun" class="theme-toggle-icon-sun hidden size-4" />
</button>
