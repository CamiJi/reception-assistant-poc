<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tableau de bord
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Fiches indexées</p>
                    <p class="mt-3 text-4xl font-semibold text-gray-900 dark:text-white">{{ $documentCount }}</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Base de connaissance opérationnelle disponible pour le personnel.</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Accès rapide</h3>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('assistant') }}" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                            Ouvrir le chat interne
                        </a>
                        @if ($isAdmin)
                            <a href="{{ route('admin.knowledge-base') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900">
                                Gérer les notes et audios
                            </a>
                        @endif
                    </div>
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                        Réponses courtes en français, ingestion texte/audio côté admin, et architecture prête pour OpenAI + pgvector.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
