<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Parc Auto') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background-color: #006233;">

            {{-- Bande décorative rouge en haut --}}
            <div class="fixed top-0 left-0 right-0 h-1" style="background-color: #C1272D;"></div>

            {{-- Logo / En-tête --}}
            <div class="mb-6 flex flex-col items-center gap-3">
                {{-- Étoile marocaine --}}
                <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-lg" style="background-color: rgba(255,255,255,0.15);">
                    <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <polygon points="50,5 61,35 95,35 67,57 78,90 50,70 22,90 33,57 5,35 39,35"
                                 fill="none" stroke="#C8A951" stroke-width="6"/>
                    </svg>
                </div>

                <div class="text-center">
                    <h1 class="text-xl font-bold text-white">Commune Urbaine</h1>
                    <p class="text-sm font-medium" style="color: rgba(255,255,255,0.75);">Gestion du Parc Automobile</p>
                </div>
            </div>

            {{-- Card login --}}
            <div class="w-full sm:max-w-md px-6 py-7 bg-white shadow-2xl overflow-hidden sm:rounded-2xl border-t-4" style="border-top-color: #C1272D;">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-center" style="color: rgba(255,255,255,0.5);">
                Royaume du Maroc — Ministère de l'Intérieur
            </p>
        </div>
    </body>
</html>
