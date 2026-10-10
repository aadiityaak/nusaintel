<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, reactive, watch } from 'vue';
import { home } from '@/routes';

type Incident = {
    public_id: string;
    title: string;
    summary: string;
    category: string | null;
    kecamatan: string | null;
    occurred_at: string | null;
    occurred_at_precision: string | null;
    published_at: string | null;
    effective_date: string | null;
    date_label: string;
    sources: {
        url: string;
        publisher: string;
        title: string;
        published_at: string | null;
        excerpt: string | null;
    }[];
};
type PageLink = { url: string | null; label: string; active: boolean };
type Paginator = {
    data: Incident[];
    links?: PageLink[];
    current_page?: number;
    last_page?: number;
    total?: number;
};
const props = defineProps<{
    incidents: Paginator;
    filters: Record<string, string | null>;
    options: { categories?: string[]; kecamatans?: string[] };
    contact?: string | null;
}>();
const form = reactive({
    from: props.filters.from ?? '',
    to: props.filters.to ?? '',
    category: props.filters.category ?? '',
    kecamatan: props.filters.kecamatan ?? '',
});
watch(
    () => props.filters,
    (value) => Object.assign(form, value),
    { deep: true },
);
const count = computed(
    () => props.incidents.total ?? props.incidents.data.length,
);
function applyFilters() {
    router.get(
        home().url,
        Object.fromEntries(Object.entries(form).filter(([, value]) => value)),
        { preserveState: true, preserveScroll: true, replace: true },
    );
}
</script>

<template>
    <Head title="Linimasa insiden publik" />
    <main class="mx-auto max-w-5xl space-y-8 p-6">
        <header class="space-y-2">
            <h1 class="text-3xl font-semibold">Linimasa Klaten</h1>
            <p>
                Temuan publik dari sumber yang tersedia; cakupan bersifat upaya
                terbaik dan tidak menyeluruh.
            </p>
            <a href="/metodologi" class="underline">Metodologi</a>
        </header>
        <form
            class="flex flex-wrap items-end gap-4"
            @submit.prevent="applyFilters"
        >
            <label class="grid gap-1"
                >Dari tanggal<input
                    v-model="form.from"
                    name="from"
                    type="date"
                    class="rounded border px-3 py-2"
            /></label>
            <label class="grid gap-1"
                >Sampai tanggal<input
                    v-model="form.to"
                    name="to"
                    type="date"
                    class="rounded border px-3 py-2"
            /></label>
            <label class="grid gap-1"
                >Kategori<select
                    v-model="form.category"
                    name="category"
                    class="rounded border px-3 py-2"
                >
                    <option value="">Semua kategori</option>
                    <option
                        v-for="item in options.categories ?? []"
                        :key="item"
                        :value="item"
                    >
                        {{ item }}
                    </option>
                </select></label
            >
            <label class="grid gap-1"
                >Kecamatan<select
                    v-model="form.kecamatan"
                    name="kecamatan"
                    class="rounded border px-3 py-2"
                >
                    <option value="">Semua kecamatan</option>
                    <option
                        v-for="item in options.kecamatans ?? []"
                        :key="item"
                        :value="item"
                    >
                        {{ item }}
                    </option>
                </select></label
            >
            <button type="submit" class="min-h-11 rounded border px-4 py-2">
                Terapkan filter
            </button>
        </form>
        <p role="status">{{ count }} insiden</p>
        <section aria-label="Peta insiden" class="rounded border p-4">
            <h2 class="font-semibold">Peta belum tersedia</h2>
            <p>
                Tidak tersedia 26 koordinat perwakilan kecamatan yang
                terverifikasi di repositori. Karena itu, peta tidak menampilkan
                titik perkiraan.
            </p>
        </section>
        <section aria-label="Daftar insiden" class="space-y-4">
            <article
                v-for="incident in incidents.data"
                :key="incident.public_id"
                class="space-y-2 rounded border p-4"
            >
                <h2 class="text-xl font-semibold">
                    <a
                        :href="`/incidents/${encodeURIComponent(incident.public_id)}`"
                        class="underline"
                        >{{ incident.title }}</a
                    >
                </h2>
                <p>{{ incident.summary }}</p>
                <dl class="flex flex-wrap gap-x-5 gap-y-1 text-sm">
                    <div>
                        <dt class="inline font-semibold">Tanggal:</dt>
                        <dd class="inline">
                            {{ incident.effective_date ?? 'Tidak tersedia' }}
                            ({{ incident.date_label }})
                        </dd>
                    </div>
                    <div v-if="incident.occurred_at">
                        <dt class="inline font-semibold">Tanggal artikel:</dt>
                        <dd class="inline">
                            {{ incident.published_at ?? 'Tidak tersedia' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="inline font-semibold">Kategori:</dt>
                        <dd class="inline">
                            {{ incident.category ?? 'Tidak diklasifikasikan' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="inline font-semibold">Kecamatan:</dt>
                        <dd class="inline">
                            {{ incident.kecamatan ?? 'Tidak diketahui' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="inline font-semibold">Sumber:</dt>
                        <dd class="inline">
                            <a
                                v-for="(source, index) in incident.sources"
                                :key="source.url"
                                :href="source.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mr-2 underline"
                                >{{
                                    source.publisher ||
                                    source.title ||
                                    `Sumber ${index + 1}`
                                }}</a
                            ><span v-if="!incident.sources.length"
                                >Tidak tersedia</span
                            >
                        </dd>
                    </div>
                </dl>
            </article>
            <p v-if="!incidents.data.length">
                Tidak ada insiden untuk filter ini.
            </p>
        </section>
        <nav
            v-if="incidents.links?.length"
            aria-label="Halaman linimasa"
            class="flex flex-wrap gap-2"
        >
            <a
                v-for="link in incidents.links"
                :key="link.label"
                :href="link.url ?? undefined"
                :aria-current="link.active ? 'page' : undefined"
                :aria-disabled="!link.url"
                :tabindex="link.url ? 0 : -1"
                class="rounded border px-3 py-2"
                :class="{
                    'font-bold': link.active,
                    'pointer-events-none opacity-50': !link.url,
                }"
                v-html="link.label"
            />
        </nav>
        <section id="metodologi" class="space-y-3 border-t pt-6">
            <h2 class="text-2xl font-semibold">Metodologi</h2>
            <p>
                Linimasa menampilkan insiden yang telah ditinjau dan
                dipublikasikan. Tanggal kejadian dan tanggal artikel ditampilkan
                terpisah; tanggal kejadian mempertahankan presisi yang tersedia.
            </p>
            <p>
                Rincian insiden bergantung pada bukti sumber yang tersedia. Nama
                individu tidak ditampilkan. Data merupakan cakupan upaya terbaik
                dan tidak boleh dianggap sebagai daftar lengkap semua insiden.
            </p>
            <p>
                Peta tidak ditampilkan karena repositori tidak menyediakan
                koordinat perwakilan terverifikasi untuk seluruh 26 kecamatan.
            </p>
            <p v-if="contact">
                Koreksi atau pertanyaan:
                <a :href="`mailto:${contact}`" class="underline">{{
                    contact
                }}</a>
            </p>
        </section>
    </main>
</template>
