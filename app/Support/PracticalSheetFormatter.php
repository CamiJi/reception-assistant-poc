<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PracticalSheetFormatter
{
    public const FIELDS = [
        'titre',
        'situation',
        'procedure_a_suivre',
        'points_d_attention',
        'quand_prevenir_la_direction',
        'reponse_courte_a_donner',
    ];

    /**
     * @param  array<string, mixed>  $sheet
     * @return array<string, string>
     */
    public static function normalize(array $sheet, string $fallbackTitle = 'Fiche pratique'): array
    {
        return [
            'titre' => trim((string) Arr::get($sheet, 'titre', $fallbackTitle)) ?: $fallbackTitle,
            'situation' => trim((string) Arr::get($sheet, 'situation', 'Situation à préciser.')),
            'procedure_a_suivre' => trim((string) Arr::get($sheet, 'procedure_a_suivre', '1. Relire la note\n2. Appliquer la procédure confirmée\n3. Escalader en cas de doute.')),
            'points_d_attention' => trim((string) Arr::get($sheet, 'points_d_attention', 'Vérifier les informations avant de répondre au client.')),
            'quand_prevenir_la_direction' => trim((string) Arr::get($sheet, 'quand_prevenir_la_direction', 'Prévenir la direction si la situation sort du cadre habituel ou si un client est mécontent.')),
            'reponse_courte_a_donner' => trim((string) Arr::get($sheet, 'reponse_courte_a_donner', 'Je vérifie immédiatement pour vous et je reviens vers vous.')),
        ];
    }

    /**
     * @param  array<string, string>  $sheet
     */
    public static function toText(array $sheet): string
    {
        $normalized = self::normalize($sheet);

        return collect([
            "Titre : {$normalized['titre']}",
            "Situation : {$normalized['situation']}",
            "Procédure à suivre : {$normalized['procedure_a_suivre']}",
            "Points d'attention : {$normalized['points_d_attention']}",
            "Quand prévenir la direction : {$normalized['quand_prevenir_la_direction']}",
            "Réponse courte à donner : {$normalized['reponse_courte_a_donner']}",
        ])->implode("\n\n");
    }

    public static function excerpt(string $text, int $limit = 160): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', $text) ?? $text), $limit);
    }
}
