<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{ contact: string | null }>();
</script>

<template>
    <Head title="Metodologi" />
    <main class="min-h-screen bg-background text-foreground">
        <div class="mx-auto max-w-3xl space-y-8 px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
            <a href="/" class="text-sm font-medium text-primary underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring">← Kembali ke linimasa</a>
            <header class="border-b border-border pb-8">
                <p class="mb-3 text-sm font-semibold uppercase tracking-wide text-primary">Transparansi data</p>
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Metodologi</h1>
                <p class="mt-4 text-lg leading-8 text-muted-foreground">Cara Linimasa Klaten menemukan, meninjau, dan menyajikan temuan dari sumber publik.</p>
            </header>
            <div class="space-y-6 leading-7">
                <section class="rounded-xl border border-border bg-card p-5 sm:p-6"><h2 class="text-lg font-semibold">Cakupan</h2><p class="mt-2 text-muted-foreground">Linimasa menyusun temuan dari sumber publik yang ditemukan melalui pencarian web harian dan ditinjau sebelum terbit. Pengindeksan tidak mencakup semua laporan; layanan ini bukan daftar lengkap atau statistik kejahatan.</p></section>
                <section class="rounded-xl border border-border bg-card p-5 sm:p-6"><h2 class="text-lg font-semibold">Waktu dan lokasi</h2><p class="mt-2 text-muted-foreground">Tanggal kejadian dibedakan dari tanggal artikel. Jika tanggal kejadian tidak diketahui, tanggal artikel dapat dipakai untuk urutan dan dilabeli sebagai tanggal artikel. Lokasi hanya dilaporkan pada tingkat kecamatan, bukan titik kejadian.</p></section>
                <section class="rounded-xl border border-border bg-card p-5 sm:p-6"><h2 class="text-lg font-semibold">Privasi dan koreksi</h2><p class="mt-2 text-muted-foreground">Nama individu tidak ditampilkan. Bila menemukan kesalahan atau memiliki pertanyaan, hubungi alamat di bawah.</p><p v-if="contact" class="mt-4 text-sm">Kontak: <a :href="`mailto:${contact}`" class="font-medium text-primary underline underline-offset-4">{{ contact }}</a></p></section>
            </div>
        </div>
    </main>
</template>
