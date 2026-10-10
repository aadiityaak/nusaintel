<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIncidentRequest;
use App\Http\Requests\Admin\UpdateIncidentRequest;
use App\Models\Incident;
use App\Models\IncidentUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class IncidentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Incidents/Index', [
            'drafts' => Incident::where('status', 'draft')->latest('updated_at')->paginate(20, ['*'], 'drafts'),
            'published' => Incident::where('status', 'published')->latest('published_at_app')->paginate(20, ['*'], 'published'),
            'rejected' => Incident::where('status', 'rejected')->latest('updated_at')->paginate(20, ['*'], 'rejected'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Incidents/Edit', ['incident' => null]);
    }

    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['reason']);
        $incident = DB::transaction(function () use ($data, $request): Incident {
            $incident = Incident::create(Arr::except($data, ['status', 'publication_basis', 'published_at_app', 'reviewed_by', 'reviewed_at', 'latitude', 'longitude']) + ['status' => 'draft']);
            $this->audit($incident, null, $this->publicState($incident), $request->validated('reason'));

            return $incident;
        });

        return to_route('admin.incidents.edit', $incident)->with('success', 'Incident saved as draft.');
    }

    public function edit(Incident $incident): Response
    {
        return Inertia::render('Admin/Incidents/Edit', ['incident' => $incident]);
    }

    public function update(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['reason']);
        DB::transaction(function () use ($incident, $data, $request): void {
            $before = $this->publicState($incident);
            $incident->fill(Arr::except($data, ['status', 'publication_basis', 'published_at_app', 'reviewed_by', 'reviewed_at', 'latitude', 'longitude']));
            $after = $this->publicState($incident);
            $incident->save();
            if ($before !== $after) {
                $this->audit($incident, $before, $after, $request->validated('reason'));
            }
        });

        return back()->with('success', 'Incident updated.');
    }

    public function transition(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        $status = $request->string('status')->toString();
        abort_unless(in_array($status, ['published', 'draft', 'rejected'], true), 422);
        if ($status === 'published') {
            abort_unless($request->boolean('confirm_publication'), 422, 'Publication confirmation is required.');
        }

        DB::transaction(function () use ($request, $incident, $status): void {
            $before = $this->publicState($incident);
            $incident->status = $status;
            $incident->publication_basis = $status === 'published' ? 'admin_review' : null;
            $incident->published_at_app = $status === 'published' ? now() : null;
            $incident->reviewed_by = $request->user()->id;
            $incident->reviewed_at = now();
            $incident->save();
            $this->audit($incident, $before, $this->publicState($incident), $request->validated('reason'));
        });

        return back()->with('success', 'Incident status updated.');
    }

    private function publicState(Incident $incident): array
    {
        return [
            'status' => $incident->status,
            'title' => $incident->title,
            'summary' => $incident->summary,
            'category' => $incident->category,
            'occurred_at' => $incident->occurred_at?->toDateString(),
            'occurred_at_precision' => $incident->occurred_at_precision,
            'published_at' => $incident->published_at?->toDateString(),
            'kecamatan' => $incident->kecamatan,
        ];
    }

    private function audit(Incident $incident, ?array $before, ?array $after, string $reason): void
    {
        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'actor_id' => request()->user()->id,
            'prior_public_state' => $before,
            'new_public_state' => $after,
            'reason' => $reason,
        ]);
    }
}
