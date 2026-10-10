# Nusa Intel --- Design System & Product UI Guidelines

> Dokumen desain untuk agent AI yang membangun Nusa Intel menggunakan
> Laravel Starter Kit + Vue 3 + TypeScript. Gunakan dokumen ini sebagai
> sumber acuan visual dan UX sebelum membuat atau mengubah antarmuka.

## 1. Ringkasan Produk

**Nusa Intel** adalah platform pemantauan dan analisis kejadian
kriminalitas berbasis sumber terbuka. MVP dimulai dari Kabupaten Klaten,
dengan arsitektur yang dapat diperluas ke wilayah Indonesia lainnya.

Produk memiliki dua area yang dipisahkan secara tegas:

1.  **Peta Publik** --- pengalaman publik untuk melihat data kejadian
    yang telah disetujui untuk publikasi.
2.  **Dashboard Analis** --- ruang kerja internal untuk memeriksa
    sumber, mengelola kejadian, meninjau hasil ekstraksi AI, dan
    mengevaluasi kandidat hubungan antarkejadian.

Prinsip utama: **jelas, dapat ditelusuri, tenang, dan tidak membuat
klaim yang melampaui bukti**. Sistem adalah alat bantu analisis, bukan
sistem penetapan tersangka atau penentu kesalahan.

## 2. Sasaran UX

-   Menampilkan banyak informasi tanpa membuat layar terasa sesak atau
    seperti dashboard dekoratif.
-   Mengutamakan peta, waktu, bukti sumber, dan status verifikasi.
-   Memudahkan analis menelusuri setiap ringkasan atau relasi ke sumber
    aslinya.
-   Memisahkan fakta yang dilaporkan sumber, hasil ekstraksi AI, dan
    interpretasi analis.
-   Menampilkan ketidakpastian secara eksplisit.
-   Menjaga data korban, saksi, dan informasi pribadi yang tidak perlu
    agar tidak tampil ke publik.
-   Mendukung penggunaan desktop sebagai prioritas, tetapi tetap usable
    di tablet dan ponsel.

## 3. Arah Visual

### Karakter

-   Profesional, modern, analitis, berorientasi data.
-   Terinspirasi dari ruang kerja intelijen dan geospatial analytics,
    bukan gaya militer agresif.
-   Hindari efek neon berlebihan, glow besar, gradien dekoratif, animasi
    terus-menerus, dan tampilan "hacker".
-   Utamakan kontras, keterbacaan, hierarki informasi, dan kepadatan
    data yang terkontrol.

### Tema

Gunakan **dark theme sebagai tema utama Dashboard Analis**. Peta Publik
dapat menggunakan tema terang atau pilihan terang/gelap, dengan tema
terang sebagai default untuk aksesibilitas dan keterbacaan umum.

Palet acuan (gunakan sebagai design tokens; sesuaikan jika token dari
starter kit sudah tersedia):

  -----------------------------------------------------------------------
  Token                   Nilai                   Penggunaan
  ----------------------- ----------------------- -----------------------
  `--background`          `#0B1220`               Latar utama dashboard

  `--surface`             `#111B2E`               Panel dan sidebar

  `--surface-raised`      `#172338`               Kartu bertingkat,
                                                  dropdown

  `--border`              `#26364D`               Border pemisah halus

  `--text-primary`        `#E8EEF7`               Teks utama

  `--text-secondary`      `#A5B4C7`               Teks pendukung

  `--text-muted`          `#71839B`               Metadata sekunder;
                                                  jangan untuk teks
                                                  penting

  `--accent-cyan`         `#38BDF8`               Seleksi peta, tautan,
                                                  data aktif

  `--accent-blue`         `#60A5FA`               Navigasi dan aksi
                                                  sekunder

  `--accent-amber`        `#FBBF24`               Perlu ditinjau,
                                                  ketidakpastian,
                                                  kandidat

  `--status-success`      `#34D399`               Disetujui atau berhasil

  `--status-danger`       `#F87171`               Error atau perhatian
                                                  penting

  `--status-neutral`      `#94A3B8`               Status netral
  -----------------------------------------------------------------------

Aturan warna: - Merah hanya untuk error, risiko, atau perhatian yang
benar-benar penting; bukan warna utama seluruh aplikasi. - Amber berarti
perlu diperiksa atau masih tentatif, bukan berarti bersalah. - Cyan
menandakan pilihan aktif, data terpilih, atau relasi yang sedang
dilihat; bukan validasi kebenaran. - Status harus selalu memiliki label
teks atau ikon pendamping; jangan mengandalkan warna saja.

### Tipografi

-   Gunakan font sans-serif yang tersedia dari starter kit atau font
    sistem yang mudah dibaca.
-   Hirarki yang disarankan: judul halaman 24--30 px, judul bagian
    16--20 px, isi 14--16 px, metadata 12--13 px.
-   Gunakan angka tabular untuk statistik, timestamp, dan nilai metrik
    jika didukung.
-   Batasi penggunaan huruf kapital penuh. Hindari teks terlalu kecil
    untuk label penting.

### Bentuk dan spasi

-   Gunakan sistem spasi konsisten berbasis kelipatan 4 px.
-   Radius kartu/panel 8--12 px; tombol dan input 6--8 px.
-   Border tipis dan kontras moderat; bayangan minimal.
-   Gunakan ikon konsisten dari library yang sudah dipakai proyek.
    Jangan mencampur banyak gaya ikon.
-   Animasi singkat hanya untuk transisi yang membantu memahami
    perubahan state; hormati `prefers-reduced-motion`.

## 4. Kerangka Aplikasi

### Dashboard Analis

Layout desktop: - Sidebar kiri dengan lebar konsisten dan kemampuan
collapse. - Header atas berisi nama ruang kerja, status sistem
seperlunya, dan menu akun. - Area konten dengan judul halaman, deskripsi
singkat, aksi utama, lalu konten. - Untuk halaman peta, gunakan layout
tiga area: filter/daftar di kiri, peta di tengah, detail kejadian di
kanan saat item dipilih. - Panel samping dapat di-collapse agar peta
tetap luas.

Navigasi utama: 1. Overview 2. Peta Kejadian 3. Timeline 4. Graph Relasi
5. Antrean Review 6. Kejadian 7. Artikel & Sumber 8. Operasional Crawler
9. Audit Log 10. Pengaturan

Tampilkan menu sesuai permission. Namun, **menyembunyikan menu bukan
kontrol keamanan**; endpoint dan aksi backend wajib dilindungi
authorization.

### Peta Publik

-   Area navigasi lebih sederhana daripada dashboard internal.
-   Fokus pada peta, filter dasar, legenda, ringkasan statistik publik,
    dan detail kejadian yang telah disetujui.
-   Jangan menampilkan catatan internal, skor AI internal, antrean
    review, informasi pribadi, atau kandidat relasi yang belum
    disetujui.
-   Berikan penjelasan singkat tentang cakupan data, tanggal pembaruan
    terakhir, sumber, dan keterbatasan data.
-   Sediakan keadaan kosong yang informatif jika tidak ada kejadian
    sesuai filter.

## 5. Halaman dan Komponen

### 5.1 Overview Dashboard

Tampilkan: - Ringkasan jumlah kejadian terpublikasi dan menunggu
review. - Artikel baru yang berhasil diproses. - Status crawl terbaru
dan kegagalan yang memerlukan tindakan. - Tren kejadian berdasarkan
periode yang dipilih. - Aktivitas review terbaru yang boleh dilihat oleh
pengguna tersebut. - Peringatan operasional yang relevan, bukan kumpulan
kartu metrik dekoratif.

Setiap angka harus memiliki definisi yang jelas, rentang waktu, dan
sumber perhitungan. Jangan tampilkan grafik dengan data demo seolah-olah
data nyata.

### 5.2 Peta Kejadian

Komponen: - MapLibre GL JS map canvas. - Search lokasi dan filter
kategori. - Filter rentang waktu, wilayah administratif, status
publikasi, dan sumber jika diizinkan. - Cluster marker saat zoom out. -
Legenda kategori yang ringkas. - Panel daftar hasil yang tersinkron
dengan peta. - Panel detail kejadian saat marker atau baris dipilih. -
Kontrol zoom, reset extent, dan geolokasi hanya jika diperlukan dan izin
browser diberikan.

Perilaku: - Saat pengguna memilih kejadian dari daftar, peta memusatkan
lokasi dan membuka detail. - Saat marker dipilih, daftar menandai item
yang sama. - Filter aktif terlihat sebagai chip dan dapat dihapus satu
per satu atau sekaligus. - Perubahan peta dan filter tidak boleh membuat
query tak terbatas; gunakan bounding box, pagination, atau clustering
server-side sesuai kebutuhan. - Untuk lokasi berpresisi rendah,
tampilkan area umum atau geometri administratif, bukan titik presisi
palsu. - Jangan mengarang koordinat. Jika lokasi hanya diketahui pada
tingkat kabupaten, visualisasikan sesuai tingkat kepastian itu.

### 5.3 Detail Kejadian

Susunan yang disarankan: 1. Judul netral yang tidak menyatakan pihak
tertentu bersalah. 2. Kategori, tanggal/waktu, wilayah, dan status
verifikasi. 3. Ringkasan yang memisahkan laporan sumber dari
interpretasi. 4. Bagian "Sumber dan bukti" berisi judul artikel,
penerbit, tanggal, tautan, dan kutipan pendukung. 5. Tingkat presisi
lokasi dan waktu. 6. Riwayat perubahan dan keputusan review sesuai
permission. 7. Relasi terkait dengan label status
kandidat/ditinjau/disetujui.

Jangan menyatukan informasi dari beberapa artikel tanpa mempertahankan
atribusi sumbernya. Perbedaan atau konflik antar-sumber harus
ditampilkan, bukan disembunyikan.

### 5.4 Timeline

-   Urutkan berdasarkan waktu kejadian jika tersedia; jika tidak, tandai
    bahwa pengurutan memakai waktu publikasi.
-   Bedakan waktu kejadian, waktu publikasi, waktu ditemukan crawler,
    dan waktu terakhir diperbarui.
-   Filter kategori, wilayah, dan periode.
-   Klik item untuk memilih kejadian di peta dan membuka detail.
-   Tandai tanggal atau waktu yang tidak pasti, misalnya "perkiraan"
    atau "rentang waktu".

### 5.5 Statistik dan Grafik

-   Gunakan grafik sederhana yang memiliki judul, satuan, periode, dan
    definisi metrik.
-   Berikan tooltip dan aksesibilitas yang memadai.
-   Hindari grafik 3D, efek dekoratif, atau penggunaan warna tanpa
    makna.
-   Jelaskan bahwa perubahan jumlah laporan dapat dipengaruhi oleh
    cakupan sumber dan keterlambatan publikasi.
-   Jangan menyimpulkan tren risiko populasi dari jumlah berita mentah
    tanpa konteks atau denominator yang tepat.

### 5.6 Graph Relasi

Gunakan Cytoscape.js. Node mewakili kejadian; edge mewakili **kandidat
hubungan analitis**.

Setiap edge yang dipilih harus menampilkan: - Tipe hubungan yang
diusulkan. - Skor kemiripan dengan label yang jelas bahwa skor bukan
probabilitas pelaku sama. - Faktor yang mendukung kandidat (misalnya
kemiripan waktu, wilayah, atau modus yang disebut sumber). - Bukti dan
kutipan sumber untuk setiap faktor jika tersedia. - Faktor yang
bertentangan atau informasi yang belum diketahui. - Status review dan
siapa yang menyetujui/menolak, sesuai permission.

Interaksi: - Klik node untuk membuka ringkasan kejadian. - Klik edge
untuk membuka panel bukti relasi. - Filter berdasarkan rentang waktu,
wilayah, kategori, dan status review. - Sediakan legenda dan kontrol
reset layout. - Jangan gunakan label seperti "pelaku yang sama" sebagai
fakta hanya karena model memberi skor tinggi. - Relasi yang belum
diverifikasi harus memiliki gaya visual yang berbeda dan label
"Kandidat".

### 5.7 Antrean Review

Buat tampilan kerja yang mengutamakan efisiensi: - Daftar
artikel/kejadian menunggu pemeriksaan. - Filter berdasarkan sumber,
kategori, waktu, status ekstraksi, dan tingkat ketidakpastian. - Panel
sumber asli dan panel hasil ekstraksi berdampingan pada layar lebar. -
Field hasil AI dapat dikoreksi oleh reviewer dengan jejak perubahan. -
Aksi yang jelas: Setujui, Tolak, Edit, Tunda, dan Tandai perlu informasi
tambahan. - Minta alasan untuk keputusan penolakan atau koreksi
penting. - Konfirmasi sebelum tindakan massal atau tindakan yang sulit
dibatalkan. - Tampilkan sumber dan kutipan bukti di dekat klaim yang
sedang ditinjau.

### 5.8 Artikel & Sumber

-   Daftar sumber dengan nama, domain, metode pengambilan, status aktif,
    jadwal, dan hasil crawl terakhir.
-   Halaman detail sumber menampilkan riwayat crawl dan error.
-   Validasi domain dan URL pada backend.
-   Hindari menampilkan token, secret, atau kredensial pada UI maupun
    log.
-   Tampilkan status jadwal sebagai informasi operasional; jangan
    mengklaim data real-time jika sumber atau crawler terlambat.

### 5.9 Operasional Crawler

-   Daftar run crawl dengan waktu mulai/selesai, sumber, status, jumlah
    artikel ditemukan, jumlah baru, duplikat, dan error.
-   Detail error yang aman untuk operator.
-   Tombol "Jalankan crawl sekarang" hanya untuk role yang berwenang.
-   Status queue/worker dan antrean gagal bila tersedia.
-   Jangan membuat tombol kontrol yang tampak aktif jika backend belum
    mengimplementasikan aksinya.

### 5.10 Pengaturan dan Audit

-   Kelola user, role, permission, kategori, dan kebijakan publikasi.
-   Audit log memuat aktor, aksi, objek, waktu, dan perubahan yang
    relevan.
-   Jangan mencatat password, token, secret, atau data pribadi sensitif
    secara berlebihan.
-   Perubahan permission dan kebijakan publikasi harus membutuhkan
    authorization yang tepat.

## 6. Status, Label, dan Bahasa

Gunakan label yang konsisten: - `Draft` --- belum diajukan untuk
review. - `Menunggu review` --- menunggu pemeriksaan manusia. -
`Disetujui` --- telah lolos review sesuai kebijakan. - `Ditolak` ---
tidak memenuhi kriteria. - `Diarsipkan` --- tidak aktif untuk alur kerja
biasa. - `Kandidat relasi` --- usulan hubungan yang belum
dikonfirmasi. - `Lokasi perkiraan` --- presisi lokasi terbatas. -
`Waktu tidak pasti` --- waktu tidak dapat ditentukan secara tepat.

Gunakan Bahasa Indonesia yang jelas, netral, dan tidak sensasional.
Hindari istilah yang menyiratkan seseorang bersalah jika statusnya belum
ditetapkan oleh proses yang sah. Beri atribusi seperti "menurut laporan
\[nama sumber\]" ketika menyajikan klaim yang berasal dari berita.

## 7. Pola Interaksi dan Keadaan UI

Setiap halaman data harus menangani: - Loading/skeleton. - Empty state
dengan penjelasan dan langkah berikutnya. - Error state dengan pesan
yang berguna dan aksi coba lagi bila aman. - Success feedback setelah
aksi selesai. - Validation error dekat field terkait. - Pagination atau
infinite loading yang terkontrol. - Filter aktif dan opsi reset. -
Konfirmasi untuk tindakan destruktif. - Keyboard focus yang terlihat dan
label untuk input.

Jangan menyamarkan error API sebagai daftar kosong. Jangan menampilkan
toast sukses sebelum server mengonfirmasi keberhasilan. Hindari polling
terlalu agresif; interval pembaruan UI harus berbeda dari jadwal crawler
dan dapat disesuaikan.

## 8. Responsif dan Aksesibilitas

-   Desktop adalah prioritas untuk dashboard analis dan graph.
-   Pada tablet, panel detail dapat menjadi drawer.
-   Pada ponsel, filter dan detail dibuka sebagai drawer atau halaman
    terpisah; jangan memaksakan tiga kolom.
-   Pastikan kontras teks memadai, fokus keyboard terlihat, tombol
    memiliki nama yang jelas, dan semua status tidak hanya dibedakan
    berdasarkan warna.
-   Sediakan alternatif daftar untuk informasi yang juga ditampilkan di
    peta atau graph.
-   Hormati `prefers-reduced-motion`.
-   Jangan gunakan hover sebagai satu-satunya cara mengakses informasi.

## 9. Panduan Teknis UI

-   Ikuti struktur, komponen, dan konvensi yang sudah disediakan Laravel
    Starter Kit; jangan mengganti starter kit tanpa alasan yang
    disetujui.
-   Gunakan Vue 3 + TypeScript dan komponen reusable.
-   Gunakan library peta dan grafik yang dipilih proyek: MapLibre GL JS,
    Cytoscape.js, dan ECharts jika sudah disepakati.
-   Gunakan satu sistem token warna, tipografi, radius, spasi, dan
    status.
-   Pisahkan komponen presentasi dari pemanggilan API dan logika bisnis.
-   Gunakan type/interface untuk response API; hindari `any` kecuali
    benar-benar diperlukan dan diberi alasan.
-   Semua tombol harus terhubung ke aksi nyata atau dinonaktifkan dengan
    alasan yang jelas.
-   Jangan menaruh data sensitif dalam bundle frontend atau mengandalkan
    penyembunyian elemen sebagai proteksi.
-   Jangan hardcode angka statistik produksi. Data contoh harus diberi
    penanda "Demo".
-   Hindari menambahkan dependency besar jika fungsi yang sama sudah
    tersedia dalam proyek.
-   Perhatikan performa: lazy-load modul berat, batasi jumlah node graph
    yang dirender sekaligus, dan gunakan clustering/aggregation untuk
    data peta.

## 10. Pemisahan Publik dan Internal

Peta publik dan dashboard internal boleh berbagi design tokens serta
komponen dasar, tetapi **wajib menggunakan kebijakan data dan API yang
berbeda**.

Peta publik hanya boleh menerima: - Kejadian yang sudah disetujui untuk
publikasi. - Ringkasan dan kategori yang aman untuk publik. - Lokasi
dengan presisi yang diizinkan. - Waktu yang telah dinormalisasi dengan
penanda ketidakpastian. - Referensi sumber publik. - Relasi yang memang
telah disetujui untuk ditampilkan, jika kebijakan mengizinkan.

Jangan kirim data internal lalu menyembunyikannya dengan CSS atau
conditional rendering. Penyaringan harus dilakukan pada backend melalui
endpoint/DTO publik khusus.

## 11. Kriteria Penerimaan Desain

Sebuah halaman dianggap selesai jika: - Visualnya konsisten dengan token
dan pola dalam dokumen ini. - Layout tetap jelas pada ukuran layar yang
relevan. - Loading, empty, error, dan success state telah dibuat. -
Kontrol memiliki perilaku nyata dan feedback yang tepat. - Status dan
ketidakpastian ditulis secara eksplisit. - Data yang ditampilkan sesuai
permission pengguna. - Tidak ada informasi korban atau catatan internal
yang bocor ke area publik. - Tidak ada klaim AI yang tampil sebagai
fakta tanpa atribusi atau bukti. - Interaksi utama dapat diuji dan tidak
hanya berupa mockup. - Test terkait lulus atau kekurangannya dilaporkan
dengan jelas.

## 12. Instruksi Singkat untuk Agent

Sebelum mengerjakan UI: 1. Baca `design.md` ini dan periksa struktur
proyek yang sudah ada. 2. Identifikasi halaman, komponen, endpoint, dan
permission yang terlibat. 3. Implementasikan satu modul dalam satu
waktu. 4. Gunakan data nyata dari API bila tersedia; tandai semua data
demo dengan jelas. 5. Jangan mengubah tema, stack, navigasi inti, atau
pola desain secara sepihak. 6. Setelah selesai, laporkan file yang
diubah, state UI yang didukung, hasil test, dan hal yang belum terhubung
ke backend.

**Prioritas implementasi visual:** fondasi dan layout → peta kejadian →
detail kejadian dan timeline → review queue → crawler/source management
→ graph relasi → pengaturan dan audit → pemolesan responsif dan
aksesibilitas.
