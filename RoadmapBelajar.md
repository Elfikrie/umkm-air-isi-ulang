Oke, siap! Mulai sekarang saya **Grow Fikri**, mentor kamu untuk project ini. Seru nih, project UMKM air isi ulang — konkret dan cocok banget buat belajar Laravel + Filament end-to-end.

Sebelum ngoding, saya mau kita sepakat dulu soal **peta belajar (roadmap)**-nya biar kamu tahu kita ini lagi di tahap mana dan mau ke mana. Nanti kita jalani satu-satu, santai, sambil diskusi tiap tahap.

## 🗺️ Roadmap Belajar Kita

**Fase 0 — Persiapan Lingkungan**
Cek PHP, Composer, Node, MySQL sudah terpasang & versinya cocok.

**Fase 1 — Instalasi Laravel**
`laravel new`, kenalan struktur folder, `.env`, koneksi ke MySQL.

**Fase 2 — Desain Database (ERD)**
Ini bagian yang kamu tanyakan soal "pakai AI gimana" — nanti saya ajarkan cara *prompting* yang benar supaya AI bisa bantu kamu bikin skema tabel yang rapi (bukan asal-asalan).

**Fase 3 — Autentikasi & Role**
Install Laravel Breeze (atau Fortify), lalu tambah sistem role (Admin, Kasir, Pelanggan) pakai package `spatie/laravel-permission`.

**Fase 4 — Install Filament**
Setup panel admin, kenalan dengan konsep Resource, Form, Table di Filament.

**Fase 5 — CRUD User (Kasir & Pelanggan) oleh Admin**
Filament Resource untuk manage akun.

**Fase 6 — Registrasi Pelanggan**
Ini di luar panel Filament (karena Filament defaultnya panel admin) — jadi kita bikin halaman registrasi custom.

**Fase 7 — Modul Pesanan**
Desain relasi tabel pesanan, Filament Resource untuk Admin/Kasir (lihat, terima, batalkan), dan halaman pemesanan untuk Pelanggan.

**Fase 8 — Laporan Keuangan**
Filament Resource CRUD laporan keuangan untuk Admin.

**Fase 9 — Halaman Pelanggan (non-Filament)**
Blade/Livewire sederhana untuk pelanggan pesan & lihat status pesanan.

**Fase 10 — Testing & Polish**
Cek alur end-to-end, rapikan validasi, siap-siap deploy kalau kamu mau.

---

Nah, karena ini pondasi banget, saya mau mulai dari **Fase 0** dulu. Sebelum instal Laravel, saya perlu tahu kondisi laptop/komputer kamu sekarang.