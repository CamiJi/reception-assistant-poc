<?php

use App\Models\KnowledgeDocument;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('assistant', 'assistant.index')->name('assistant');

    Route::view('admin/knowledge-base', 'admin.index')
        ->middleware('role:admin')
        ->name('admin.knowledge-base');

    Route::get('dashboard', function () {
        return view('dashboard', [
            'documentCount' => KnowledgeDocument::count(),
            'isAdmin' => auth()->user()?->isAdmin() ?? false,
        ]);
    })->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');
});

require __DIR__.'/auth.php';
