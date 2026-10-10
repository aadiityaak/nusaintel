<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['domain', 'domain_type', 'enabled', 'verified_by', 'verified_at', 'ai_classification', 'ai_confidence', 'ai_model', 'ai_classified_at', 'ai_reason'])]
#[Hidden(['ai_classification', 'ai_confidence', 'ai_model', 'ai_classified_at', 'ai_reason'])]
class SourceDomain extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['enabled' => 'boolean', 'verified_at' => 'datetime', 'ai_confidence' => 'decimal:4', 'ai_classified_at' => 'datetime'];
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
