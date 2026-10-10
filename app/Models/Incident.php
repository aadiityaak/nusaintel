<?php

namespace App\Models;

use Database\Factories\IncidentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['title', 'summary', 'category', 'occurred_at', 'occurred_at_precision', 'published_at', 'kecamatan', 'latitude', 'longitude', 'status', 'publication_basis', 'published_at_app', 'reviewed_by', 'reviewed_at'])]
#[Hidden(['id', 'reviewed_by', 'created_at', 'updated_at'])]
class Incident extends Model
{
    /** @use HasFactory<IncidentFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Incident $incident): void {
            $incident->public_id ??= (string) Str::uuid();
        });
    }

    protected function casts(): array
    {
        return ['occurred_at' => 'date', 'published_at' => 'date', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'published_at_app' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class, 'incident_sources')->withPivot(['supporting_excerpt'])->withTimestamps();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(IncidentUpdate::class);
    }
}
