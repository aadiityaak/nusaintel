<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['url', 'domain', 'publisher', 'source_type', 'title', 'published_at', 'excerpt', 'content_hash', 'fetch_status', 'fetch_error'])]
#[Hidden(['id', 'content_hash', 'fetch_error', 'created_at', 'updated_at'])]
class Source extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['published_at' => 'date'];
    }

    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_sources')->withPivot(['supporting_excerpt'])->withTimestamps();
    }
}
