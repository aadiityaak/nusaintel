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
    <main class="min-h-screen bg-background text-foreground">
        <div class="mx-auto max-w-6xl space-y-8 p-6 lg:p-10">
            <header class="flex flex-col gap-4 border-b border-border pb-7 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="mb-2 text-sm font-semibold uppercase tracking-wide text-primary">Ruang kerja analis</p><h1 class="text-3xl font-semibold tracking-tight">Antrean kejadian</h1><p class="mt-2 text-muted-foreground">Tinjau status publikasi, sumber, dan koreksi sebelum data tampil.</p></div>
                <Link href="/admin/incidents/create" class="inline-flex min-h-10 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring">Buat draf</Link>
            </header>
            <section v-for="queue in queues" :key="queue.label" class="space-y-3">
                <div class="flex items-baseline justify-between"><h2 class="text-lg font-semibold">{{ queue.label === 'Drafts' ? 'Menunggu review' : queue.label === 'Published' ? 'Disetujui' : 'Ditolak' }}</h2><span class="text-sm tabular-nums text-muted-foreground">{{ queue.rows.length }}</span></div>
                <div v-if="!queue.rows.length" class="rounded-xl border border-dashed border-border bg-muted/40 p-6 text-sm text-muted-foreground">Tidak ada kejadian pada antrean ini.</div>
                <ul v-else class="divide-y divide-border overflow-hidden rounded-xl border border-border bg-card">
                    <li v-for="row in queue.rows" :key="row.id" class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0"><h3 class="font-semibold">{{ row.title }}</h3><p class="mt-1 text-sm text-muted-foreground">{{ row.category }} · {{ row.kecamatan }} · {{ row.status }}</p></div>
                        <Link :href="`/admin/incidents/${row.id}/edit`" class="inline-flex min-h-10 items-center justify-center rounded-md border border-border px-4 py-2 text-sm font-medium transition-colors hover:bg-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring">Buka review</Link>
                    </li>
                </ul>
            </section>
        </div>
    </main>
</template>
