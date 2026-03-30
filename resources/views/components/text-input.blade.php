@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-green-600 focus:ring-green-500 rounded-lg shadow-sm bg-white text-gray-900']) }}>
