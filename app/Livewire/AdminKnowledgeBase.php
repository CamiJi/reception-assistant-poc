<?php

namespace App\Livewire;

use App\Models\KnowledgeDocument;
use App\Services\ReceptionAssistant\DocumentIngestionService;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminKnowledgeBase extends Component
{
    use WithFileUploads;

    public string $noteTitle = '';

    public string $noteContent = '';

    public string $audioTitle = '';

    public mixed $audioFile = null;

    public function saveNote(DocumentIngestionService $ingestionService): void
    {
        $validated = $this->validate([
            'noteTitle' => ['required', 'string', 'max:120'],
            'noteContent' => ['required', 'string', 'min:20'],
        ]);

        $ingestionService->ingestText($validated['noteTitle'], $validated['noteContent'], (int) auth()->id());

        $this->reset('noteTitle', 'noteContent');
        session()->flash('status', 'Note ajoutée, structurée et indexée avec succès.');
    }

    public function saveAudio(DocumentIngestionService $ingestionService): void
    {
        $validated = $this->validate([
            'audioTitle' => ['required', 'string', 'max:120'],
            'audioFile' => ['required', 'file', 'max:20480', 'mimetypes:audio/mpeg,audio/mp4,audio/x-m4a,audio/wav,audio/x-wav,audio/webm'],
        ]);

        $ingestionService->ingestAudio($validated['audioTitle'], $validated['audioFile'], (int) auth()->id());

        $this->reset('audioTitle', 'audioFile');
        session()->flash('status', 'Audio importé, transcrit et indexé avec succès.');
    }

    public function render()
    {
        return view('livewire.admin-knowledge-base', [
            'documents' => KnowledgeDocument::query()->with('chunks')->latest()->get(),
        ]);
    }
}
