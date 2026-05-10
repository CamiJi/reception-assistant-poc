<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'reception-assistant-poc') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-950 text-white antialiased">
        <main class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-16 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                <section>
                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.3em] text-gray-300">
                        Hôtel · assistant interne
                    </span>
                    <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                        Un POC simple pour centraliser les procédures réception et répondre vite.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg text-gray-300">
                        Notes texte, uploads audio, transcription, génération de fiches pratiques, indexation vectorielle et chat RAG en français dans une interface minimale.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl bg-white px-5 py-3 text-sm font-medium text-gray-950 transition hover:bg-gray-200">
                            Se connecter
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl border border-white/15 px-5 py-3 text-sm font-medium text-white transition hover:bg-white/5">
                                Créer un compte
                            </a>
                        @endif
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                    <h2 class="text-lg font-semibold text-white">MVP couvert</h2>
                    <ul class="mt-4 space-y-3 text-sm text-gray-300">
                        <li>• Authentification simple avec rôles admin / user</li>
                        <li>• Back-office ultra simple pour notes texte et audios</li>
                        <li>• Structuration automatique en fiche pratique</li>
                        <li>• Indexation des chunks + recherche sémantique</li>
                        <li>• Chat interne type ChatGPT orienté action</li>
                    </ul>
                    <div class="mt-6 rounded-2xl border border-white/10 bg-gray-950/60 p-4 text-sm text-gray-300">
                        <p class="font-medium text-white">Format de fiche</p>
                        <p class="mt-2">titre · situation · procédure à suivre · points d’attention · quand prévenir la direction · réponse courte à donner</p>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
