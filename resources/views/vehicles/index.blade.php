<x-app-layout>
   <x-slot name="header">
        <h2 class="text-white font-semibold text-2xl leading-tight">
            Gestion de Véhicules
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:vehicles />
        </div>
    </div>
</x-app-layout>
