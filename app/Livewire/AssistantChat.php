<?php

namespace App\Livewire;

use App\Support\PracticalSheetFormatter;
use App\Services\ReceptionAssistant\RagAnswerService;
use Livewire\Component;

class AssistantChat extends Component
{
    public string $question = '';

    /**
     * @var array<int, array{role: string, content: string, source?: string}>
     */
    public array $messages = [];

    public function ask(RagAnswerService $ragAnswerService): void
    {
        $validated = $this->validate([
            'question' => ['required', 'string', 'min:4', 'max:500'],
        ]);

        $question = $validated['question'];
        $this->messages[] = [
            'role' => 'user',
            'content' => $question,
        ];

        $result = $ragAnswerService->answer($question);
        $firstContext = $result['context'][0] ?? null;

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $result['answer'],
            'source' => $firstContext
                ? $firstContext['document_title'].' · '.PracticalSheetFormatter::excerpt($firstContext['content'])
                : null,
        ];

        $this->reset('question');
    }

    public function render()
    {
        return view('livewire.assistant-chat');
    }
}
