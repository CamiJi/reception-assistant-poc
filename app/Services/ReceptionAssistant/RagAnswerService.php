<?php

namespace App\Services\ReceptionAssistant;

class RagAnswerService
{
    public function __construct(
        private readonly SemanticSearchService $semanticSearchService,
        private readonly AiGateway $aiGateway,
    ) {}

    /**
     * @return array{answer: string, context: array<int, array{score: float, content: string, document_title: string, practical_sheet: array<string, string>}>}
     */
    public function answer(string $question): array
    {
        $context = $this->semanticSearchService->search(
            $question,
            (int) config('reception_assistant.top_k', 4)
        );

        return [
            'answer' => $this->aiGateway->answer($question, $context),
            'context' => $context,
        ];
    }
}
