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
    <main class="mx-auto max-w-3xl space-y-6 p-6">
        <a :href="home().url" class="underline">Kembali ke linimasa</a>
        <article class="space-y-4">
            <h1 class="text-3xl font-semibold">{{ incident.title }}</h1>
            <p>{{ incident.summary }}</p>
            <dl class="grid grid-cols-[max-content_1fr] gap-x-4 gap-y-2">
                <dt>Kategori</dt>
                <dd>{{ incident.category ?? 'Tidak diklasifikasikan' }}</dd>
                <dt>Kecamatan</dt>
                <dd>{{ incident.kecamatan ?? 'Tidak diketahui' }}</dd>
                <dt>Tanggal kejadian</dt>
                <dd>
                    {{ incident.occurred_at ?? 'Tidak diketahui'
                    }}<template v-if="incident.occurred_at_precision">
                        ({{ incident.occurred_at_precision }})</template
                    >
                </dd>
                <dt>{{ incident.date_label }}</dt>
                <dd>{{ incident.effective_date ?? 'Tidak tersedia' }}</dd>
                <dt v-if="incident.occurred_at">Tanggal artikel</dt>
                <dd v-if="incident.occurred_at">
                    {{ incident.published_at ?? 'Tidak tersedia' }}
                </dd>
            </dl>
            <section aria-labelledby="sources-heading" class="space-y-2">
                <h2 id="sources-heading" class="text-xl font-semibold">
                    Sumber dan bukti
                </h2>
                <ul
                    v-if="incident.sources?.length"
                    class="list-disc space-y-2 pl-6"
                >
                    <li
                        v-for="(source, index) in incident.sources"
                        :key="source.url ?? index"
                    >
                        <a
                            v-if="source.url"
                            :href="source.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="underline"
                            >{{ source.title || source.url }}</a
                        >
                        <span v-else>{{ source.title || 'Sumber' }}</span>
                        <span v-if="source.published_at">
                            — tanggal artikel {{ source.published_at }}</span
                        >
                        <p v-if="source.excerpt">{{ source.excerpt }}</p>
                    </li>
                </ul>
                <p v-else>Sumber tidak tersedia.</p>
            </section>
        </article>
        <p v-if="contact">
            Koreksi atau pertanyaan:
            <a :href="`mailto:${contact}`" class="underline">{{ contact }}</a>
        </p>
    </main>
</template>
