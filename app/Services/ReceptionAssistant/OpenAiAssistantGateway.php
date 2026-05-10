<?php

namespace App\Services\ReceptionAssistant;

use App\Support\PracticalSheetFormatter;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiAssistantGateway implements AiGateway
{
    public function transcribe(string $absolutePath): string
    {
        $response = $this->client()
            ->attach('file', fopen($absolutePath, 'r'), basename($absolutePath))
            ->post('/audio/transcriptions', [
                'model' => config('services.openai.transcription_model'),
                'language' => 'fr',
            ])
            ->throw()
            ->json();

        return trim((string) Arr::get($response, 'text', ''));
    }

    public function createPracticalSheet(string $rawText, string $title): array
    {
        $content = $this->chatCompletion([
            ['role' => 'system', 'content' => 'Tu es un assistant interne pour hôtel. Réécris toujours en français. Retourne uniquement un JSON avec les clés: titre, situation, procedure_a_suivre, points_d_attention, quand_prevenir_la_direction, reponse_courte_a_donner. Les réponses doivent être concrètes, courtes et orientées action.'],
            ['role' => 'user', 'content' => "Titre proposé : {$title}\n\nTexte brut :\n{$rawText}"],
        ], true);

        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('La génération de fiche OpenAI n’a pas renvoyé un JSON valide.');
        }

        return PracticalSheetFormatter::normalize($decoded, $title);
    }

    public function embed(string $text): array
    {
        $payload = [
            'model' => config('services.openai.embedding_model'),
            'input' => $text,
        ];

        if (str_starts_with((string) config('services.openai.embedding_model'), 'text-embedding-3')) {
            $payload['dimensions'] = (int) config('reception_assistant.embedding_dimensions', 256);
        }

        $response = $this->client()
            ->post('/embeddings', $payload)
            ->throw()
            ->json();

        return array_map(
            fn (mixed $value): float => round((float) $value, 8),
            Arr::get($response, 'data.0.embedding', [])
        );
    }

    public function answer(string $question, array $context): string
    {
        if ($context === []) {
            return 'Je n’ai pas assez d’informations dans la base interne. Prévenez un administrateur pour compléter la connaissance.';
        }

        $formattedContext = collect($context)->map(function (array $item): string {
            return implode("\n", [
                'Fiche : '.$item['document_title'],
                PracticalSheetFormatter::toText($item['practical_sheet']),
                'Extrait : '.$item['content'],
            ]);
        })->implode("\n\n---\n\n");

        return $this->chatCompletion([
            ['role' => 'system', 'content' => 'Tu réponds à un employé d’hôtel en français. Réponse courte, fiable, exploitable, orientée action. Si l’information est incertaine, dis-le clairement et conseille de prévenir la direction.'],
            ['role' => 'user', 'content' => "Contexte RAG :\n{$formattedContext}\n\nQuestion : {$question}"],
        ]);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    private function chatCompletion(array $messages, bool $expectsJson = false): string
    {
        $payload = [
            'model' => config('services.openai.chat_model'),
            'messages' => $messages,
            'temperature' => 0.2,
        ];

        if ($expectsJson) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = $this->client()
            ->post('/chat/completions', $payload)
            ->throw()
            ->json();

        return trim((string) Arr::get($response, 'choices.0.message.content', ''));
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl('https://api.openai.com/v1')
            ->timeout(90)
            ->withToken((string) config('services.openai.api_key'))
            ->acceptJson();
    }
}
