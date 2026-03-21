# Optik Master — Sistem Manajemen Toko Optik

Laravel 12 · PHP 8.2+ · Bootstrap 5 · MySQL

---

## Fitur Lengkap

| Modul | Fitur |
|---|---|
| **Auth** | Login, Logout, Concurrent session detection, Auto-logout (inaktif 30 menit) |
| **RBAC** | 4 role: `super_admin`, `admin`, `dokter`, `kasir` · 23 permission granular |
| **Pasien** | CRUD + SoftDelete · No. RM auto-generate · BPJS · Cetak Kartu Pasien |
| **Rekam Medis** | Input resep OD/OS lengkap (SPH, CYL, AXIS, ADD, PD, VIS) · Linked ke transaksi |
| **Produk** | CRUD + SoftDelete · Upload gambar · Kode auto-generate · Alert stok menipis |
| **Kategori** | CRUD + SoftDelete · Status aktif/nonaktif |
| **POS / Transaksi** | Keranjang dinamis · Diskon item + transaksi · Subsidi BPJS · Multi metode bayar · Cetak Invoice |
| **Supplier** | CRUD · Kode auto-generate · Riwayat PO |
| **Purchase Order** | Buat PO · Terima (update stok otomatis + catat mutasi masuk) · Batalkan |
| **Mutasi Stok** | Otomatis tercatat saat transaksi, pembatalan, dan penerimaan PO |
| **Import/Export** | Import produk & pasien dari Excel/CSV · Export ke Excel |
| **Laporan** | Penjualan · Produk Terlaris · Stok · Mutasi Stok · Export CSV · Cetak PDF |
| **Activity Log** | Rekam semua aktivitas user (modul, aksi, IP, timestamp) |
| **Dashboard** | Stat cards · Grafik omzet 7 hari (1 query) · Stok menipis · Top produk |

---

## Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- MySQL / MariaDB
- Node.js 18+

### Langkah Setup

```bash
# 1. Clone / ekstrak project
cd optik-master

# 2. Install dependencies
composer install

# 3. Copy .env dan generate key
cp .env.example .env
php artisan key:generate

# 4. Sesuaikan .env — isi DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Buat database MySQL
mysql -u root -p -e "CREATE DATABASE optik_store;"

# 6. Migrasi + Seeder
php artisan migrate --seed

# 7. Install & build frontend
npm install
npm run build

# 8. Storage link
php artisan storage:link

# 9. Jalankan server
php artisan serve
```

Buka: http://localhost:8000

---

## Akun Default

| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@optik.com | password |
| Admin | admin@optik.com | password |
| Dokter | dokter@optik.com | password |
| Kasir | kasir@optik.com | password |

---

## Hak Akses per Role

| Permission | super_admin | admin | dokter | kasir |
|---|:---:|:---:|:---:|:---:|
| Pasien CRUD | ✅ | ✅ | ✅ | Lihat + Tambah |
| Rekam Medis CRUD | ✅ | ✅ | ✅ | Lihat |
| Produk CRUD | ✅ | ✅ | ❌ | Lihat |
| Transaksi | ✅ | ✅ | ❌ | Lihat + Buat |
| Supplier & PO | ✅ | ✅ | ❌ | ❌ |
| Laporan | ✅ | ✅ | ❌ | ❌ |
| Manajemen User | ✅ | ❌ | ❌ | ❌ |
| Activity Log | ✅ | ❌ | ❌ | ❌ |

---

## Struktur Database

```
users               — akun pengguna + session_token
patients            — data pasien + no_bpjs
medical_records     — resep mata OD/OS lengkap
categories          — kategori produk
products            — inventaris produk
transactions        — header transaksi (BPJS, diskon, metode bayar)
transaction_items   — detail item transaksi
stock_movements     — mutasi stok (masuk/keluar/retur/adjustment)
suppliers           — data supplier
purchase_orders     — pembelian ke supplier
purchase_order_items— item PO
activity_logs       — audit trail aktivitas user
permissions/roles   — Spatie Laravel Permission
```

---

## Catatan Teknis

- **Form Request** dipakai di semua form utama (Patient, Product, Transaction, MedicalRecord)
- **DB::transaction()** + `lockForUpdate()` di setiap operasi stok untuk mencegah race condition
- **N+1 query** sudah dioptimasi — Dashboard grafik omzet 7 hari = 1 query (bukan 7)
- **ActivityLog::catat()** static helper tersedia untuk dipanggil dari controller manapun
- **SoftDeletes** di: patients, medical_records, products, categories, transactions, suppliers, purchase_orders
- Semua route permission granular per method (view/create/edit/delete terpisah)
