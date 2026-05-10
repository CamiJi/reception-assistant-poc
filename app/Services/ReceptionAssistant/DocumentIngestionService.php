<?php

namespace App\Services\ReceptionAssistant;

use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use App\Support\PracticalSheetFormatter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DocumentIngestionService
{
    public function __construct(private readonly AiGateway $aiGateway)
    {
    }

    public function ingestText(string $title, string $rawText, int $adminId): KnowledgeDocument
    {
        return DB::transaction(function () use ($title, $rawText, $adminId): KnowledgeDocument {
            $sheet = $this->aiGateway->createPracticalSheet($rawText, $title);

            $document = KnowledgeDocument::query()->create([
                'title' => $title,
                'source_type' => KnowledgeDocument::SOURCE_TEXT,
                'status' => KnowledgeDocument::STATUS_READY,
                'raw_text' => $rawText,
                'transcript_text' => $rawText,
                'practical_sheet' => $sheet,
                'created_by' => $adminId,
            ]);

            $this->indexDocument($document);

            return $document->refresh();
        });
    }

    public function ingestAudio(string $title, UploadedFile $audio, int $adminId): KnowledgeDocument
    {
        $storedPath = $audio->store('knowledge-audio', [
            'disk' => config('reception_assistant.storage_disk', 'local'),
        ]);

        $absolutePath = Storage::disk(config('reception_assistant.storage_disk', 'local'))->path($storedPath);
        $transcript = $this->aiGateway->transcribe($absolutePath);

        return DB::transaction(function () use ($title, $adminId, $storedPath, $transcript): KnowledgeDocument {
            $sheet = $this->aiGateway->createPracticalSheet($transcript, $title);

            $document = KnowledgeDocument::query()->create([
                'title' => $title,
                'source_type' => KnowledgeDocument::SOURCE_AUDIO,
                'status' => KnowledgeDocument::STATUS_READY,
                'source_path' => $storedPath,
                'raw_text' => $transcript,
                'transcript_text' => $transcript,
                'practical_sheet' => $sheet,
                'created_by' => $adminId,
            ]);

            $this->indexDocument($document);

            return $document->refresh();
        });
    }

    public function indexDocument(KnowledgeDocument $document): void
    {
        $document->chunks()->delete();

        $chunks = $this->splitIntoChunks(PracticalSheetFormatter::toText($document->practical_sheet));

        foreach ($chunks as $index => $content) {
            $embedding = $this->aiGateway->embed($content);
            $chunk = $document->chunks()->create([
                'position' => $index,
                'content' => $content,
                'embedding' => $embedding,
            ]);

            $this->syncPgvectorEmbedding($chunk, $embedding);
        }
    }

    /**
     * @return array<int, string>
     */
    private function splitIntoChunks(string $text): array
    {
        $chunkSize = max(150, (int) config('reception_assistant.chunk_size', 500));
        $paragraphs = preg_split('/\n{2,}/', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $chunks = [];
        $buffer = '';

        foreach ($paragraphs as $paragraph) {
            $candidate = trim($buffer.' '.trim($paragraph));

            if ($candidate !== '' && mb_strlen($candidate) > $chunkSize && $buffer !== '') {
                $chunks[] = trim($buffer);
                $buffer = trim($paragraph);
                continue;
            }

            $buffer = $candidate;
        }

        if ($buffer !== '') {
            $chunks[] = trim($buffer);
        }

        return $chunks === [] ? [trim($text)] : $chunks;
    }

    /**
     * @param  array<int, float>  $embedding
     */
    private function syncPgvectorEmbedding(KnowledgeChunk $chunk, array $embedding): void
    {
        if (DB::getDriverName() !== 'pgsql' || ! Schema::hasColumn('knowledge_chunks', 'embedding_vector')) {
            return;
        }

        DB::statement(
            'UPDATE knowledge_chunks SET embedding_vector = ?::vector WHERE id = ?',
            [$this->toVectorLiteral($embedding), $chunk->getKey()]
        );
    }

    /**
     * @param  array<int, float>  $embedding
     */
    private function toVectorLiteral(array $embedding): string
    {
        return '['.collect($embedding)
            ->map(fn (float $value): string => number_format($value, 8, '.', ''))
            ->implode(',').']';
    }
}
