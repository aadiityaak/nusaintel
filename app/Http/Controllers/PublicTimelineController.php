<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PublicTimelineController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = Validator::make($request->query(), [
            'category' => ['sometimes', 'string', 'max:100', Rule::in(config('crime-timeline.categories', []))],
            'kecamatan' => ['sometimes', 'string', 'max:150', Rule::in(config('crime-timeline.kecamatan', []))],
            'from' => ['sometimes', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:from'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ])->validate();

        $query = Incident::query()
            ->where('status', 'published')
            ->with('sources:id,url,publisher,title,published_at,excerpt')
            ->select(['id', 'public_id', 'title', 'summary', 'category', 'occurred_at', 'occurred_at_precision', 'published_at', 'kecamatan', 'latitude', 'longitude'])
            ->when(isset($filters['category']), fn ($query) => $query->where('category', $filters['category']))
            ->when(isset($filters['kecamatan']), fn ($query) => $query->where('kecamatan', $filters['kecamatan']))
            ->when(isset($filters['from']), fn ($query) => $query->whereRaw('COALESCE(occurred_at, published_at) >= ?', [$filters['from']]))
            ->when(isset($filters['to']), fn ($query) => $query->whereRaw('COALESCE(occurred_at, published_at) <= ?', [$filters['to']]));
        $query
            ->orderByRaw('COALESCE(occurred_at, published_at) DESC')
            ->orderByDesc('id');

        return Inertia::render('Timeline', [
            'incidents' => $query->paginate(20)->withQueryString()->through(fn (Incident $incident) => $this->publicIncident($incident)),
            'filters' => array_intersect_key($filters, array_flip(['category', 'kecamatan', 'from', 'to'])),
            'contact' => config('crime-timeline.contact'),
            'options' => ['categories' => config('crime-timeline.categories', []), 'kecamatans' => config('crime-timeline.kecamatan', [])],
        ]);
    }

    public function show(string $publicId): Response
    {
        $incident = Incident::query()->where('public_id', $publicId)->where('status', 'published')->firstOrFail();

        $incident->load('sources:id,url,publisher,title,published_at,excerpt');

        return Inertia::render('Incidents/Show', ['incident' => $this->publicIncident($incident), 'contact' => config('crime-timeline.contact')]);
    }

    public function methodology(): Response
    {
        return Inertia::render('Methodology', ['contact' => config('crime-timeline.contact')]);
    }

    private function publicIncident(Incident $incident): array
    {
        return [
            'public_id' => $incident->public_id,
            'title' => $incident->title,
            'summary' => $incident->summary,
            'category' => $incident->category,
            'occurred_at' => $incident->occurred_at?->toDateString(),
            'occurred_at_precision' => $incident->occurred_at_precision,
            'published_at' => $incident->published_at?->toDateString(),
            'effective_date' => $incident->occurred_at?->toDateString() ?? $incident->published_at?->toDateString(),
            'date_label' => $incident->occurred_at === null ? 'Tanggal artikel; tanggal kejadian tidak diketahui' : 'Tanggal kejadian',
            'latitude' => $incident->latitude !== null ? (float) $incident->latitude : null,
            'longitude' => $incident->longitude !== null ? (float) $incident->longitude : null,
            'sources' => $incident->sources->map(fn ($source) => [
                'url' => $source->url,
                'publisher' => $source->publisher,
                'title' => $source->title,
                'published_at' => $source->published_at?->toDateString(),
                'excerpt' => $source->pivot->supporting_excerpt ?: $source->excerpt,
            ])->values()->all(),
        ];
    }
}
