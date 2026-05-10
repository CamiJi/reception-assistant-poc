<?php

namespace Database\Factories;

use App\Models\KnowledgeDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KnowledgeDocument>
 */
class KnowledgeDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'title' => fake()->sentence(3),
            'source_type' => KnowledgeDocument::SOURCE_TEXT,
            'status' => KnowledgeDocument::STATUS_READY,
            'raw_text' => fake()->paragraph(),
            'transcript_text' => fake()->paragraph(),
            'practical_sheet' => [
                'titre' => fake()->sentence(3),
                'situation' => fake()->sentence(),
                'procedure_a_suivre' => '1. '.fake()->sentence().' 2. '.fake()->sentence(),
                'points_d_attention' => fake()->sentence(),
                'quand_prevenir_la_direction' => fake()->sentence(),
                'reponse_courte_a_donner' => fake()->sentence(),
            ],
        ];
    }
}
