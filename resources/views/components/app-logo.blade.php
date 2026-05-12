@props(['class' => 'size-full', 'alt' => 'Logo CBT'])

<img
    {{ $attributes->merge([
        'class' => $class,
        'src' => 'https://i.ibb.co.com/CKDDdxHY/Chat-GPT-Image-12-Mei-2026-22-09-47.png',
        'alt' => $alt,
        'referrerpolicy' => 'no-referrer',
    ]) }}
>
