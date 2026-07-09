# 🍳 Mini Cookpad — Sistem Koin & Dual Chef Verification

Platform berbagi resep masakan berbasis web, dengan sistem koin buat unlock resep premium dan verifikasi chef.

## Teknologi
- **PHP**: >= 8.1
- **Framework**: CodeIgniter 4 (v4.5.8)
- **Database**: MySQL
- **Server**: XAMPP / built-in server (`php spark serve`)

## Fitur
- Manajemen resep (tambah, edit, hapus) oleh chef
- Resep gratis & resep premium (unlock pakai koin)
- Sistem top-up koin (5 paket, Rp 5.000 – Rp 150.000)
- Dual chef verification: Basic (upload KTP) & Advanced (upload sertifikat)
- Bagi hasil koin ke chef tiap resep premium di-unlock (50% / 70%)
- Bookmark resep
- Dashboard admin buat approve verifikasi chef

## Cara Install
1. `composer install`
2. **Penting:** pastikan folder `app` sejajar dengan `public`, `vendor`, `writable` (jangan sampai nyasar ke dalam folder lain)
3. Buat database `mini_cookpad`
4. Sesuaikan `.env` dengan konfigurasi database kamu
5. `php spark migrate`
6. `php spark db:seed InitialSeeder`
7. `php spark serve`
8. Akses: `http://localhost:8080`

## Akun Demo
| Role              | Email                     | Password |
|-------------------|---------------------------|----------|
| Admin             | admin@cookpad.com         | admin123 |
| Chef Verified     | chef.rina@cookpad.com     | chef123  |
| Chef Verified     | chef.takeshi@cookpad.com  | chef123  |
| Chef (belum verif)| chef.marco@cookpad.com    | chef123  |
| User Free         | user.andi@cookpad.com     | user123  |
| User Free         | user.sari@cookpad.com     | user123  |

## Struktur Database
10 tabel: `users`, `chef_verifications`, `recipes`, `ingredients`, `steps`, `bookmarks`, `coin_packages`, `coin_topups`, `coin_transactions`, `recipe_unlocks`.

## Alur Singkat
- **Verifikasi chef**: user daftar → upload KTP (jadi Chef, 50% bagi hasil) atau upload sertifikat (jadi Chef Verified, 70% bagi hasil) → admin approve.
- **Unggah resep**: chef bikin resep, tentukan gratis atau premium (isi harga koin).
- **Beli resep premium**: user top-up koin dulu → koin dipakai buat unlock resep → koin otomatis kebagi ke chef sesuai persentase.
- **Top-up koin**: user pilih paket → bayar → saldo koin bertambah sesuai paket + bonus.
