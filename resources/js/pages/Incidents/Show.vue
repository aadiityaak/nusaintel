<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import { home } from '@/routes';

type Source = {
    title?: string;
    url?: string;
    published_at?: string | null;
    excerpt?: string | null;
    publisher?: string;
};
type Incident = {
    title: string;
    summary: string;
    category: string | null;
    occurred_at: string | null;
    occurred_at_precision: string | null;
    published_at: string | null;
    effective_date: string | null;
    date_label: string;
    kecamatan: string | null;
    public_id: string;
    sources?: Source[];
};

const props = defineProps<{ incident: Incident; contact?: string | null }>();
</script>

<template>
    <Head :title="incident.title" />
    <main class="min-h-screen bg-background text-foreground">
        <div class="mx-auto max-w-4xl space-y-8 px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
            <a :href="home().url" class="text-sm font-medium text-primary underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring">← Kembali ke linimasa</a>
            <article class="space-y-8">
                <header class="border-b border-border pb-8">
                    <p class="mb-3 text-sm font-semibold uppercase tracking-wide text-primary">Detail kejadian · {{ incident.category ?? 'Kategori belum tersedia' }}</p>
                    <h1 class="max-w-3xl text-3xl font-semibold tracking-tight sm:text-4xl">{{ incident.title }}</h1>
                    <p class="mt-5 max-w-3xl text-lg leading-8 text-muted-foreground">{{ incident.summary }}</p>
                </header>
                <section aria-labelledby="facts-heading" class="rounded-xl border border-border bg-card p-5 sm:p-6">
                    <h2 id="facts-heading" class="text-lg font-semibold">Ringkasan data</h2>
                    <dl class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div><dt class="text-sm text-muted-foreground">Wilayah</dt><dd class="mt-1 font-medium">{{ incident.kecamatan ?? 'Tidak diketahui' }}</dd></div>
                        <div><dt class="text-sm text-muted-foreground">Tanggal kejadian</dt><dd class="mt-1 font-medium">{{ incident.occurred_at ?? 'Tidak diketahui' }}<span v-if="incident.occurred_at_precision" class="ml-1 text-sm font-normal text-muted-foreground">({{ incident.occurred_at_precision }})</span></dd></div>
                        <div><dt class="text-sm text-muted-foreground">Tanggal yang digunakan</dt><dd class="mt-1 font-medium">{{ incident.effective_date ?? 'Tidak tersedia' }} <span class="text-sm font-normal text-muted-foreground">({{ incident.date_label }})</span></dd></div>
                        <div v-if="incident.occurred_at"><dt class="text-sm text-muted-foreground">Tanggal artikel</dt><dd class="mt-1 font-medium">{{ incident.published_at ?? 'Tidak tersedia' }}</dd></div>
                    </dl>
                </section>
                <section aria-labelledby="sources-heading" class="space-y-4">
                    <div><h2 id="sources-heading" class="text-xl font-semibold">Sumber dan bukti</h2><p class="mt-1 text-sm text-muted-foreground">Klaim di bawah berasal dari sumber yang tercantum.</p></div>
                    <ul v-if="incident.sources?.length" class="space-y-4">
                        <li v-for="(source, index) in incident.sources" :key="source.url ?? index" class="rounded-xl border border-border bg-card p-5">
                            <a v-if="source.url" :href="source.url" target="_blank" rel="noopener noreferrer" class="font-semibold text-primary underline underline-offset-4">{{ source.title || source.url }}</a>
                            <span v-else class="font-semibold">{{ source.title || 'Sumber' }}</span>
                            <p v-if="source.publisher" class="mt-1 text-sm text-muted-foreground">{{ source.publisher }}<span v-if="source.published_at"> · {{ source.published_at }}</span></p>
                            <p v-if="source.excerpt" class="mt-4 border-l-2 border-primary/40 pl-4 text-sm leading-6 text-muted-foreground">“{{ source.excerpt }}”</p>
                        </li>
                    </ul>
                    <p v-else class="rounded-xl border border-dashed border-border bg-muted/40 p-5 text-sm text-muted-foreground">Sumber tidak tersedia.</p>
                </section>
            </article>
            <p v-if="contact" class="border-t border-border pt-6 text-sm text-muted-foreground">Koreksi atau pertanyaan: <a :href="`mailto:${contact}`" class="font-medium text-primary underline underline-offset-4">{{ contact }}</a></p>
        </div>
    </main>
</template>
