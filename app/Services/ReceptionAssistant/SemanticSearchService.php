<?php

namespace App\Services\ReceptionAssistant;

use App\Models\KnowledgeChunk;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SemanticSearchService
{
    public function __construct(private readonly AiGateway $aiGateway) {}

    /**
     * @return array<int, array{score: float, content: string, document_title: string, practical_sheet: array<string, string>}>
     */
    public function search(string $question, int $limit = 4): array
    {
        $queryEmbedding = $this->aiGateway->embed($question);

        return DB::getDriverName() === 'pgsql' && Schema::hasColumn('knowledge_chunks', 'embedding_vector')
            ? $this->searchWithPgvector($queryEmbedding, $limit)
            : $this->searchInPhp($queryEmbedding, $limit);
    }

    /**
     * @param  array<int, float>  $queryEmbedding
     * @return array<int, array{score: float, content: string, document_title: string, practical_sheet: array<string, string>}>
     */
    private function searchInPhp(array $queryEmbedding, int $limit): array
    {
        return KnowledgeChunk::query()
            ->with('document')
            ->get()
            ->map(function (KnowledgeChunk $chunk) use ($queryEmbedding): array {
                return [
                    'score' => $this->cosineSimilarity($queryEmbedding, $chunk->embedding ?? []),
                    'content' => $chunk->content,
                    'document_title' => $chunk->document?->title ?? 'Fiche pratique',
                    'practical_sheet' => $chunk->document?->practical_sheet ?? [],
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @param  array<int, float>  $queryEmbedding
     * @return array<int, array{score: float, content: string, document_title: string, practical_sheet: array<string, string>}>
     */
    private function searchWithPgvector(array $queryEmbedding, int $limit): array
    {
        $literal = '['.collect($queryEmbedding)
            ->map(fn (float $value): string => number_format($value, 8, '.', ''))
            ->implode(',').']';

        return KnowledgeChunk::query()
            ->select('knowledge_chunks.*')
            ->selectRaw('1 - (embedding_vector <=> ?::vector) as similarity', [$literal])
            ->with('document')
            ->orderByDesc('similarity')
            ->limit($limit)
            ->get()
            ->map(fn (KnowledgeChunk $chunk): array => [
                'score' => (float) Arr::get($chunk->getAttributes(), 'similarity', 0.0),
                'content' => $chunk->content,
                'document_title' => $chunk->document?->title ?? 'Fiche pratique',
                'practical_sheet' => $chunk->document?->practical_sheet ?? [],
            ])
            ->all();
    }

    /**
     * @param  array<int, float>  $left
     * @param  array<int, float>  $right
     */
    private function cosineSimilarity(array $left, array $right): float
    {
        $count = min(count($left), count($right));

        if ($count === 0) {
            return 0.0;
        }

        $dot = 0.0;
        $leftNorm = 0.0;
        $rightNorm = 0.0;

        for ($index = 0; $index < $count; $index++) {
            $dot += $left[$index] * $right[$index];
            $leftNorm += $left[$index] ** 2;
            $rightNorm += $right[$index] ** 2;
        }

        if ($leftNorm === 0.0 || $rightNorm === 0.0) {
            return 0.0;
        }

        return round($dot / (sqrt($leftNorm) * sqrt($rightNorm)), 6);
    }
}
