<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\Source;
use Illuminate\Database\Seeder;

class DemoIncidentSeeder extends Seeder
{
    public function run(): void
    {
        $cases = [
            [
                'title' => 'Polres Klaten ungkap kasus kekerasan seksual di Karangnongko',
                'summary' => 'Rilis resmi Polres Klaten memberitakan pengungkapan kasus kekerasan seksual yang terjadi di wilayah Karangnongko. Ringkasan ini menghilangkan identitas pribadi; detail sensitif pada sumber tidak disalin.',
                'category' => 'kekerasan_seksual',
                'kecamatan' => 'Karangnongko',
                'occurred_at' => null,
                'published_at' => '2026-02-20',
                'source_title' => 'Polres Klaten Ungkap Kasus Pencabulan Ayah terhadap Anak Kandung Selama 14 Tahun',
                'excerpt' => 'Dalam keterangannya, Kapolres menjelaskan bahwa peristiwa tersebut terjadi di wilayah Kecamatan Karangnongko, Kabupaten Klaten.',
                'url' => 'https://polres.klaten.go.id/2026/02/polres-klaten-ungkap-kasus-pencabulan.html',
            ],
            [
                'title' => 'Rilis patroli keamanan wilayah Karangnongko',
                'summary' => 'Polsek Karangnongko melaporkan kegiatan patroli keamanan dan pencegahan gangguan kamtibmas. Item ini memberi variasi visual linimasa, bukan catatan tindak pidana.',
                'category' => 'gangguan_kamtibmas',
                'kecamatan' => 'Karangnongko',
                'occurred_at' => '2023-10-27',
                'published_at' => '2023-10-27',
                'source_title' => 'Anggota Polsek Karangnongko Giat Patroli Harkamtibmas',
                'excerpt' => 'Anggota Polsek Karangnongko melaksanakan patroli harkamtibmas untuk mengantisipasi gangguan keamanan.',
                'url' => 'https://polres.klaten.go.id/2023/10/anggota-polsek-karangnongko-giat.html',
            ],
        ];
        $representativeCoordinates = [
            'Karangnongko' => [-7.6724769, 110.5431371],
            'Klaten Tengah' => [-7.7138476, 110.6081029],
            'Klaten Utara' => [-7.6901361, 110.6120522],
            'Klaten Selatan' => [-7.7185607, 110.5865944],
            'Wedi' => [-7.7720166, 110.5996961],
            'Cawas' => [-7.7624077, 110.7126773],
            'Prambanan' => [-7.7441544, 110.5110856],
            'Jogonalan' => [-7.7265181, 110.5487592],
            'Jatinom' => [-7.6297879, 110.5829332],
        ];

        $demoCategories = ['pencurian', 'kecelakaan', 'gangguan_kamtibmas', 'penipuan'];
        $demoKecamatans = ['Klaten Tengah', 'Klaten Utara', 'Klaten Selatan', 'Wedi', 'Cawas', 'Prambanan', 'Jogonalan', 'Jatinom'];
        foreach (range(1, 28) as $number) {
            $category = $demoCategories[($number - 1) % count($demoCategories)];
            $kecamatan = $demoKecamatans[($number - 1) % count($demoKecamatans)];
            $date = now()->subDays($number)->toDateString();
            $cases[] = [
                'title' => sprintf('[DEMO LOKAL] Catatan uji %02d — %s di %s', $number, $category, $kecamatan),
                'summary' => 'Data demo lokal untuk menguji tampilan linimasa, filter, pagination, dan detail sumber. Bukan catatan kejadian nyata dan tidak boleh dipakai sebagai data produksi.',
                'category' => $category,
                'kecamatan' => $kecamatan,
                'occurred_at' => $date,
                'published_at' => $date,
                'source_title' => sprintf('Sumber demo lokal %02d', $number),
                'excerpt' => 'Kutipan demo lokal untuk pengujian antarmuka.',
                'url' => sprintf('https://example.test/linimasa-klaten-demo/%02d', $number),
            ];
        }

        foreach ($cases as $case) {
            $incident = Incident::query()->updateOrCreate(
                ['title' => $case['title'], 'kecamatan' => $case['kecamatan']],
                [
                    'summary' => $case['summary'],
                    'category' => $case['category'],
                    'occurred_at' => $case['occurred_at'],
                    'occurred_at_precision' => $case['occurred_at'] === null ? 'unknown' : 'exact',
                    'published_at' => $case['published_at'],
                    'status' => 'published',
                    'publication_basis' => 'admin_review',
                    'published_at_app' => $case['published_at'].' 12:00:00',
                    'latitude' => $representativeCoordinates[$case['kecamatan']][0],
                    'longitude' => $representativeCoordinates[$case['kecamatan']][1],
                ],
            );

            $source = Source::query()->firstOrCreate(
                ['url' => $case['url']],
                [
                    'domain' => 'polres.klaten.go.id',
                    'publisher' => 'Polres Klaten',
                    'source_type' => 'official',
                    'title' => $case['source_title'],
                    'published_at' => $case['published_at'],
                    'excerpt' => $case['excerpt'],
                    'fetch_status' => 'fetched',
                ],
            );

            $incident->sources()->syncWithoutDetaching([
                $source->id => ['supporting_excerpt' => $case['excerpt']],
            ]);
        }
    }
}
