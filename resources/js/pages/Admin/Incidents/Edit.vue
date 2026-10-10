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
    <main class="min-h-screen bg-background text-foreground">
        <div class="mx-auto max-w-3xl space-y-8 p-6 lg:p-10">
            <header class="border-b border-border pb-7"><p class="mb-2 text-sm font-semibold uppercase tracking-wide text-primary">Ruang kerja analis</p><h1 class="text-3xl font-semibold tracking-tight">{{ incident ? 'Edit kejadian' : 'Buat draf kejadian' }}</h1><p class="mt-2 text-muted-foreground">Pisahkan fakta laporan sumber dari interpretasi. Simpan alasan setiap perubahan.</p></header>
            <form class="space-y-5 rounded-xl border border-border bg-card p-5 shadow-sm sm:p-6" @submit.prevent="save">
                <label class="block text-sm font-medium">Judul<input v-model="form.title" required maxlength="255" class="mt-1.5 block min-h-10 w-full rounded-md border border-input bg-background px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" /></label>
                <label class="block text-sm font-medium">Ringkasan<textarea v-model="form.summary" required maxlength="5000" rows="5" class="mt-1.5 block w-full rounded-md border border-input bg-background px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" /></label>
                <div class="grid gap-5 sm:grid-cols-2"><label class="block text-sm font-medium">Kategori<input v-model="form.category" required maxlength="100" class="mt-1.5 block min-h-10 w-full rounded-md border border-input bg-background px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" /></label><label class="block text-sm font-medium">Kecamatan<input v-model="form.kecamatan" required maxlength="150" class="mt-1.5 block min-h-10 w-full rounded-md border border-input bg-background px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" /></label></div>
                <div class="grid gap-5 sm:grid-cols-2"><label class="block text-sm font-medium">Tanggal kejadian<input v-model="form.occurred_at" type="date" class="mt-1.5 block min-h-10 w-full rounded-md border border-input bg-background px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" /></label><label class="block text-sm font-medium">Presisi waktu<select v-model="form.occurred_at_precision" class="mt-1.5 block min-h-10 w-full rounded-md border border-input bg-background px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring"><option value="">Tidak diketahui</option><option value="exact">Tepat</option><option value="approximate">Perkiraan</option><option value="unknown">Tidak diketahui</option></select></label></div>
                <label class="block text-sm font-medium">Tanggal publikasi artikel<input v-model="form.published_at" type="date" class="mt-1.5 block min-h-10 w-full rounded-md border border-input bg-background px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" /></label>
                <label class="block text-sm font-medium">Alasan perubahan<textarea v-model="form.reason" required maxlength="2000" rows="4" class="mt-1.5 block w-full rounded-md border border-input bg-background px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" /></label>
                <p v-if="form.errors.reason" role="alert" class="text-sm text-destructive">{{ form.errors.reason }}</p>
                <button class="min-h-10 rounded-md bg-primary px-5 py-2 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" :disabled="form.processing">{{ form.processing ? 'Menyimpan…' : 'Simpan perubahan' }}</button>
            </form>
            <section v-if="incident" aria-labelledby="transition-heading" class="space-y-4 border-t border-border pt-7"><div><h2 id="transition-heading" class="text-lg font-semibold">Keputusan publikasi</h2><p class="mt-1 text-sm text-muted-foreground">Tindakan ini mengubah status yang terlihat di area publik.</p></div><div class="flex flex-wrap gap-3"><button v-if="incident.status !== 'published'" type="button" class="min-h-10 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" @click="transition('published')">Setujui dan publikasikan</button><button v-if="incident.status === 'published'" type="button" class="min-h-10 rounded-md border border-border px-4 py-2 text-sm font-medium hover:bg-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" @click="transition('draft')">Kembalikan ke draf</button><button v-if="incident.status !== 'rejected'" type="button" class="min-h-10 rounded-md border border-destructive/40 px-4 py-2 text-sm font-medium text-destructive hover:bg-destructive/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring" @click="transition('rejected')">Tolak</button></div></section>
        </div>
    </main>
</template>
