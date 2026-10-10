<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['status', 'started_at', 'ended_at', 'results_found', 'sources_created', 'sources_processed', 'incidents_created', 'error_summary'])]
class CrawlRun extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime'];
    }
}
