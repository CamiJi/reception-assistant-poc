<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assistant réception</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Posez une question opérationnelle. La réponse est générée en français à partir des fiches indexées.</p>

        <div class="mt-6 space-y-4">
            @forelse ($messages as $message)
                <div @class(['flex', 'justify-end' => $message['role'] === 'user'])>
                    <div @class([
                        'max-w-3xl rounded-2xl px-4 py-3 text-sm shadow-sm whitespace-pre-line',
                        'bg-gray-900 text-white' => $message['role'] === 'user',
                        'bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100' => $message['role'] === 'assistant',
                    ])>
                        {{ $message['content'] }}
                        @if (! empty($message['source']))
                            <p class="mt-3 border-t border-black/10 pt-3 text-xs text-gray-500 dark:border-white/10 dark:text-gray-400">
                                Source : {{ $message['source'] }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    Exemples : « Que dire à un client qui arrive après minuit ? » ou « Quelle procédure pour un bagage oublié ? »
                </div>
            @endforelse
        </div>
    </div>

    <form wire:submit="ask" class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <label for="question" class="sr-only">Question</label>
        <div class="flex flex-col gap-3 sm:flex-row">
            <input wire:model="question" id="question" type="text" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Ex: Comment gérer une arrivée tardive avec clé de nuit ?" />
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                Envoyer
            </button>
        </div>
        @error('question') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
    </form>
</div>
