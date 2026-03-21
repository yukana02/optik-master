# Changelog — Optik Master

## v2.0.0 (2026-03-21) — Major Update

### Bug Fixes
- **FIXED** Duplikat migration `add_soft_deletes_to_categories_table` → dihapus
- **FIXED** Migration `potongan_bpjs` tidak konsisten dengan model → dihapus, pakai `subsidi_bpjs`
- **FIXED** View `transactions/index` referensi `potongan_bpjs` → diubah ke `subsidi_bpjs`
- **FIXED** `User` model tidak memiliki `isDokter()` → ditambahkan
- **FIXED** Dashboard N+1 query (7 query loop) → dioptimasi menjadi 1 query `groupBy`
- **FIXED** Route permission terlalu longgar (semua aksi dibungkus `view`) → granular per method

### New Features
- **BARU** Form Request classes untuk semua form utama (Patient, Product, Transaction, MedicalRecord)
- **BARU** Cetak Invoice / Struk transaksi (browser print / PDF siap cetak)
- **BARU** Cetak Kartu Pasien (format kartu muat di dompet, ada resep terakhir)
- **BARU** Modul Supplier — CRUD lengkap + kode auto-generate
- **BARU** Modul Purchase Order — buat PO, terima (auto update stok + mutasi masuk), batalkan
- **BARU** Activity Log — audit trail semua aksi user (modul, aksi, IP, timestamp)
- **BARU** Tombol Cetak laporan (Penjualan, Stok, Mutasi Stok) — halaman print-ready
- **BARU** Route print laporan: `reports.penjualan.print`, `reports.stok.print`, `reports.mutasi-stok.print`
- **BARU** Sidebar: menu Pengadaan (Supplier, Purchase Order) + Activity Log

### Improvements
- Permission baru: `supplier.*`, `purchase_order.*` (sudah di-seed ke setiap role)
- README.md lengkap dengan panduan instalasi, akun default, tabel akses, dan struktur DB
