# Perbaikan Upload Gambar untuk PHP Artisan Serve

## Masalah

Karena Anda menggunakan `php artisan serve`, file `.htaccess` dan `.user.ini` **TIDAK** akan bekerja karena PHP built-in server tidak membaca file-file tersebut.

## Solusi

### Opsi 1: Edit php.ini CLI (Rekomendasi)

Lokasi php.ini: `/etc/php/8.5/cli/php.ini`

```bash
# Backup php.ini terlebih dahulu
sudo cp /etc/php/8.5/cli/php.ini /etc/php/8.5/cli/php.ini.backup

# Edit php.ini
sudo nano /etc/php/8.5/cli/php.ini
```

Cari dan ubah baris berikut:

```ini
; Cari baris upload_max_filesize dan ubah menjadi:
upload_max_filesize = 100M

; Cari baris post_max_size dan ubah menjadi:
post_max_size = 110M

; Cari baris max_execution_time dan ubah menjadi:
max_execution_time = 300

; Cari baris max_input_time dan ubah menjadi:
max_input_time = 300

; Cari baris memory_limit dan ubah menjadi:
memory_limit = 512M
```

Simpan dan keluar (Ctrl+O, Enter, Ctrl+X).

### Opsi 2: Menggunakan Apache yang Sudah Berjalan

Karena Apache sudah berjalan di server Anda, Anda bisa menggunakan Apache sebagai gantinya:

```bash
# Stop php artisan serve (tekan Ctrl+C di terminal)

# Pastikan Apache sudah berjalan
sudo systemctl status apache2

# Jika Apache belum berjalan, jalankan:
sudo systemctl start apache2

# Pastikan Apache dapat mengakses direktori public
sudo chown -R www-data:www-data /home/abel-natanael/dev/personal/love-documentation
sudo chmod -R 775 /home/abel-natanael/dev/personal/love-documentation

# Pastikan storage directory writable
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Akses aplikasi via Apache di browser:
# http://localhost/
```

### Opsi 3: Menggunakan Nginx + PHP-FPM

Jika Anda ingin menggunakan Nginx:

```bash
# Install Nginx dan PHP-FPM (jika belum)
sudo apt install nginx php8.5-fpm

# Konfigurasi Nginx untuk Laravel
sudo nano /etc/nginx/sites-available/love-documentation
```

Tambahkan konfigurasi berikut:

```nginx
server {
    listen 80;
    server_name localhost;
    root /home/abel-natanael/dev/personal/love-documentation/public;

    index index.php index.html;

    # Upload size limit
    client_max_body_size 100M;
    client_body_timeout 300s;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Aktifkan site:

```bash
sudo ln -s /etc/nginx/sites-available/love-documentation /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

# Edit PHP-FPM php.ini
sudo nano /etc/php/8.5/fpm/php.ini

# Ubah konfigurasi yang sama seperti Opsi 1:
upload_max_filesize = 100M
post_max_size = 110M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M

# Restart PHP-FPM
sudo systemctl restart php8.5-fpm
```

### Opsi 4: Menggunakan Laravel Valet (Mac Only)

Jika Anda menggunakan Mac:

```bash
# Install Valet jika belum
composer global require laravel/valet
valet install

# Link project
cd /home/abel-natanael/dev/personal/love-documentation
valet link love-documentation

# Akses di browser:
# http://love-documentation.test
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

Buka file `routes/web.php` dan tambahkan route temporary:

```php
Route::get('/phpinfo', function () {
    phpinfo();
});
```

Akses: `http://localhost:8000/phpinfo` (untuk artisan serve)
Atau: `http://localhost/phpinfo` (untuk Apache/Nginx)

## Restart Server

### Untuk PHP Artisan Serve

```bash
# Stop server (tekan Ctrl+C di terminal artisan serve)

# Start ulang
php artisan serve
```

### Untuk Apache

```bash
sudo systemctl restart apache2
```

### Untuk Nginx + PHP-FPM

```bash
sudo systemctl restart nginx
sudo systemctl restart php8.5-fpm
```

## Troubleshooting

### Masalah: Permission Denied saat Edit php.ini

```bash
# Pastikan Anda memiliki akses sudo
sudo -v

# Cek ownership php.ini
ls -la /etc/php/8.5/cli/php.ini

# Jika perlu, ubah ownership (tidak disarankan)
sudo chown $USER:$(id -gn) /etc/php/8.5/cli/php.ini
```

### Masalah: Apache 403 Forbidden

```bash
# Pastikan permission directory benar
sudo chown -R www-data:www-data /home/abel-natanael/dev/personal/love-documentation
sudo chmod -R 775 /home/abel-natanael/dev/personal/love-documentation

# Pastikan storage dan bootstrap/cache writable
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

### Masalah: Nginx 502 Bad Gateway

```bash
# Cek status PHP-FPM
sudo systemctl status php8.5-fpm

# Cek PHP-FPM error log
sudo tail -f /var/log/php8.5-fpm.log

# Cek Nginx error log
sudo tail -f /var/log/nginx/error.log
```

### Masalah: Gambar Masih Gagal Diupload

1. Cek Laravel log:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Cek PHP error log:
   ```bash
   tail -f /var/log/php8.5-cli.log
   # atau untuk Apache
   tail -f /var/log/apache2/error.log
   ```

3. Verifikasi konfigurasi:
   ```bash
   php -i | grep -E "upload_max_filesize|post_max_size"
   ```

## Ringkasan

| Metode | Kelebihan | Kekurangan |
|--------|-----------|------------|
| Edit php.ini CLI | Mudah untuk development | Perlu akses sudo |
| Gunakan Apache | Tidak perlu edit php.ini (gunakan .htaccess) | Konfigurasi permission |
| Gunakan Nginx + PHP-FPM | Performa lebih baik | Konfigurasi lebih kompleks |
| Laravel Valet | Sangat mudah (Mac only) | Hanya untuk Mac |

## Rekomendasi

Untuk development di Linux, **rekomendasi saya adalah menggunakan Apache** yang sudah berjalan di server Anda karena:

1. Apache sudah terinstall dan berjalan
2. File `.htaccess` sudah dibuat dan akan bekerja dengan Apache
3. Tidak perlu mengedit php.ini secara manual
4. Lebih mirip dengan environment production

Langkah cepat:

```bash
# Stop php artisan serve (Ctrl+C)

# Pastikan Apache berjalan
sudo systemctl start apache2

# Set permission
sudo chown -R www-data:www-data /home/abel-natanael/dev/personal/love-documentation
sudo chmod -R 775 /home/abel-natanael/dev/personal/love-documentation
sudo chmod -R 775 storage bootstrap/cache

# Akses di browser
# http://localhost/
```
