<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeDocument extends Model
{
    /** @use HasFactory<\Database\Factories\KnowledgeDocumentFactory> */
    use HasFactory;

    public const SOURCE_AUDIO = 'audio';

    public const SOURCE_TEXT = 'text';

    public const STATUS_READY = 'ready';

    protected $fillable = [
        'created_by',
        'title',
        'source_type',
        'status',
        'source_path',
        'raw_text',
        'transcript_text',
        'practical_sheet',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'practical_sheet' => 'array',
            'metadata' => 'array',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(KnowledgeChunk::class);
    }
}
