# Perbaikan Upload Gambar - Summary

## Masalah

Error: "The files.0 failed to upload" saat mencoba mengupload gambar 3.2MB

## Penyebab Utama

Ada beberapa batasan ukuran file yang tidak konsisten:

### 1. PHP Configuration (Masalah Utama)
- `upload_max_filesize` = 2MB (default)
- `post_max_size` = 8MB (default)

Ini adalah batasan di server PHP yang menyebabkan gambar 3.2MB gagal diupload.

### 2. Livewire Configuration
- Default rules: `['required', 'file', 'max:12288']` (12MB)

### 3. Aplikasi Laravel
- `ImageProcessingService::validate()` membatasi ke 10MB
- `ImageUploadComponent` mengizinkan 50MB tapi service-nya hanya 10MB
- Error message tidak konsisten

## Perbaikan yang Dilakukan

### 1. Menghapus Batasan di ImageProcessingService

**File**: `app/Services/ImageProcessingService.php`

```php
// SEBELUM
public function validate(UploadedFile $file): bool
{
    $allowedMimeTypes = [...];
    $maxSize = 10 * 1024 * 1024; // 10MB

    return in_array($file->getMimeType(), $allowedMimeTypes)
        && $file->getSize() <= $maxSize;
}

// SESUDAH
public function validate(UploadedFile $file): bool
{
    $allowedMimeTypes = [...];
    // Tidak ada batasan ukuran file di sini
    return in_array($file->getMimeType(), $allowedMimeTypes);
}
```

### 2. Memperbaiki Error Message

**File**: `app/Http/Livewire/ImageUploadComponent.php`

```php
// SEBELUM
$this->uploadErrors[] = "File {$image->getClientOriginalName()} tidak valid atau terlalu besar (max 10MB)";

// SESUDAH
$this->uploadErrors[] = "File {$image->getClientOriginalName()} tidak valid. Format yang didukung: JPEG, PNG, GIF, WebP";
```

### 3. Menghapus Batasan di Livewire Config

**File**: `config/livewire.php`

```php
// SEBELUM
'rules' => null, // Default: ['required', 'file', 'max:12288'] (12MB)

// SESUDAH
'rules' => ['file', 'mimes:jpeg,jpg,png,gif,webp'], // Tidak ada batasan ukuran file
```

### 4. Menambahkan PHP Configuration Override

**File**: `public/.user.ini`

```ini
upload_max_filesize = 100M
post_max_size = 110M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

**File**: `public/.htaccess`

```apache
<IfModule mod_php.c>
    php_value upload_max_filesize 100M
    php_value post_max_size 110M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 512M
</IfModule>
```

## Langkah-langkah untuk Mengaktifkan Perubahan

### Opsi 1: Restart PHP-FPM (Rekomendasi untuk Local Development)

```bash
# Untuk Ubuntu/Debian
sudo systemctl restart php8.x-fpm

# Atau untuk Mac dengan Homebrew
brew services restart php
```

### Opsi 2: Restart Apache (jika menggunakan Apache)

```bash
sudo systemctl restart apache2
```

### Opsi 3: Restart Nginx (jika menggunakan Nginx)

```bash
sudo systemctl restart nginx
```

### Opsi 4: Jika Menggunakan PHP Built-in Server

```bash
# Stop server (Ctrl+C)
# Start ulang
php artisan serve
```

### Opsi 5: Jika Menggunakan Laravel Valet

```bash
valet restart
```

## Verifikasi Perubahan

### Cek PHP Configuration

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

### Cek di Browser

Buka `phpinfo()` di browser untuk memverifikasi perubahan:

```php
// routes/web.php (temporary)
Route::get('/phpinfo', function () {
    phpinfo();
});
```

Akses: `http://localhost:8000/phpinfo`

## Catatan Penting

### 1. .user.ini vs .htaccess

- **.user.ini**: Bekerja di shared hosting dengan PHP-FPM/CGI. File ini dibaca setiap 300 detik (default).
- **.htaccess**: Bekerja di Apache dengan mod_php.

### 2. Jika Perubahan Tidak Berlaku

Jika setelah restart server perubahan masih tidak berlaku, coba:

1. Hapus cache PHP:
   ```bash
   rm -rf storage/framework/cache/*
   ```

2. Tunggu beberapa menit (untuk .user.ini yang dibaca setiap 300 detik)

3. Edit php.ini secara langsung:
   ```bash
   # Cari lokasi php.ini
   php --ini

   # Edit file php.ini
   sudo nano /etc/php/8.x/apache2/php.ini
   # atau
   sudo nano /etc/php/8.x/fpm/php.ini

   # Tambahkan/ubah:
   upload_max_filesize = 100M
   post_max_size = 110M
   max_execution_time = 300
   max_input_time = 300
   memory_limit = 512M

   # Restart PHP-FPM
   sudo systemctl restart php8.x-fpm
   ```

### 3. Nginx Configuration

Jika menggunakan Nginx, tambahkan konfigurasi berikut di server block:

```nginx
server {
    ...
    client_max_body_size 100M;
    client_body_timeout 300s;
    ...
}
```

Restart Nginx:
```bash
sudo systemctl restart nginx
```

## Testing

### Test Upload Gambar 3.2MB

1. Buka halaman upload gambar
2. Pilih gambar 3.2MB
3. Upload gambar
4. Verifikasi gambar berhasil diupload dan diproses

### Test Upload Gambar Lebih Besar

1. Coba upload gambar 10MB, 20MB, dll.
2. Verifikasi gambar berhasil diupload
3. Verifikasi gambar dikompresi dengan benar

## Troubleshooting

### Masalah: Gambar masih gagal diupload

1. Cek error log:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Cek PHP error log:
   ```bash
   tail -f /var/log/php8.x-error.log
   ```

3. Verifikasi PHP configuration:
   ```bash
   php -i | grep -E "upload_max_filesize|post_max_size"
   ```

### Masalah: Timeout saat processing gambar besar

1. Tingkatkan `max_execution_time` dan `max_input_time`
2. Tingkatkan `memory_limit` untuk processing gambar
3. Pertimbangkan menggunakan queue untuk processing gambar besar

### Masalah: Server tidak bisa restart

1. Cek permission:
   ```bash
   ls -la public/.user.ini
   ls -la public/.htaccess
   ```

2. Pastikan file memiliki permission yang benar:
   ```bash
   chmod 644 public/.user.ini
   chmod 644 public/.htaccess
   ```

## Ringkasan Perubahan

| File | Perubahan |
|------|-----------|
| `app/Services/ImageProcessingService.php` | Hapus batasan 10MB |
| `app/Http/Livewire/ImageUploadComponent.php` | Perbaiki error message |
| `config/livewire.php` | Hapus batasan 12MB |
| `public/.user.ini` | Tambah PHP configuration override |
| `public/.htaccess` | Tambah PHP configuration override |

## Konfigurasi Baru

| Setting | Nilai Baru |
|---------|------------|
| `upload_max_filesize` | 100MB |
| `post_max_size` | 110MB |
| `max_execution_time` | 300 detik |
| `max_input_time` | 300 detik |
| `memory_limit` | 512MB |
| Livewire temporary upload rules | Tidak ada batasan |
| ImageProcessingService validation | Tidak ada batasan ukuran |
| ImageUploadComponent validation | 50MB (tetap) |

## Referensi

- [Laravel File Uploads](https://laravel.com/docs/10.x/filesystem#file-uploads)
- [PHP Configuration](https://www.php.net/manual/en/ini.list.php)
- [Livewire File Uploads](https://livewire.laravel.com/docs/file-uploads)
