<div align="center">

# 📦 Warehouse FG — Finished Goods Monitoring System
### Sistem Manajemen Penyimpanan & Monitoring Kapasitas Gudang PT Indofood

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Database-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

*Aplikasi web terintegrasi untuk memantau okupansi rak penyimpanan, melacak distribusi stok SKU Finished Goods, serta mencatat transaksi mutasi pallet secara real-time.*

</div>

---

## 🎯 Latar Belakang & Tujuan

Pengelolaan gudang barang jadi (*Finished Goods*) membutuhkan visibilitas yang akurat terhadap sisa kapasitas rak, posisi setiap varian produk (SKU), serta riwayat pergerakan barang agar proses distribusi berjalan optimal tanpa *over-capacity* maupun salah penempatan lokasi.

Sistem ini dirancang khusus untuk mendukung operasional gudang **PT Indofood** dengan fitur pelacakan berbasis **Gudang $\rightarrow$ Area $\rightarrow$ Rak $\rightarrow$ Level Rak**, konversi otomatis **Pallet ke Karton**, serta kontrol akses berbasis peran (*Role-Based Access Control*).

---

## ✨ Fitur Utama Sistem

### 1. 📊 Dashboard & Warehouse Command Center
* Ringkasan KPI infrastruktur gudang (Total Gudang, Area, Rak, dan SKU Aktif).
* Indikator visual tingkat okupansi penyimpanan dalam satuan **Pallet** dan estimasi **Karton**.
* **Warehouse Command Center** per unit gudang dengan akses cepat yang menyesuaikan *role* pengguna.

### 2. 🗂️ Pengelolaan Master Data (Super Admin)
* **Data Gudang:** Manajemen unit gudang aktif beserta status operasionalnya.
* **Data Area:** Pembagian zona penyimpanan pada masing-masing unit gudang.
* **Data Rak & Level:** Konfigurasi unit rak (3 atau 4 tingkat level) dengan kapasitas standar **3 pallet per level** serta tampilan matriks slot rak.
* **Kategori & Master Produk (SKU):** Pengelolaan katalog produk jadi lengkap dengan rasio **Standar Isi Karton per Pallet** untuk konversi stok otomatis.
* **Manajemen Pengguna:** Pengaturan akun personel, aktivasi/nonaktivasi akun, dan penetapan unit gudang tugas.

### 3. 🔄 Operasional & Transaksi Penyimpanan
* **Monitoring Kapasitas:** Pemantauan sisa slot pallet secara hierarkis dari level gudang hingga detail tingkatan rak.
* **Lokasi Produk:** Pelacakan posisi penyimpanan setiap SKU lengkap dengan informasi jumlah pallet dan total karton.
* **Transaksi Penyimpanan (`IN`, `OUT`, `MOVE`):**
  * `IN` (Barang Masuk): Penempatan pallet baru ke level rak kosong atau level dengan SKU yang sama.
  * `OUT` (Barang Keluar): Pengurangan stok pallet dari level rak asal untuk kebutuhan distribusi.
  * `MOVE` (Pindah Lokasi): Relokasi stok antar level rak dengan penguncian transaksi (*pessimistic locking*) untuk menjaga integritas stok.

### 4. 📑 Pusat Laporan & Audit
* **Laporan Stok Produk per Lokasi:** Rekapitulasi posisi stok aktif yang dapat difilter berdasarkan gudang, kategori, maupun kode SKU/rak.
* **Laporan Riwayat Transaksi:** Log audit keluar-masuk dan perpindahan barang berdasarkan rentang tanggal, jenis transaksi, dan unit gudang.

---

## 🔐 Matriks Hak Akses Pengguna (RBAC)

| Role | Cakupan Gudang | Master Data & User | Monitoring & Lokasi | Input Transaksi (`IN/OUT/MOVE`) | Laporan & Riwayat |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Super Admin** | Semua Gudang Aktif | ✅ Penuh | ✅ Lihat | ✅ Penuh | ✅ Semua Gudang |
| **Admin Gudang** | Gudang Penugasan | ❌ | ✅ Lihat | ✅ Penuh | ✅ Gudang Tugas |
| **Supervisor** | Gudang Penugasan | ❌ | ✅ Lihat (*Read-Only*) | ❌ (*Read-Only*) | ✅ Gudang Tugas |
| **Petugas FG** | Gudang Penugasan | ❌ | ✅ Lihat | ✅ Penuh | ✅ Gudang Tugas |

---

## 🛠️ Teknologi yang Digunakan

* **Backend Framework:** Laravel 13 (PHP 8.5)
* **Database:** PostgreSQL
* **Frontend & UI:** Blade Templating Engine, Bootstrap 5.3, Bootstrap Icons, Custom Enterprise CSS (`indofood-wms.css`)
* **Keamanan & Concurrency:** Laravel Auth Middleware, Role-Based Access Control (RBAC), Database Transaction & Row-Level Locking (`lockForUpdate`)

---

## 🚀 Panduan Instalasi Lokal

1. **Clone repository ini**
   ```bash
   https://github.com/Marwann-styd/LARAVEL-KULIYEHH.git
 
