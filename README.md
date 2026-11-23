# Warehouse Finished Goods Monitoring System (WMS) — PT Indofood

Sistem Informasi Pemantauan Penyimpanan & Mutasi Barang Jadi (*Finished Goods*) berbasis web untuk mendukung operasional pergudangan **PT Indofood**. Aplikasi ini dirancang untuk mengontrol kapasitas rak secara *real-time*, menjaga integritas penempatan SKU per level rak, serta menyediakan audit log mutasi dan laporan stok siap cetak.

---

## Tech Stack

- **Backend:** PHP 8.x, Laravel Framework
- **Database:** PostgreSQL (dengan *Pessimistic Locking* untuk integritas transaksi)
- **Frontend:** Blade Templating, Bootstrap 5, Bootstrap Icons, Custom CSS Print Layout
- **Zona Waktu & Lokalisasi:** `Asia/Jakarta` (WIB)

---

## Fitur Utama & Aturan Bisnis

1. **Hierarki Master Lokasi Penyimpanan**
   - Pengelolaan struktur bertingkat: **Unit Gudang (`WH`) → Area Penyimpanan → Unit Rak (`RAK`) → Level Rak (`Lv`)**.
   - Pembuatan slot level rak otomatis sesuai jumlah tingkatan dan kapasitas maksimal pallet per level.
2. **Transaksi Mutasi Pallet (`IN`, `OUT`, `MOVE`)**
   - **IN (Barang Masuk):** Penempatan produk jadi dari lini produksi ke level rak tujuan.
   - **MOVE (Pindah Lokasi):** Relokasi stok antar level rak dalam satu gudang.
   - **OUT (Barang Keluar):** Pengeluaran stok untuk distribusi/pengiriman (*Delivery Order*).
   - Konversi otomatis jumlah **Pallet** ke satuan **Karton** berdasarkan standar konversi masing-masing SKU.
3. **Validasi Ketat & Proteksi Integritas Data**
   - **Anti Over-Capacity:** Sistem menolak transaksi masuk/pindah apabila total pallet melebihi batas kapasitas level rak.
   - **Single-SKU per Level (Anti Mix-SKU):** Satu level rak hanya boleh menampung satu jenis SKU produk yang sama pada satu waktu.
   - **Concurrency Control:** Menggunakan `DB::transaction()` dan `lockForUpdate()` untuk mencegah selisih stok saat transaksi bersamaan.
4. **Monitoring Kapasitas & Laporan Siap Cetak**
   - Visualisasi okupansi rak dan detail lokasi produk aktif.
   - Laporan **Stok & Lokasi Produk** serta **Riwayat Transaksi** lengkap dengan filter gudang, tipe mutasi, rentang tanggal, pencarian SKU, dan format cetak resmi (`Print-Ready`).

---

## Matriks Hak Akses Pengguna (RBAC)

| Role | Cakupan Gudang | Master Data | Transaksi (`IN/OUT/MOVE`) | Monitoring & Laporan | Cetak Laporan |
| :--- | :--- | :---: | :---: | :---: | :---: |
| **Super Admin** | Global (Semua Gudang) | Penuh | Penuh | Semua Gudang | Ya |
| **Admin Gudang** | Terkunci 1 Gudang | Sesuai Gudang | Penuh | Gudang Terkait | Ya |
| **Petugas FG** | Terkunci 1 Gudang | Tidak Ada | Penuh | Gudang Terkait | Ya |
| **Supervisor** | Terkunci 1 Gudang | Tidak Ada | *Read-Only* (Pantau) | Gudang Terkait | Ya |

---

## Struktur Entitas Database

- `warehouses` — Master unit gudang (*Finished Goods Utama, Ekspor, dll.*)
- `areas` — Pembagian zona penyimpanan di dalam gudang
- `racks` & `rack_levels` — Unit rak beserta konfigurasi level dan kapasitas pallet
- `categories` & `products` — Master kategori dan SKU produk beserta rasio karton/pallet
- `storage_placements` — Saldo stok aktual yang menempati setiap level rak
- `storage_transactions` — Riwayat lengkap seluruh aktivitas mutasi (`IN`, `OUT`, `MOVE`)
- `users` — Akun pengguna beserta peran (`role`) dan penugasan gudang (`warehouse_id`)

---

## Panduan Instalasi Lokal

1. **Clone repositori**
   ```bash
   git clone [https://github.com/Marwann-styd/LARAVEL-KULIYEHH.git](https://github.com/Marwann-styd/LARAVEL-KULIYEHH.git)
   cd LARAVEL-KULIYEHH
   ```

2. **Install dependensi PHP**
   ```bash
   composer install
   ```

3. **Konfigurasi environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan koneksi database PostgreSQL dan zona waktu di file `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=warehouse_fg
   DB_USERNAME=postgres
   DB_PASSWORD=your_password

   APP_TIMEZONE=Asia/Jakarta
   ```

4. **Migrasi database & jalankan aplikasi**
   ```bash
   php artisan migrate --seed
   php artisan serve
   ```
   Akses aplikasi melalui browser di `http://127.0.0.1:8000`.

---

## Pengembang

- **Marwan Setyadi** — Mahasiswa Teknik Informatika (Proyek Kerja Praktik)
- **Repositori:** [github.com/Marwann-styd/LARAVEL-KULIYEHH](https://github.com/Marwann-styd/LARAVEL-KULIYEHH)
