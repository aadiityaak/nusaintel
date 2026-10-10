<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('title');
            $table->text('summary');
            $table->string('category');
            $table->date('occurred_at')->nullable();
            $table->string('occurred_at_precision')->nullable();
            $table->date('published_at')->nullable();
            $table->string('kecamatan');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status')->default('draft');
            $table->string('publication_basis')->nullable();
            $table->timestamp('published_at_app')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'occurred_at', 'published_at']);
            $table->index(['kecamatan', 'category']);
        });

        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->text('url')->unique();
            $table->string('domain');
            $table->string('publisher');
            $table->string('source_type')->default('unknown');
            $table->string('title');
            $table->date('published_at')->nullable();
            $table->text('excerpt');
            $table->string('content_hash')->nullable();
            $table->string('fetch_status')->default('pending');
            $table->text('fetch_error')->nullable();
            $table->timestamps();
        });

        Schema::create('incident_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->text('supporting_excerpt')->nullable();
            $table->timestamps();
            $table->unique(['incident_id', 'source_id']);
        });

        Schema::create('crawl_runs', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('results_found')->default(0);
            $table->unsignedInteger('sources_created')->default(0);
            $table->unsignedInteger('sources_processed')->default(0);
            $table->unsignedInteger('incidents_created')->default(0);
            $table->text('error_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('source_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('domain_type');
            $table->boolean('enabled')->default(true);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('ai_classification')->nullable();
            $table->decimal('ai_confidence', 5, 4)->nullable();
            $table->string('ai_model')->nullable();
            $table->timestamp('ai_classified_at')->nullable();
            $table->text('ai_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('incident_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('prior_public_state')->nullable();
            $table->json('new_public_state')->nullable();
            $table->text('reason');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['incident_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_updates');
        Schema::dropIfExists('source_domains');
        Schema::dropIfExists('crawl_runs');
        Schema::dropIfExists('incident_sources');
        Schema::dropIfExists('sources');
        Schema::dropIfExists('incidents');
    }
};
