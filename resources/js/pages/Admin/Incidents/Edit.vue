<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

type Incident = {
    id: number;
    title: string;
    summary: string;
    category: string;
    occurred_at: string | null;
    occurred_at_precision: string | null;
    published_at: string | null;
    kecamatan: string;
    status: string;
};
const props = defineProps<{ incident: Incident | null }>();
const form = useForm({
    title: props.incident?.title ?? '',
    summary: props.incident?.summary ?? '',
    category: props.incident?.category ?? '',
    occurred_at: props.incident?.occurred_at ?? '',
    occurred_at_precision: props.incident?.occurred_at_precision ?? '',
    published_at: props.incident?.published_at ?? '',
    kecamatan: props.incident?.kecamatan ?? '',
    reason: '',
    status: '',
    confirm_publication: false,
});
function save() {
    form.submit(
        props.incident ? 'put' : 'post',
        props.incident
            ? `/admin/incidents/${props.incident.id}`
            : '/admin/incidents',
    );
}
function transition(status: string) {
    if (!form.reason.trim()) {
        form.setError('reason', 'Reason required for status changes.');
        return;
    }
    form.status = status;
    if (
        status === 'published' &&
        !window.confirm(
            'Publishing makes this incident publicly visible. Continue?',
        )
    )
        return;
    form.confirm_publication = status === 'published';
    form.post(`/admin/incidents/${props.incident?.id}/transition`);
}
</script>

<template>
    <main class="mx-auto max-w-3xl space-y-5 p-6">
        <h1 class="text-2xl font-semibold">
            {{ incident ? 'Edit incident' : 'Create incident draft' }}
        </h1>
        <form class="space-y-4" @submit.prevent="save">
            <label class="block"
                >Title<input
                    v-model="form.title"
                    required
                    maxlength="255"
                    class="mt-1 block w-full rounded border p-3"
            /></label>
            <label class="block"
                >Summary<textarea
                    v-model="form.summary"
                    required
                    maxlength="5000"
                    class="mt-1 block w-full rounded border p-3"
                />
            </label>
            <label class="block"
                >Category<input
                    v-model="form.category"
                    required
                    maxlength="100"
                    class="mt-1 block w-full rounded border p-3"
            /></label>
            <label class="block"
                >Kecamatan<input
                    v-model="form.kecamatan"
                    required
                    maxlength="150"
                    class="mt-1 block w-full rounded border p-3"
            /></label>
            <label class="block"
                >Incident date<input
                    v-model="form.occurred_at"
                    type="date"
                    class="mt-1 block w-full rounded border p-3"
            /></label>
            <label class="block"
                >Date precision<select
                    v-model="form.occurred_at_precision"
                    class="mt-1 block w-full rounded border p-3"
                >
                    <option value="">Unknown</option>
                    <option value="exact">Exact</option>
                    <option value="approximate">Approximate</option>
                    <option value="unknown">Unknown</option>
                </select></label
            >
            <label class="block"
                >Article publication date<input
                    v-model="form.published_at"
                    type="date"
                    class="mt-1 block w-full rounded border p-3"
            /></label>
            <label class="block"
                >Reason for change<textarea
                    v-model="form.reason"
                    required
                    maxlength="2000"
                    class="mt-1 block w-full rounded border p-3"
                />
            </label>
            <button
                class="rounded border px-5 py-3"
                :disabled="form.processing"
            >
                Save
            </button>
        </form>
        <div v-if="incident" class="flex flex-wrap gap-3 border-t pt-5">
            <button
                v-if="incident.status !== 'published'"
                type="button"
                class="rounded border px-5 py-3"
                @click="transition('published')"
            >
                Publish
            </button>
            <button
                v-if="incident.status === 'published'"
                type="button"
                class="rounded border px-5 py-3"
                @click="transition('draft')"
            >
                Unpublish
            </button>
            <button
                v-if="incident.status !== 'rejected'"
                type="button"
                class="rounded border px-5 py-3"
                @click="transition('rejected')"
            >
                Reject
            </button>
        </div>
        <p v-if="form.errors.reason" role="alert">{{ form.errors.reason }}</p>
    </main>
</template>
