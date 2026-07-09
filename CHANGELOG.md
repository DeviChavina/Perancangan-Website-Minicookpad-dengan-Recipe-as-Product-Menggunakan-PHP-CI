# Mini Cookpad CI4 — Changelog Perbaikan

> ⚠️ Catatan penting: saya tidak punya akses PHP/internet di lingkungan ini, jadi semua
> perbaikan di bawah dibuat lewat **code review manual**, bukan hasil testing langsung.
> Folder `vendor/` saya hapus dari zip ini (terlalu besar & bisa di-generate ulang).
> Jalankan `composer install` setelah extract, lalu `php spark serve` untuk menjalankan.

## 🔴 Bug Kritis (memblokir fitur sepenuhnya)

1. **Admin & Chef area tidak bisa diakses sama sekali.**
   `AdminFilter` & `ChefFilter` mengecek `session('role')`, padahal seluruh aplikasi
   menyimpan role di `session('user_role')`. Akibatnya halaman `/admin/*` dan
   `/chef/dashboard` selalu redirect "akses ditolak" — bahkan untuk admin/chef asli.
   → `app/Filters/AdminFilter.php`, `app/Filters/ChefFilter.php`

2. **Tombol "Simulasikan Pembayaran" selalu 404.**
   Memakai `<a href>` (GET) ke route yang didaftarkan `post()`. Diubah jadi form POST.
   → `app/Views/payment/index.php`

3. **Tombol bookmark di halaman detail resep tidak berfungsi.**
   Form mengirim **slug** padahal controller/route mengharapkan **id** numerik resep.
   → `app/Views/recipe/detail.php`

4. **Link "Lihat Resep" 404** di dashboard chef & halaman bookmark.
   Memakai `/recipes/{slug}` padahal route yang ada adalah `/recipe/{slug}` (singular).
   → `app/Views/chef/dashboard.php`, `app/Views/bookmark/index.php`

5. **Folder upload tidak ada.** `public/uploads/recipes`, `verifications`, `steps`,
   `avatars` belum dibuat — upload foto resep/verifikasi chef akan gagal/error.
   → dibuat foldernya + `.gitkeep`

6. **Foto langkah resep (step image) tidak pernah benar-benar di-upload** — kode
   menyiapkan nama variabel file tapi tidak pernah memanggil `getFile()`.
   → `app/Controllers/Chef.php`

7. **Tidak ada proteksi CSRF** sama sekali di seluruh form POST (login, register,
   verifikasi chef, buat resep, approve/reject verifikasi, dll). Sekarang CSRF filter
   diaktifkan secara global + `csrf_field()` ditambahkan ke setiap form POST.
   → `app/Config/Filters.php` + semua view dengan `<form method="post">`

8. **Halaman Admin → Manajemen User menampilkan jumlah resep & bookmark = 0** untuk
   semua user, karena controller tidak pernah menghitungnya.
   → `app/Controllers/Admin.php`

## 🟠 Bug Logika / Data

9. Dashboard chef hanya menampilkan resep berstatus `published`, padahal seharusnya
   chef bisa melihat resep draft miliknya sendiri juga. Sekaligus menambahkan
   `bookmark_count` yang sebelumnya hilang dari statistik dashboard.
   → `app/Models/RecipeModel.php` (`getRecipesByChef`)

10. Badge role di header (`mc-badge-userfree`, dst.) tidak pernah cocok dengan class
    CSS yang ada (`mc-badge-free`, `mc-badge-chef`, dll) — kecuali untuk ADMIN.
    Ditambahkan helper `role_badge_class()` yang benar.
    → `app/Helpers/cookpad_helper.php`, `app/Views/layout/header.php`

11. Menu nav menampilkan link "Dashboard" untuk role `CHEF_PENDING`, padahal
    `ChefFilter` akan menolak mereka (butuh `CHEF_VERIFIED`). Sekarang
    `CHEF_PENDING` diarahkan ke halaman status verifikasi.
    → `app/Views/layout/header.php`

## 🟢 Fitur yang Dilengkapi

12. **Edit & hapus resep untuk chef** — sebelumnya chef hanya bisa membuat resep, tidak
    bisa mengubah atau menghapusnya sama sekali.
    - Route baru: `GET/POST /chef/recipe/{id}/edit`, `/update`, `/delete`
    - Controller: `editRecipe()`, `updateRecipe()`, `deleteRecipe()` di `Chef.php`
      (dengan validasi kepemilikan resep, penggantian gambar, replace ingredients/steps)
    - View baru: `app/Views/chef/edit_recipe.php`
    - Tombol Edit/Hapus ditambahkan di `app/Views/chef/dashboard.php`

13. **Admin bisa mengubah role user langsung** dari halaman Manajemen User (sebelumnya
    admin hanya bisa approve/reject verifikasi chef, tidak ada kontrol role manual).
    - Route baru: `POST /admin/users/{id}/role`
    - Controller: `Admin::changeUserRole()` (dengan pengaman: admin tidak bisa
      menurunkan role akunnya sendiri secara tidak sengaja)
    - UI dropdown role + tombol simpan di setiap baris tabel user

14. **Auto-expire subscription premium.** Sebelumnya tidak ada mekanisme apa pun untuk
    menurunkan user dari `USER_PREMIUM` kembali ke `USER_FREE` setelah `end_date`
    subscription lewat — premium jadi berlaku selamanya. Sekarang dicek otomatis di
    `AuthFilter` setiap request halaman yang butuh login: subscription ditandai
    `expired` dan role user diturunkan otomatis.
    → `app/Filters/AuthFilter.php`

## 🆕 Update Round 3

15. **CSRF crash: "The action you requested is not allowed."**
    Disebabkan oleh `regenerate = true` di `app/Config/Security.php`. Saat Debug
    Toolbar aktif, browser membuat request AJAX di background untuk mengambil data
    toolbar — request ini ikut lewat CSRF filter dan, karena `regenerate=true`,
    **langsung meregenerasi cookie token sebelum user sempat submit form**. Token
    yang sudah tercetak di HTML jadi tidak valid lagi → semua form (login, register,
    dst) gagal dengan SecurityException. Ini bug klasik kombinasi
    CodeIgniter4 + Debug Toolbar + CSRF cookie regenerate.
    **Fix:** `regenerate` diubah ke `false` (standar untuk kebanyakan aplikasi —
    token tetap valid sepanjang sesi, tidak rapuh terhadap request background/tab
    ganda/tombol back).
    → `app/Config/Security.php`

16. **Foto resep yang diupload tidak pernah ditampilkan di mana pun!** Backend sudah
    bisa terima upload (`Chef::storeRecipe`, `updateRecipe`), tapi semua tampilan
    (kartu resep di Beranda, halaman Semua Resep, halaman detail, dashboard chef,
    daftar bookmark) selalu menampilkan placeholder emoji + gradient, tidak pernah
    memeriksa kolom `image`. Sekarang foto asli ditampilkan jika ada, dengan fallback
    ke emoji/gradient jika resep belum punya foto:
    - `app/Views/home/index.php` — kartu resep di beranda
    - `app/Views/recipe/index.php` — kartu di daftar semua resep
    - `app/Views/recipe/detail.php` — foto hero besar di halaman detail (baru,
      sebelumnya tidak ada gambar sama sekali di halaman ini)
    - `app/Views/chef/dashboard.php` — thumbnail di tiap baris resep milik chef
    - `app/Views/bookmark/index.php` — thumbnail di daftar bookmark

> Jika setelah update ini foto masih tidak muncul: pastikan folder
> `public/uploads/recipes/` ada & writable, dan cek nama file yang tersimpan di kolom
> `recipes.image` di database benar-benar ada di folder tersebut.

## ⚠️ Belum Dikerjakan (rekomendasi langkah selanjutnya)

- Avatar upload untuk user (kolom `avatar` ada di skema tapi belum ada form upload-nya)
- Halaman riwayat pembayaran user (`PaymentModel::getByUser()` sudah ada tapi belum
  dipakai di view manapun)
- Rate limiting / lockout untuk percobaan login yang gagal
- Pagination untuk daftar resep & daftar user admin (saat ini `findAll()` tanpa limit)
- Unit/feature test (belum ada test sama sekali di project ini)
- Testing end-to-end nyata di lingkungan dengan PHP — semua perbaikan di atas berbasis
  code review, **wajib di-test ulang sebelum dipakai produksi**
