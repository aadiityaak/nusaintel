<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{ contact: string | null }>();
</script>

<template>
    <Head title="Metodologi" />
    <main class="mx-auto max-w-3xl space-y-4 p-6">
        <h1 class="text-3xl font-semibold">Metodologi</h1>
        <p>
            Linimasa menyusun temuan dari sumber publik yang ditemukan melalui
            pencarian web harian dan ditinjau sebelum terbit. Pengindeksan tidak
            mencakup semua laporan; layanan ini bukan daftar lengkap atau
            statistik kejahatan.
        </p>
        <p>
            Tanggal kejadian dibedakan dari tanggal artikel. Jika tanggal
            kejadian tidak diketahui, tanggal artikel dapat dipakai untuk urutan
            dan dilabeli sebagai tanggal artikel.
        </p>
        <p>
            Lokasi hanya dilaporkan pada tingkat kecamatan, bukan titik
            kejadian. Nama individu tidak ditampilkan.
        </p>
        <p v-if="contact">
            Untuk koreksi atau pertanyaan:
            <a :href="`mailto:${contact}`" class="underline">{{ contact }}</a>
        </p>
    </main>
</template>
