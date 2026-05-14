@props(['class' => 'size-full', 'alt' => 'Logo CBT'])

@php
    $logo = config('app.school_logo');
    $logoUrl = str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://') || str_starts_with($logo, '/')
        ? $logo
        : asset($logo);
@endphp

<img
    {{ $attributes->merge([
        'class' => $class,
        'src' => $logoUrl,
        'alt' => $alt,
        'referrerpolicy' => 'no-referrer',
    ]) }}
>
