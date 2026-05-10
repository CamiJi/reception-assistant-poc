<?php

return [
    'embedding_dimensions' => (int) env('OPENAI_EMBEDDING_DIMENSIONS', 256),
    'chunk_size' => (int) env('RECEPTION_ASSISTANT_CHUNK_SIZE', 500),
    'chunk_overlap' => (int) env('RECEPTION_ASSISTANT_CHUNK_OVERLAP', 80),
    'top_k' => (int) env('RECEPTION_ASSISTANT_TOP_K', 4),
    'storage_disk' => env('RECEPTION_ASSISTANT_STORAGE_DISK', 'local'),
];
