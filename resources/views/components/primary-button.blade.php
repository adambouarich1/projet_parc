<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2']) }} style="background-color: #006233;" onmouseover="this.style.backgroundColor='#004d28'" onmouseout="this.style.backgroundColor='#006233'">
    {{ $slot }}
</button>
