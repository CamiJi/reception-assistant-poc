<?php

use App\Livewire\AssistantChat;
use App\Models\User;
use App\Services\ReceptionAssistant\DocumentIngestionService;
use Livewire\Livewire;

test('authenticated user can open assistant screen', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('assistant'))
        ->assertOk()
        ->assertSee('Chat employé');
});

test('assistant answers from indexed knowledge in french', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    app(DocumentIngestionService::class)->ingestText(
        'Bagage oublié',
        'Si un client oublie un bagage, enregistrer l’objet, prévenir immédiatement le housekeeping, stocker le bagage en sécurité et prévenir la direction si le contenu semble sensible.',
        $admin->id,
    );

    $this->actingAs($user);

    Livewire::test(AssistantChat::class)
        ->set('question', 'Que faire pour un bagage oublié ?')
        ->call('ask')
        ->assertHasNoErrors()
        ->assertSet('question', '')
        ->assertSee('Action immédiate')
        ->assertSee('Bagage oublié');
});
