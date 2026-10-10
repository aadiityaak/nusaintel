<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { home } from '@/routes';

type Incident = { public_id: string; title: string; summary: string; category: string | null; kecamatan: string | null; latitude: number | null; longitude: number | null; effective_date: string | null; date_label: string; sources: { url: string; publisher: string; title: string; published_at: string | null; excerpt: string | null }[] };
type PageLink = { url: string | null; label: string; active: boolean };
type Paginator = { data: Incident[]; links?: PageLink[]; current_page?: number; last_page?: number; total?: number };
const props = defineProps<{ incidents: Paginator; filters: Record<string, string | null>; options: { categories?: string[]; kecamatans?: string[] }; contact?: string | null }>();
const mapElement = ref<HTMLElement | null>(null);
let map: L.Map | null = null;
let markers: L.LayerGroup | null = null;
const form = reactive({ from: props.filters.from ?? '', to: props.filters.to ?? '', category: props.filters.category ?? '', kecamatan: props.filters.kecamatan ?? '' });
const count = computed(() => props.incidents.total ?? props.incidents.data.length);
const mappedIncidents = computed(() => props.incidents.data.filter((incident) => incident.latitude !== null && incident.longitude !== null));
watch(() => props.filters, (value) => Object.assign(form, value), { deep: true });
function applyFilters() { router.get(home().url, Object.fromEntries(Object.entries(form).filter(([, value]) => value)), { preserveState: true, preserveScroll: true, replace: true }); }
function escapeHtml(value: string): string { return value.replace(/[&<>']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;' })[character] ?? character); }
function renderMarkers(): void { if (!map || !markers) return; const markerLayer = markers; markerLayer.clearLayers(); props.incidents.data.forEach((incident) => { if (incident.latitude === null || incident.longitude === null) return; const marker = L.circleMarker([incident.latitude, incident.longitude], { radius: 7, color: '#d7b56d', weight: 2, fillColor: '#172338', fillOpacity: 0.95 }); marker.bindPopup(`<strong>${escapeHtml(incident.title)}</strong><br><span>Lokasi perkiraan tingkat kecamatan, bukan titik kejadian.</span><br><a href="/incidents/${encodeURIComponent(incident.public_id)}">Buka detail</a>`); markerLayer.addLayer(marker); }); }
onMounted(async () => { await nextTick(); if (!mapElement.value) return; map = L.map(mapElement.value, { zoomControl: false, scrollWheelZoom: false }).setView([-7.705, 110.606], 11); L.control.zoom({ position: 'bottomright' }).addTo(map); L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map); markers = L.layerGroup().addTo(map); renderMarkers(); });
watch(() => props.incidents.data, renderMarkers, { deep: true });
onBeforeUnmount(() => { map?.remove(); map = null; markers = null; });
</script>


<template>
    <Head title="Linimasa insiden publik" />
    <main class="min-h-screen bg-[#0b1220] text-[#e8eef7]">
        <div class="mx-auto max-w-[1600px] px-3 py-3 sm:px-5 lg:px-6">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-[#26364d] pb-3">
                <div><a :href="home().url" class="text-sm font-semibold tracking-[0.18em] text-[#38bdf8]">LINIMASA KLATEN</a><p class="mt-1 text-xs text-[#a5b4c7]">Pemantauan sumber terbuka · cakupan upaya terbaik</p></div>
                <nav class="flex items-center gap-1 text-sm"><a href="/metodologi" class="rounded px-3 py-2 text-[#a5b4c7] hover:bg-[#172338] hover:text-white focus-visible:outline-2 focus-visible:outline-[#38bdf8]">Metodologi</a></nav>
            </header>
            <section aria-labelledby="map-heading" class="relative mt-3 overflow-hidden rounded-lg border border-[#26364d] bg-[#111b2e]">
                <div class="pointer-events-none absolute left-4 top-4 z-[500] max-w-xs rounded-md border border-[#26364d] bg-[#0b1220]/95 p-3 shadow-xl"><h1 id="map-heading" class="text-sm font-semibold text-white">Peta kejadian Klaten</h1><p class="mt-1 text-xs leading-5 text-[#a5b4c7]">{{ mappedIncidents.length }} titik pada halaman ini · lokasi perkiraan tingkat kecamatan, bukan titik kejadian.</p></div>
                <div ref="mapElement" class="h-[58vh] min-h-[420px] w-full bg-[#172338]" aria-label="Peta kejadian publik Klaten"></div>
                <div v-if="!mappedIncidents.length" class="absolute inset-0 z-[400] flex items-center justify-center bg-[#0b1220]/75 p-6 text-center"><div><p class="font-semibold">Belum ada lokasi yang dapat dipetakan</p><p class="mt-1 max-w-md text-sm text-[#a5b4c7]">Data kejadian tetap tersedia pada linimasa di bawah. Titik hanya tampil saat koordinat pusat kecamatan sudah diverifikasi.</p></div></div>
            </section>
            <section class="mt-3 grid gap-3 lg:grid-cols-[minmax(260px,320px)_1fr]">
                <aside class="rounded-lg border border-[#26364d] bg-[#111b2e] p-4"><div class="flex items-center justify-between"><h2 class="text-sm font-semibold">Filter</h2><span class="text-xs tabular-nums text-[#a5b4c7]">{{ count }} insiden</span></div><form class="mt-4 space-y-3" @submit.prevent="applyFilters"><label class="grid gap-1 text-xs text-[#a5b4c7]">Dari<input v-model="form.from" type="date" class="min-h-11 rounded border border-[#26364d] bg-[#0b1220] px-3 text-sm text-white" /></label><label class="grid gap-1 text-xs text-[#a5b4c7]">Sampai<input v-model="form.to" type="date" class="min-h-11 rounded border border-[#26364d] bg-[#0b1220] px-3 text-sm text-white" /></label><label class="grid gap-1 text-xs text-[#a5b4c7]">Kategori<select v-model="form.category" class="min-h-11 rounded border border-[#26364d] bg-[#0b1220] px-3 text-sm text-white"><option value="">Semua kategori</option><option v-for="item in options.categories ?? []" :key="item" :value="item">{{ item }}</option></select></label><label class="grid gap-1 text-xs text-[#a5b4c7]">Kecamatan<select v-model="form.kecamatan" class="min-h-11 rounded border border-[#26364d] bg-[#0b1220] px-3 text-sm text-white"><option value="">Semua kecamatan</option><option v-for="item in options.kecamatans ?? []" :key="item" :value="item">{{ item }}</option></select></label><button class="min-h-11 w-full rounded bg-[#38bdf8] px-3 text-sm font-semibold text-[#0b1220] hover:bg-[#60a5fa] focus-visible:outline-2 focus-visible:outline-[#38bdf8]">Terapkan filter</button></form><p class="mt-5 border-t border-[#26364d] pt-4 text-xs leading-5 text-[#71839b]">© OpenStreetMap contributors · peta tidak menyatakan lokasi kejadian secara presisi.</p></aside>
                <section aria-label="Daftar insiden" class="space-y-3"><div class="flex items-center justify-between"><h2 class="text-sm font-semibold">Linimasa terbaru</h2><span class="text-xs text-[#71839b]">Halaman {{ incidents.current_page ?? 1 }}</span></div><article v-for="incident in incidents.data" :key="incident.public_id" class="rounded-lg border border-[#26364d] bg-[#111b2e] p-4 hover:border-[#38bdf8]/60"><div class="flex flex-wrap justify-between gap-2"><div><p class="text-xs font-medium uppercase tracking-wide text-[#38bdf8]">{{ incident.category ?? 'Kategori belum tersedia' }}</p><h3 class="mt-1 text-base font-semibold"><a :href="`/incidents/${encodeURIComponent(incident.public_id)}`" class="hover:text-[#38bdf8] focus-visible:outline-2 focus-visible:outline-[#38bdf8]">{{ incident.title }}</a></h3></div><span class="text-xs text-[#a5b4c7]">{{ incident.date_label }}</span></div><p class="mt-3 text-sm leading-6 text-[#a5b4c7]">{{ incident.summary }}</p><p class="mt-3 text-xs text-[#71839b]">{{ incident.kecamatan ?? 'Wilayah tidak diketahui' }} · {{ incident.sources.length }} sumber</p></article><div v-if="!incidents.data.length" class="rounded-lg border border-dashed border-[#26364d] p-8 text-center text-sm text-[#a5b4c7]">Tidak ada temuan untuk filter ini.</div></section>
            </section>
            <nav v-if="incidents.links?.length" aria-label="Halaman linimasa" class="mt-4 flex flex-wrap gap-2"><a v-for="link in incidents.links" :key="link.label" :href="link.url ?? undefined" :aria-current="link.active ? 'page' : undefined" :tabindex="link.url ? 0 : -1" class="min-h-11 rounded border border-[#26364d] px-3 py-2 text-sm hover:bg-[#172338] focus-visible:outline-2 focus-visible:outline-[#38bdf8]" :class="{ 'bg-[#38bdf8] font-semibold text-[#0b1220]': link.active, 'pointer-events-none opacity-50': !link.url }" v-html="link.label" /></nav>
        </div>
    </main>
</template>
