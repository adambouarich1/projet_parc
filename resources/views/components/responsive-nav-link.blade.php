@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 text-start text-base font-medium text-gray-100 bg-gray-900 focus:outline-none focus:text-gray-100 focus:bg-gray-800 focus:border-indigo-400 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-gray-100 hover:bg-gray-800 hover:border-indigo-300 focus:outline-none focus:text-gray-100 focus:bg-gray-800 focus:border-indigo-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
