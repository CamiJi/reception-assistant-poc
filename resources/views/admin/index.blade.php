<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Back-office admin</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ajoutez rapidement des notes ou des audios pour enrichir l’assistant interne.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <livewire:admin-knowledge-base />
        </div>
    </div>
</x-app-layout>
