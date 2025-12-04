# INSTRUKSI FITUR PILIHAN SAUS

## Langkah 1: Update Database

1. Buka **phpMyAdmin** (http://localhost/phpmyadmin)
2. Pilih database aplikasi Anda
3. Klik tab **SQL**
4. Copy dan jalankan query berikut:

```sql
ALTER TABLE item_pesanan
ADD COLUMN catatan TEXT NULL AFTER subtotal,
ADD COLUMN level_pedas VARCHAR(100) NULL AFTER catatan;
```

5. Klik **Go/Kirim**

## Langkah 2: Konfigurasi di Admin Panel

1. Login ke **Admin Panel** (admin/login.php)
2. Buka menu **Settings/Pengaturan**
3. Scroll ke bagian **"Pilihan Saus Produk"**
4. Centang pilihan saus yang ingin ditampilkan:
   - ☑️ Tidak Bersaus
   - ☑️ Pedas
   - ☑️ Manis
   - ☑️ Pedas Manis
5. Ubah label jika perlu (default: "Pilih Saus")
6. Klik **"Simpan Pengaturan Saus"**

## Cara Kerja Fitur:

### Untuk Pelanggan:

1. Tambah produk ke keranjang
2. Buka halaman **Cart/Keranjang**
3. Setiap item akan menampilkan pilihan saus dengan **checkbox**
4. Pelanggan bisa memilih **LEBIH DARI SATU** pilihan saus:
   - Contoh: Bisa pilih "Pedas" dan "Manis" sekaligus
   - Atau pilih "Tidak Bersaus" saja
   - Atau pilih "Pedas Manis" saja
5. Isi catatan tambahan jika perlu
6. Klik **"Simpan Catatan"**
7. Pilihan saus akan tersimpan dan ditampilkan di:
   - ✅ Badge di keranjang
   - ✅ Ringkasan checkout
   - ✅ Pesan WhatsApp ke admin

### Untuk Admin:

1. Admin menerima pesan WhatsApp dengan detail:
   - Nama produk dan jumlah
   - Pilihan saus (misal: "Pedas, Manis")
   - Catatan khusus (jika ada)
2. Admin bisa mengatur pilihan saus apa saja yang tersedia
3. Bisa mengubah label sesuai kebutuhan

## Keunggulan Fitur:

✅ **Multiple Choice** - Pelanggan bisa pilih lebih dari 1 saus
✅ **Customizable** - Admin bisa atur pilihan saus dari panel
✅ **Terintegrasi** - Otomatis muncul di checkout dan WhatsApp
✅ **User Friendly** - UI checkbox yang mudah dipahami
✅ **Flexible** - Bisa tambah/kurangi pilihan dari admin

## Pilihan Saus Default:

1. **Tidak Bersaus** - Untuk yang tidak ingin saus
2. **Pedas** - Saus pedas
3. **Manis** - Saus manis
4. **Pedas Manis** - Kombinasi pedas dan manis

Admin bisa non-aktifkan pilihan yang tidak diinginkan dari admin panel.
