<?php

namespace App\Services\ReceptionAssistant;

use App\Support\PracticalSheetFormatter;
use Illuminate\Support\Str;

class LocalAssistantGateway implements AiGateway
{
    public function transcribe(string $absolutePath): string
    {
        $filename = pathinfo($absolutePath, PATHINFO_FILENAME);

        return "Transcription locale de secours pour {$filename}. TODO: configurer OPENAI_API_KEY pour utiliser la transcription réelle.";
    }

    public function createPracticalSheet(string $rawText, string $title): array
    {
        $cleanText = trim(preg_replace('/\s+/', ' ', $rawText) ?? $rawText);
        $sentences = preg_split('/(?<=[.!?])\s+/u', $cleanText, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $situation = $sentences[0] ?? $cleanText ?: 'Situation opérationnelle communiquée par un administrateur.';
        $steps = collect($sentences)
            ->slice(0, 3)
            ->values()
            ->map(fn (string $sentence, int $index) => ($index + 1).'. '.rtrim($sentence, '.').'.')
            ->implode("\n");

        return PracticalSheetFormatter::normalize([
            'titre' => $title,
            'situation' => $situation,
            'procedure_a_suivre' => $steps ?: "1. Lire la note\n2. Appliquer les consignes\n3. Prévenir un responsable si nécessaire.",
            'points_d_attention' => $sentences[1] ?? 'Confirmer les informations sensibles avant de répondre au client.',
            'quand_prevenir_la_direction' => 'Prévenir la direction si la situation implique un risque client, une réclamation ou un geste commercial important.',
            'reponse_courte_a_donner' => 'Je m’en occupe tout de suite et je reviens vers vous rapidement.',
        ], $title);
    }

    public function embed(string $text): array
    {
        $dimensions = max(8, (int) config('reception_assistant.embedding_dimensions', 256));
        $vector = array_fill(0, $dimensions, 0.0);
        $tokens = preg_split('/[^\pL\pN]+/u', Str::lower($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($tokens as $token) {
            $index = abs(crc32($token)) % $dimensions;
            $vector[$index] += 1.0;
        }

        $norm = sqrt(array_reduce($vector, fn (float $carry, float $value) => $carry + ($value ** 2), 0.0));

        if ($norm > 0.0) {
            foreach ($vector as $index => $value) {
                $vector[$index] = round($value / $norm, 8);
            }
        }

        return $vector;
    }

    public function answer(string $question, array $context): string
    {
        if ($context === []) {
            return 'Je n’ai pas encore trouvé de fiche utile. Demandez à un administrateur d’ajouter une note ou un audio sur ce sujet.';
        }

        $bestMatch = $context[0];
        $sheet = PracticalSheetFormatter::normalize($bestMatch['practical_sheet'], $bestMatch['document_title']);

        return trim(implode(' ', [
            'Action immédiate : '.$sheet['procedure_a_suivre'],
            'Attention : '.$sheet['points_d_attention'],
            'Réponse au client : '.$sheet['reponse_courte_a_donner'],
        ]));
    }
}
