<?php

namespace Tests\Feature;

use App\Jobs\ProcessCrimeSource;
use App\Jobs\SearchKlatenSources;
use App\Models\CrawlRun;
use App\Models\Incident;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrimeSourceIngestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_brave_results_create_canonical_sources_once_and_record_completed_run(): void
    {
        config(['crime-timeline.brave_api_key' => 'brave-test-secret', 'crime-timeline.search.queries' => ['pencurian']]);
        Bus::fake();
        Http::fake(['*' => Http::response([
            'web' => ['results' => [
                ['url' => 'https://news.example/story?utm_source=search'],
                ['url' => 'https://news.example/story'],
            ]],
        ])]);

        (new SearchKlatenSources)->handle();
        (new SearchKlatenSources)->handle();

        $this->assertDatabaseCount('sources', 1);
        $this->assertDatabaseHas('sources', ['url' => 'https://news.example/story', 'fetch_status' => 'pending']);
        $this->assertDatabaseCount('crawl_runs', 2);
        $this->assertDatabaseHas('crawl_runs', ['status' => 'completed', 'results_found' => 2, 'sources_created' => 1]);
        Bus::assertDispatched(ProcessCrimeSource::class, 1);
        Http::assertSent(fn (Request $request) => $request->hasHeader('X-Subscription-Token', 'brave-test-secret')
            && $request['country'] === 'ID'
            && str_contains($request['q'], 'Klaten'));
    }

    public function test_brave_api_errors_record_safe_summary_without_credentials(): void
    {
        config(['crime-timeline.brave_api_key' => 'brave-private-secret', 'crime-timeline.search.queries' => ['pencurian']]);
        Http::fake(['https://api.search.brave.com/res/v1/web/search' => Http::response(['message' => 'brave-private-secret'], 500)]);

        (new SearchKlatenSources)->handle();

        $run = CrawlRun::sole();
        $this->assertSame('failed', $run->status);
        $this->assertSame('Search provider returned an unsuccessful response.', $run->error_summary);
        $this->assertStringNotContainsString('brave-private-secret', $run->error_summary);
        $this->assertSame(0, Source::count());
    }

    public function test_malformed_brave_response_records_safe_failure(): void
    {
        config(['crime-timeline.brave_api_key' => 'brave-test-secret', 'crime-timeline.search.queries' => ['pencurian']]);
        Http::fake(['*' => Http::response(['unexpected' => 'brave-test-secret'])]);

        (new SearchKlatenSources)->handle();

        $run = CrawlRun::sole();
        $this->assertSame('failed', $run->status);
        $this->assertSame('Search response or processing failed.', $run->error_summary);
        $this->assertStringNotContainsString('brave-test-secret', $run->error_summary);
    }

    public function test_fetch_rejects_localhost_and_private_ip_without_http_request(): void
    {
        Http::fake();
        $local = Source::factory()->create(['url' => 'http://localhost/private']);
        $private = Source::factory()->create(['url' => 'http://192.168.1.10/private']);

        (new ProcessCrimeSource($local->id))->handle();
        (new ProcessCrimeSource($private->id))->handle();

        $this->assertDatabaseHas('sources', ['id' => $local->id, 'fetch_status' => 'failed']);
        $this->assertDatabaseHas('sources', ['id' => $private->id, 'fetch_status' => 'failed']);
        Http::assertNothingSent();
    }

    public function test_fetch_rejects_redirect_to_private_address_and_marks_source_failed(): void
    {
        Http::fake(['https://93.184.216.34/*' => Http::response('', 302, ['Location' => 'http://127.0.0.1/admin'])]);
        $source = Source::factory()->create(['url' => 'https://93.184.216.34/story']);

        (new ProcessCrimeSource($source->id))->handle();

        $source->refresh();
        $this->assertSame('failed', $source->fetch_status);
        $this->assertSame('Fetch rejected or unsupported response.', $source->fetch_error);
        Http::assertSentCount(1);
        $this->assertSame(0, Incident::count());
    }

    public function test_missing_extraction_provider_marks_source_failed_and_creates_no_incident(): void
    {
        Http::fake(['https://93.184.216.34/*' => Http::response('Public article text.', 200, ['Content-Type' => 'text/plain'])]);
        config(['crime-timeline.nine_router_api_key' => null, 'crime-timeline.nine_router_model' => 'test-model', 'crime-timeline.nine_router_endpoint' => 'https://router.example/chat']);
        $source = Source::factory()->create(['url' => 'https://93.184.216.34/story']);

        (new ProcessCrimeSource($source->id))->handle();

        $source->refresh();
        $this->assertSame('failed', $source->fetch_status);
        $this->assertSame('Extraction provider configuration unavailable.', $source->fetch_error);
        $this->assertSame(0, Incident::count());
        Http::assertSentCount(1);
    }

    public function test_fetch_rejects_response_larger_than_configured_byte_limit(): void
    {
        Http::fake(['https://93.184.216.34/*' => Http::response('12345', 200, ['Content-Type' => 'text/plain'])]);
        config(['crime-timeline.fetch.max_bytes' => 4]);
        $source = Source::factory()->create(['url' => 'https://93.184.216.34/story']);

        (new ProcessCrimeSource($source->id))->handle();

        $this->assertSame('failed', $source->fresh()->fetch_status);
        $this->assertSame('Fetch rejected or unsupported response.', $source->fresh()->fetch_error);
        $this->assertSame(0, Incident::count());
    }

    public function test_empty_taxonomy_fails_closed_to_draft_even_for_official_candidate(): void
    {
        config([
            'crime-timeline.categories' => [],
            'crime-timeline.kecamatan' => [],
            'crime-timeline.nine_router_api_key' => 'router-test-secret',
            'crime-timeline.nine_router_model' => 'test-model',
            'crime-timeline.nine_router_endpoint' => 'https://router.example/chat',
        ]);
        $payload = [
            'title' => 'Police report on theft', 'summary' => 'Police report describes a theft in Klaten.',
            'category' => 'theft', 'occurred_at' => '2026-09-01', 'occurred_at_precision' => 'exact',
            'published_at' => '2026-09-02', 'kecamatan' => 'Klaten Tengah',
            'evidence_excerpt' => 'Police report describes a theft in Klaten.', 'source_type' => 'official',
            'official_confidence' => 0.99, 'official_release' => true, 'sensitive_flags' => [], 'identity_strings' => [],
        ];
        Http::fake([
            'https://93.184.216.34/story' => Http::response('Police report describes a theft in Klaten.', 200, ['Content-Type' => 'text/plain']),
            'https://router.example/chat' => Http::response(['choices' => [['message' => ['content' => json_encode($payload)]]]]),
        ]);
        $source = Source::factory()->create(['url' => 'https://93.184.216.34/story']);

        (new ProcessCrimeSource($source->id))->handle();

        $incident = Incident::sole();
        $this->assertSame('draft', $incident->status);
        $this->assertSame('unclassified', $incident->category);
        $this->assertNull($incident->published_at_app);
        $this->assertSame('fetched', $source->fresh()->fetch_status);
        Http::assertSent(fn (Request $request) => $request->url() === 'https://router.example/chat'
            && ! str_contains(json_encode($request->data()), 'router-test-secret'));
    }
}
