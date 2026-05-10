<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Chat employé</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Réponses courtes, utiles et orientées action à partir des fiches internes.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <livewire:assistant-chat />
        </div>
    </div>
</x-app-layout>
