# Debug Upload Gambar - Summary

## Masalah

Error: "The files.0 failed to upload" saat mencoba mengupload gambar 3.2MB

## Analisis Masalah

### Kemungkinan Penyebab (5-7 Sumber Masalah)

1. **PHP Configuration** - `upload_max_filesize` dan `post_max_size` terlalu kecil
2. **Livewire Configuration** - Disk atau directory temporary file upload salah konfigurasi
3. **Permission Directory** - Directory `storage/app/livewire-tmp` tidak ada atau permission tidak benar
4. **APP_URL** - APP_URL di `.env` tidak sesuai dengan port yang digunakan oleh `php artisan serve`
5. **Livewire Middleware** - Middleware group tidak benar atau ada masalah dengan CSRF token
6. **Session/Cache Driver** - Redis connection bermasalah atau session tidak tersimpan dengan benar
7. **Validation Rules** - Validation rules di Livewire atau aplikasi terlalu ketat

### Penyebab yang Paling Mungkin (1-2 Sumber Utama)

1. **APP_URL tidak sesuai** - APP_URL di `.env` adalah `http://localhost` tapi `php artisan serve` berjalan di port 8000. Ini menyebabkan Livewire temporary file upload endpoint tidak bisa mengakses file dengan benar.

2. **Permission Directory** - Directory `storage/app/livewire-tmp` memiliki permission `drwx------` (700) yang terlalu ketat, menyebabkan Livewire tidak bisa menyimpan file temporary.

## Perbaikan yang Dilakukan

### 1. Mengubah PHP Configuration

**File**: `/etc/php/8.5/cli/php.ini`

```ini
upload_max_filesize = 100M
post_max_size = 110M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

Verifikasi:
```bash
php -i | grep -E "upload_max_filesize|post_max_size|memory_limit|max_execution_time"
```

Expected output:
```
upload_max_filesize => 100M => 100M
post_max_size => 110M => 110M
memory_limit => 512M => 512M
max_execution_time => 300 => 300
```

### 2. Memperbaiki Permission Directory

```bash
chmod -R 775 storage/app/livewire-tmp
```

### 3. Mengubah APP_URL di .env

**File**: `.env`

```env
APP_URL=http://localhost:8000
```

### 4. Memperbaiki Livewire Configuration

**File**: `config/livewire.php`

```php
'temporary_file_upload' => [
    'disk' => 'local',     // Gunakan disk local untuk temporary uploads
    'rules' => ['file', 'mimes:jpeg,jpg,png,gif,webp'], // Tidak ada batasan ukuran file
    'directory' => 'livewire-tmp',   // Directory untuk temporary uploads
    'middleware' => null,
],
```

### 5. Membersihkan Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 6. Menambahkan Logging

**File**: `app/Http/Livewire/ImageUploadComponent.php`

Menambahkan Log facade dan logging di berbagai titik untuk debugging:
- Saat memulai upload process
- Saat validation gagal
- Saat processing image
- Saat error terjadi

## Langkah-langkah untuk Mengaktifkan Perubahan

### 1. Restart PHP Artisan Serve

```bash
# Stop server (tekan Ctrl+C di terminal artisan serve)

# Start ulang
php artisan serve
```

### 2. Clear Browser Cache

- Clear browser cache
- Clear local storage
- Clear session storage

### 3. Test Upload Gambar

1. Buka halaman upload gambar
2. Pilih gambar 3.2MB
3. Upload gambar
4. Periksa Laravel log jika error masih terjadi:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Debugging Lanjutan

### Jika Error Masih Terjadi

#### 1. Periksa Laravel Log

```bash
tail -f storage/logs/laravel.log
```

Cari error messages seperti:
- "Validation failed"
- "Failed to upload image"
- "Image validation failed"

#### 2. Periksa Browser Console

Buka browser console (F12) dan periksa:
- Network tab untuk failed requests
- Console tab untuk JavaScript errors
- Application tab untuk session/storage issues

#### 3. Periksa Livewire Network Requests

Di browser Network tab, cari request ke:
- `/livewire/upload/file`
- `/livewire/message/...`

Periksa:
- Response status code
- Response body
- Request headers
- Response headers

#### 4. Periksa Temporary Directory

```bash
ls -la storage/app/livewire-tmp/
```

Pastikan:
- Directory ada
- Permission benar (775 atau 777)
- Owner benar

#### 5. Periksa Redis Connection

```bash
redis-cli ping
```

Expected output:
```
PONG
```

#### 6. Periksa Session

```bash
redis-cli
> KEYS laravel_session*
> GET <session_key>
```

#### 7. Test dengan File Kecil

Coba upload file yang sangat kecil (< 1MB) untuk melihat apakah masalah terkait ukuran file atau tidak.

#### 8. Test dengan File Format Berbeda

Coba upload file dengan format berbeda (JPEG, PNG, GIF, WebP) untuk melihat apakah masalah terkait format file.

#### 9. Periksa Livewire Version

```bash
composer show livewire/livewire
```

Pastikan menggunakan versi terbaru.

#### 10. Periksa Intervention Image

```bash
composer show intervention/image
```

Pastikan intervention/image terinstall dengan benar.

## Troubleshooting Spesifik

### Error: "The files.0 failed to upload"

Ini adalah error dari Livewire yang terjadi saat temporary file upload gagal.

**Kemungkinan Penyebab:**
1. APP_URL tidak sesuai dengan port yang digunakan
2. Temporary directory tidak ada atau permission salah
3. Livewire temporary file upload endpoint tidak bisa diakses
4. CSRF token tidak valid
5. Session tidak tersimpan dengan benar

**Solusi:**
1. Pastikan APP_URL di `.env` sesuai dengan port yang digunakan
2. Pastikan directory `storage/app/livewire-tmp` ada dan permission benar
3. Clear cache dan restart server
4. Clear browser cache dan session
5. Periksa browser console untuk error messages

### Error: "413 Request Entity Too Large"

Ini adalah error dari server yang menunjukkan ukuran request terlalu besar.

**Solusi:**
1. Periksa `upload_max_filesize` dan `post_max_size` di php.ini
2. Periksa `client_max_body_size` di Nginx (jika menggunakan Nginx)
3. Periksa `LimitRequestBody` di Apache (jika menggunakan Apache)

### Error: "500 Internal Server Error"

Ini adalah error dari server yang menunjukkan ada masalah di server side.

**Solusi:**
1. Periksa Laravel log: `tail -f storage/logs/laravel.log`
2. Periksa PHP error log: `tail -f /var/log/php8.5-cli.log`
3. Periksa web server error log (Apache/Nginx)
4. Periksa apakah Redis berjalan: `redis-cli ping`

### Error: "419 Page Expired"

Ini adalah error yang menunjukkan CSRF token tidak valid.

**Solusi:**
1. Clear browser cache dan session
2. Clear Laravel cache: `php artisan cache:clear`
3. Clear session: `php artisan session:table && php artisan migrate`
4. Periksa APP_URL di `.env`

## Ringkasan Perubahan

| File | Perubahan |
|------|-----------|
| `.env` | Mengubah APP_URL ke `http://localhost:8000` |
| `config/livewire.php` | Mengubah disk ke `local`, directory ke `livewire-tmp` |
| `app/Http/Livewire/ImageUploadComponent.php` | Menambahkan Log facade dan logging |
| `storage/app/livewire-tmp` | Mengubah permission ke 775 |

## Checklist Sebelum Testing

- [ ] PHP configuration sudah diubah dan terverifikasi
- [ ] Directory `storage/app/livewire-tmp` ada dan permission benar
- [ ] APP_URL di `.env` sesuai dengan port yang digunakan
- [ ] Livewire configuration sudah diperbarui
- [ ] Cache sudah dibersihkan
- [ ] Server `php artisan serve` sudah di-restart
- [ ] Browser cache sudah dibersihkan
- [ ] Redis berjalan dengan benar

## Next Steps

1. Restart `php artisan serve`
2. Clear browser cache
3. Test upload gambar 3.2MB
4. Periksa Laravel log jika error masih terjadi
5. Jika error masih terjadi, berikan informasi detail:
   - Error message lengkap
   - Laravel log output
   - Browser console output
   - Network request details

## Referensi

- [Laravel File Uploads](https://laravel.com/docs/10.x/filesystem#file-uploads)
- [Livewire File Uploads](https://livewire.laravel.com/docs/file-uploads)
- [PHP Configuration](https://www.php.net/manual/en/ini.list.php)
