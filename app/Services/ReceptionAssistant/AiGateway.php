<?php

namespace App\Services\ReceptionAssistant;

interface AiGateway
{
    public function transcribe(string $absolutePath): string;

    /**
     * @return array<string, string>
     */
    public function createPracticalSheet(string $rawText, string $title): array;

    /**
     * @return array<int, float>
     */
    public function embed(string $text): array;

    /**
     * @param  array<int, array{score: float, content: string, document_title: string, practical_sheet: array<string, string>}>  $context
     */
    public function answer(string $question, array $context): string;
}
