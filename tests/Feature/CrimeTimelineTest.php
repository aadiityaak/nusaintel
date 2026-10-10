<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\IncidentUpdate;
use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CrimeTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_only_published_incidents_and_keeps_article_date_distinct(): void
    {
        config(['crime-timeline.categories' => ['theft'], 'crime-timeline.kecamatan' => ['Klaten Tengah']]);
        $published = Incident::factory()->create([
            'status' => 'published', 'title' => 'Published case', 'occurred_at' => null,
            'published_at' => '2025-03-02', 'category' => 'theft', 'kecamatan' => 'Klaten Tengah',
        ]);
        Incident::factory()->create(['status' => 'draft', 'title' => 'Private draft']);
        Incident::factory()->create(['status' => 'rejected', 'title' => 'Private rejected']);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Timeline')
                ->has('incidents.data', 1)
                ->where('incidents.data.0.public_id', $published->public_id)
                ->where('incidents.data.0.occurred_at', null)
                ->where('incidents.data.0.published_at', '2025-03-02')
                ->where('incidents.data.0.effective_date', '2025-03-02')
                ->where('incidents.data.0.date_label', 'Tanggal artikel; tanggal kejadian tidak diketahui')
            );
    }

    public function test_detail_is_public_only_and_exposes_citation_but_not_internal_fields(): void
    {
        $incident = Incident::factory()->create([
            'status' => 'published', 'title' => 'Public case', 'occurred_at' => '2025-04-01',
            'published_at' => '2025-04-02', 'reviewed_by' => User::factory(),
        ]);
        $source = Source::factory()->create([
            'url' => 'https://example.test/report', 'publisher' => 'Example News',
            'title' => 'Report title', 'published_at' => '2025-04-02', 'excerpt' => 'Evidence excerpt',
        ]);
        $incident->sources()->attach($source, ['supporting_excerpt' => 'Claim-specific evidence']);
        $response = $this->get(route('incidents.show', $incident->public_id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Incidents/Show')
                ->where('incident.public_id', $incident->public_id)
                ->where('incident.date_label', 'Tanggal kejadian')
                ->where('incident.sources.0.url', $source->url)
                ->where('incident.sources.0.excerpt', 'Claim-specific evidence')
                ->missing('incident.id')
                ->missing('incident.reviewed_by')
                ->missing('incident.publication_basis')
                ->missing('incident.reviewed_at')
            );
        $this->assertStringNotContainsString('reviewed_by', $response->getContent());
    }

    public function test_draft_rejected_and_unknown_incident_details_return_404(): void
    {
        foreach (['draft', 'rejected'] as $status) {
            $incident = Incident::factory()->create(['status' => $status]);
            $this->get(route('incidents.show', $incident->public_id))->assertNotFound();
        }

        $this->get(route('incidents.show', '00000000-0000-4000-8000-000000000000'))->assertNotFound();
    }

    public function test_timeline_filters_effective_date_and_preserves_filter_query_on_pagination(): void
    {
        config(['crime-timeline.categories' => ['theft'], 'crime-timeline.kecamatan' => ['Klaten Tengah', 'Wedi']]);
        Incident::factory()->create([
            'status' => 'published', 'occurred_at' => null, 'published_at' => '2025-03-05',
            'category' => 'theft', 'kecamatan' => 'Klaten Tengah',
        ]);
        Incident::factory()->create([
            'status' => 'published', 'occurred_at' => '2025-03-06', 'published_at' => '2025-03-01',
            'category' => 'theft', 'kecamatan' => 'Klaten Tengah',
        ]);
        Incident::factory()->create([
            'status' => 'published', 'occurred_at' => '2025-03-05', 'category' => 'theft', 'kecamatan' => 'Wedi',
        ]);
        $this->get(route('home', ['from' => '2025-03-05', 'to' => '2025-03-05', 'kecamatan' => 'Klaten Tengah', 'category' => 'theft']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('incidents.data', 0)
                ->where('filters.from', '2025-03-05')
                ->where('filters.to', '2025-03-05'));

        Incident::factory()->count(21)->create(['status' => 'published', 'occurred_at' => '2025-03-05', 'category' => 'theft', 'kecamatan' => 'Klaten Tengah']);

        for ($index = 0; $index < 21; $index++) {
            Incident::factory()->create(['status' => 'published', 'category' => 'theft', 'kecamatan' => 'Klaten Tengah']);
        }
        $this->get(route('home', ['category' => 'theft', 'kecamatan' => 'Klaten Tengah', 'page' => 2]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('incidents.current_page', 2)
                ->where('incidents.per_page', 20)
                ->where('incidents.prev_page_url', fn ($url) => is_string($url) && str_contains($url, 'category=theft') && str_contains(urldecode($url), 'kecamatan=Klaten Tengah'))
            );
    }

    public function test_invalid_public_filters_fail_validation(): void
    {
        config(['crime-timeline.categories' => ['theft'], 'crime-timeline.kecamatan' => ['Klaten Tengah']]);
        $this->get(route('home', ['category' => 'not-allowed']))->assertSessionHasErrors('category');
        $this->get(route('home', ['from' => 'not-a-date']))->assertSessionHasErrors('from');
    }

    public function test_admin_requires_verified_allowlisted_email_and_can_publish_with_audit(): void
    {
        config(['crime-timeline.admin_emails' => ['admin@example.test']]);
        $draft = Incident::factory()->create(['status' => 'draft', 'title' => 'Review case']);
        $unlisted = User::factory()->create(['email' => 'other@example.test']);
        $this->actingAs($unlisted)->get(route('admin.incidents.index'))->assertForbidden();

        $unverified = User::factory()->unverified()->create(['email' => 'admin@example.test']);
        $this->actingAs($unverified)->get(route('admin.incidents.index'))->assertRedirect(route('verification.notice'));

        $admin = User::factory()->create(['email' => 'ADMIN@example.test']);
        $this->actingAs($admin)->get(route('admin.incidents.index'))->assertOk();
        $this->actingAs($admin)->post(route('admin.incidents.transition', $draft), [
            'status' => 'published', 'confirm_publication' => true, 'reason' => 'Reviewed source evidence.',
            'title' => $draft->title, 'summary' => $draft->summary, 'category' => $draft->category,
        ])->assertRedirect();

        $draft->refresh();
        $this->assertSame('published', $draft->status);
        $this->assertSame($admin->id, $draft->reviewed_by);
        $this->assertDatabaseHas('incident_updates', [
            'incident_id' => $draft->id, 'actor_id' => $admin->id, 'reason' => 'Reviewed source evidence.',
        ]);
        $update = IncidentUpdate::query()->where('incident_id', $draft->id)->sole();
        $this->assertSame('draft', $update->prior_public_state['status']);
        $this->assertSame('published', $update->new_public_state['status']);
        $this->get(route('incidents.show', $draft->public_id))->assertOk();
    }

    public function test_missing_admin_allowlist_denies_incident_management(): void
    {
        config(['crime-timeline.admin_emails' => []]);
        $this->actingAs(User::factory()->create())->get(route('admin.incidents.index'))->assertForbidden();
    }
}
