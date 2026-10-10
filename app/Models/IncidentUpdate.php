<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['incident_id', 'actor_id', 'prior_public_state', 'new_public_state', 'reason'])]
#[Hidden(['actor_id'])]
class IncidentUpdate extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'incident_updates';

    protected function casts(): array
    {
        return ['prior_public_state' => 'array', 'new_public_state' => 'array', 'created_at' => 'datetime'];
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
