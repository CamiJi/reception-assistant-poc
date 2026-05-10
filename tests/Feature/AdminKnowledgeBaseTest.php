<?php

use App\Livewire\AdminKnowledgeBase;
use App\Models\KnowledgeDocument;
use App\Models\User;
use Livewire\Livewire;

test('admin can open knowledge base screen', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.knowledge-base'))
        ->assertOk()
        ->assertSee('Back-office admin');
});

test('user cannot open admin knowledge base screen', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.knowledge-base'))
        ->assertForbidden();
});

test('admin can ingest a text note into a structured knowledge document', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(AdminKnowledgeBase::class)
        ->set('noteTitle', 'Arrivée tardive')
        ->set('noteContent', 'Quand un client arrive après minuit, vérifier la réservation, préparer la clé, rappeler le code parking et prévenir la direction seulement si le client n’est pas attendu ou devient conflictuel.')
        ->call('saveNote')
        ->assertHasNoErrors();

    $document = KnowledgeDocument::query()->first();

    expect($document)->not->toBeNull()
        ->and($document->title)->toBe('Arrivée tardive')
        ->and($document->source_type)->toBe(KnowledgeDocument::SOURCE_TEXT)
        ->and($document->chunks->count())->toBeGreaterThanOrEqual(1)
        ->and($document->practical_sheet['reponse_courte_a_donner'])->not->toBe('');
});
