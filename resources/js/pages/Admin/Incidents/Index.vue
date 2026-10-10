<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

type Incident = {
    id: number;
    title: string;
    category: string;
    kecamatan: string;
    status: string;
    public_id: string;
};
type Page = { data: Incident[] };
type Queue = { label: string; rows: Incident[] };
const props = defineProps<{ drafts: Page; published: Page; rejected: Page }>();
const queues: Queue[] = [
    { label: 'Drafts', rows: props.drafts.data },
    { label: 'Published', rows: props.published.data },
    { label: 'Rejected', rows: props.rejected.data },
];
</script>

<template>
    <main class="mx-auto max-w-5xl space-y-8 p-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Incident management</h1>
            <Link
                href="/admin/incidents/create"
                class="rounded border px-4 py-3"
                >Create draft</Link
            >
        </header>
        <section v-for="queue in queues" :key="queue.label">
            <h2 class="mb-3 text-lg font-medium">{{ queue.label }}</h2>
            <p v-if="!queue.rows.length" class="text-muted-foreground">
                No records.
            </p>
            <ul class="divide-y rounded border">
                <li
                    v-for="row in queue.rows"
                    :key="row.id"
                    class="flex items-center justify-between gap-4 p-4"
                >
                    <span
                        >{{ row.title }} · {{ row.category }} ·
                        {{ row.kecamatan }}</span
                    >
                    <Link
                        :href="`/admin/incidents/${row.id}/edit`"
                        class="rounded border px-4 py-3"
                        >Edit</Link
                    >
                </li>
            </ul>
        </section>
    </main>
</template>
