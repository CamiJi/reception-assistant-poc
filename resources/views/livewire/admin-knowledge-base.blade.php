<div class="space-y-6">
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <form wire:submit="saveNote" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ajouter une note texte</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">La note est restructurée automatiquement puis indexée pour le chat interne.</p>
            </div>
            <div>
                <label for="noteTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titre</label>
                <input wire:model="noteTitle" id="noteTitle" type="text" class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Ex: Procédure arrivée tardive" />
                @error('noteTitle') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="noteContent" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Texte brut</label>
                <textarea wire:model="noteContent" id="noteContent" rows="8" class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Décrivez la situation, la procédure, les exceptions..."></textarea>
                @error('noteContent') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                Enregistrer la note
            </button>
        </form>

        <form wire:submit="saveAudio" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Importer un audio</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Le fichier est stocké, transcrit puis converti en fiche pratique structurée.</p>
            </div>
            <div>
                <label for="audioTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titre</label>
                <input wire:model="audioTitle" id="audioTitle" type="text" class="mt-1 w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Ex: Gestion des bagages oubliés" />
                @error('audioTitle') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="audioFile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fichier audio</label>
                <input wire:model="audioFile" id="audioFile" type="file" accept="audio/*" class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300" />
                @error('audioFile') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900">
                Importer et transcrire
            </button>
        </form>
    </div>

    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Fiches déjà indexées</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aperçu minimal des contenus disponibles pour le chat employé.</p>
            </div>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-200">{{ $documents->count() }} fiches</span>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($documents as $document)
                <article class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ $document->title }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $document->source_type }} · {{ $document->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-200">{{ $document->chunks->count() }} chunk(s)</span>
                    </div>
                    <dl class="mt-4 grid gap-3 text-sm text-gray-700 dark:text-gray-200 lg:grid-cols-2">
                        <div>
                            <dt class="font-medium">Situation</dt>
                            <dd class="mt-1">{{ $document->practical_sheet['situation'] }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium">Réponse courte</dt>
                            <dd class="mt-1">{{ $document->practical_sheet['reponse_courte_a_donner'] }}</dd>
                        </div>
                    </dl>
                </article>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune fiche indexée pour le moment.</p>
            @endforelse
        </div>
    </section>
</div>
