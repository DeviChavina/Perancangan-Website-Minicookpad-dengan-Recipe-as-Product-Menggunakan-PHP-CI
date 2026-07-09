# Mini Cookpad v2 — Sistem Koin & Dual Chef Verification

## Cara Menjalankan

```bash
cd cookpad-v2
composer install

# Edit .env → sesuaikan password MySQL
# Buat database: CREATE DATABASE mini_cookpad ...

php spark migrate        # Jalankan migrasi (v1 + v2)
php spark db:seed InitialSeeder
php spark serve
```

Buka: **http://localhost:8080**

---

## Akun Demo

| Role            | Email                     | Password |
|-----------------|---------------------------|----------|
| Admin           | admin@cookpad.com         | admin123 |
| Chef Verified   | chef.rina@cookpad.com     | chef123  |
| Chef Verified   | chef.takeshi@cookpad.com  | chef123  |
| Chef (Unverif.) | chef.marco@cookpad.com    | chef123  |
| User Free       | user.andi@cookpad.com     | user123  |
| User Free       | user.sari@cookpad.com     | user123  |

---

## Fitur Baru v2

### 🪙 Sistem Koin (Ganti Subscription)
- Beli koin dengan Rupiah via QRIS / Virtual Account
- Harga paket: 10 koin (Rp 5.000) s/d 500+200 koin (Rp 150.000)
- Resep premium dibeli per-unlock dengan koin (5–50 koin/resep)
- Riwayat transaksi koin lengkap

### 👨‍🍳 Dual Chef Verification
| Jalur | Syarat | Hasil | Revenue |
|-------|--------|-------|---------|
| Basic | Upload KTP | Role: Chef | 50% per unlock |
| Advanced | Upload Sertifikat | Role: Chef Verified | 70% per unlock |

### 💰 Bagi Hasil Resep Premium
- **Chef Verified**: 70% koin, platform 30%
- **Chef biasa**: 50% koin, platform 50%
- Contoh resep harga 10 koin → Chef Verified dapat 7 koin, Chef biasa 5 koin

### Paket Koin
| Paket   | Koin     | Bonus | Harga      |
|---------|----------|-------|------------|
| Starter | 10       | 0     | Rp 5.000   |
| Basic   | 50       | +5    | Rp 20.000  |
| Popular | 100      | +20   | Rp 45.000  |
| Pro     | 200      | +60   | Rp 80.000  |
| Master  | 500      | +200  | Rp 150.000 |
