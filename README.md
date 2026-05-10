# reception-assistant-poc

POC Laravel + Livewire pour un assistant interne hôtelier en français : ingestion de notes texte ou audio, transcription, génération de fiches pratiques, indexation vectorielle et chat RAG orienté action.

## Stack

- Laravel 13
- Livewire 3
- PostgreSQL + pgvector
- API OpenAI (transcription, structuration, embeddings, génération)
- Docker Compose pour le dev local

## Fonctionnalités MVP

- Authentification simple
- Deux rôles : `admin` et `user`
- Chat employé minimaliste type ChatGPT interne
- Back-office admin pour ajouter une note texte
- Back-office admin pour uploader un audio
- Transcription audio via OpenAI (fallback local si la clé n’est pas configurée)
- Génération automatique de fiche pratique structurée
- Découpage en chunks et indexation vectorielle
- Recherche sémantique + réponse RAG en français

## Installation locale avec Docker

```bash
cp .env.example .env
docker compose up --build
```

Puis dans un autre terminal :

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Application : http://localhost:8000
Vite : http://localhost:5173

## Installation locale sans Docker

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Comptes de démonstration

Après `php artisan db:seed` :

- Admin : `admin@hotel.test` / `password`
- User : `user@hotel.test` / `password`

## Variables d’environnement utiles

```dotenv
APP_NAME="reception-assistant-poc"
APP_LOCALE=fr
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=reception_assistant
DB_USERNAME=reception
DB_PASSWORD=reception
OPENAI_API_KEY=
OPENAI_CHAT_MODEL=gpt-4.1-mini
OPENAI_EMBEDDING_MODEL=text-embedding-3-small
OPENAI_EMBEDDING_DIMENSIONS=256
OPENAI_TRANSCRIPTION_MODEL=gpt-4o-mini-transcribe
RECEPTION_ASSISTANT_CHUNK_SIZE=500
RECEPTION_ASSISTANT_TOP_K=4
```

## Structure principale

- `app/Livewire/AdminKnowledgeBase.php` : back-office admin
- `app/Livewire/AssistantChat.php` : interface de chat employé
- `app/Services/ReceptionAssistant/*` : ingestion, embeddings, recherche, RAG
- `database/migrations/*knowledge*` : documents, chunks, rôle utilisateur, support pgvector
- `docker-compose.yml` : environnement local app + vite + postgres/pgvector

## TODO d’intégration externe

- Brancher une vraie clé OpenAI pour activer la transcription et la génération en production
- Ajouter une stratégie de monitoring / logs pour les appels OpenAI
- Optimiser l’index pgvector (probes, vacuum, filtres métier) quand le volume augmente
- Ajouter une gestion de fichiers audio plus robuste (S3, quotas, nettoyage)
- Ajouter des garde-fous métier sur les réponses RAG avant mise en production
