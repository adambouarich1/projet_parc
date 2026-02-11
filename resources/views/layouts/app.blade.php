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

<body class="font-sans antialiased bg-gray-950">
<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-4 border-b border-gray-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <span class="text-xl">🚗</span>
                </div>
                <span class="text-lg font-bold text-white">Parc Auto</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">📊</span>
                Dashboard
            </a>

            <a href="{{ route('vehicles.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('vehicles.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">🚗</span>
                Véhicules
            </a>

            <a href="{{ route('drivers.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('drivers.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">👥</span>
                Conducteurs
            </a>

            <a href="{{ route('missions.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('missions.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">📋</span>
                Missions
            </a>

            <a href="{{ route('fuel.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('fuel.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">⛽</span>
                Carburant
            </a>

            <a href="{{ route('interventions.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('interventions.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">🔧</span>
                Interventions
            </a>

            @php
                $alertCount = \App\Models\Alert::nonArchive()->critique()->count();
            @endphp

            <a href="{{ route('alerts.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('alerts.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">🔔</span>
                Alertes
                @if($alertCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-rose-600 rounded-full">
                        {{ $alertCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('insurances.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('insurances.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">🛡️</span>
                Assurances
            </a>

            <a href="{{ route('vignettes.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('vignettes.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">🏷️</span>
                Vignettes
            </a>

            <a href="{{ route('archives.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
               {{ request()->routeIs('archives.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="text-lg">🗄️</span>
                Archives
            </a>
        </nav>

        {{-- User section --}}
        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </span>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ Auth::user()->role_label }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition"
                            title="Déconnexion">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col">

        {{-- Top bar --}}
        <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6 sticky top-0 z-40">
            <h1 class="text-lg font-semibold text-white">
                @yield('title', 'Dashboard')
            </h1>

            <div class="flex items-center gap-4">
                <a href="{{ route('alerts.index') }}"
                   class="relative p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>

                    @if($alertCount > 0)
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-rose-600 rounded-full">
                            {{ $alertCount }}
                        </span>
                    @endif
                </a>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </span>
                    </div>
                    <span class="text-sm font-medium text-white hidden md:block">
                        {{ Auth::user()->name }}
                    </span>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

</div>
</body>
</html>
