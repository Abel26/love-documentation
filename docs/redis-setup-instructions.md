# Instruksi Setup Redis untuk Laravel Project

## Status Saat Ini

✅ **Redis Server**: Sudah terinstall dan berjalan (PONG response)
✅ **Konfigurasi .env**: Sudah diupdate untuk menggunakan Redis
✅ **Konfigurasi Laravel Config**: Sudah terverifikasi dan siap digunakan

⚠️ **PHP Redis Extension**: Perlu diinstall secara manual (tidak bisa menggunakan sudo tanpa password)

## Langkah yang Perlu Dilakukan Secara Manual

### 1. Install PHP Redis Extension

Karena saya tidak bisa menggunakan sudo tanpa password, Anda perlu menjalankan perintah berikut secara manual di terminal:

```bash
# Install PHP Redis extension untuk PHP 8.5
sudo apt install php8.5-redis -y

# Atau jika menggunakan PHP versi lain, sesuaikan dengan versi PHP Anda:
# sudo apt install php8.0-redis -y
# sudo apt install php8.1-redis -y
# sudo apt install php8.2-redis -y
# sudo apt install php8.3-redis -y
```

### 2. Restart PHP-FPM (jika menggunakan PHP-FPM)

```bash
# Restart PHP-FPM sesuai versi PHP Anda
sudo systemctl restart php8.5-fpm

# Atau sesuaikan dengan versi PHP Anda:
# sudo systemctl restart php8.0-fpm
# sudo systemctl restart php8.1-fpm
# sudo systemctl restart php8.2-fpm
# sudo systemctl restart php8.3-fpm
```

### 3. Verifikasi PHP Redis Extension

```bash
# Cek apakah extension sudah terload
php -m | grep redis

# Expected output: redis
```

### 4. Clear Laravel Cache

Setelah PHP Redis extension terinstall, jalankan perintah berikut untuk clear cache Laravel:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 5. Test Koneksi Redis

```bash
# Test dengan Artisan Tinker
php artisan tinker
```

Di dalam Tinker, jalankan perintah berikut:

```php
// Test basic connection
Redis::ping();
// Expected output: true

// Test set and get
Redis::set('test_key', 'test_value');
Redis::get('test_key');
// Expected output: 'test_value'

// Test delete
Redis::del('test_key');

// Test Cache
Cache::put('test_cache', 'test_value', 60);
Cache::get('test_cache');
// Expected output: 'test_value'

// Test Session
session(['test_session' => 'test_value']);
session('test_session');
// Expected output: 'test_value'
```

### 6. Test Queue Worker

Buat test job dan dispatch:

```bash
# Test job sudah dibuat di app/Jobs/TestJob.php
# Dispatch test job
php artisan tinker
```

Di dalam Tinker:

```php
App\Jobs\TestJob::dispatch();
```

Jalankan queue worker:

```bash
php artisan queue:work redis
```

### 7. Test Broadcasting (Opsional)

Jika Anda ingin menggunakan real-time events dengan Redis broadcasting:

```bash
# Install Laravel Echo Server (opsional)
npm install -g laravel-echo-server

# Initialize Laravel Echo Server
laravel-echo-server init

# Start Laravel Echo Server
laravel-echo-server start
```

## Konfigurasi yang Sudah Diupdate

### File: `.env`

Berikut adalah konfigurasi yang sudah diupdate:

```env
# Broadcasting Driver
BROADCAST_DRIVER=redis

# Cache Driver
CACHE_DRIVER=redis

# Queue Connection
QUEUE_CONNECTION=redis

# Session Driver
SESSION_DRIVER=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=phpredis
REDIS_DB=0
REDIS_CACHE_DB=1
```

### File: `config/database.php`

Redis connection sudah terkonfigurasi dengan benar:
- `default` connection menggunakan database 0
- `cache` connection menggunakan database 1

### File: `config/cache.php`

Redis store sudah terkonfigurasi dengan connection 'cache'.

### File: `config/queue.php`

Redis connection sudah terkonfigurasi dengan connection 'default'.

### File: `config/session.php`

Session driver sudah diset ke redis.

### File: `config/broadcasting.php`

Redis connection sudah terkonfigurasi dengan connection 'default'.

## Setup Queue Worker untuk Production (Opsional)

Jika Anda ingin menjalankan queue worker secara otomatis di production:

### Install Supervisor

```bash
sudo apt install supervisor -y
```

### Create Supervisor Config

Buat file konfigurasi supervisor:

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Isi dengan konfigurasi berikut:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/abel-natanael/dev/personal/love-documentation/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=abel-natanael
numprocs=2
redirect_stderr=true
stdout_logfile=/home/abel-natanael/dev/personal/love-documentation/storage/logs/worker.log
stopwaitsecs=3600
```

### Start Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Monitor Queue Worker

```bash
sudo supervisorctl status
```

## Troubleshooting

### Redis Connection Refused

Jika Anda mengalami error "Redis connection refused":

```bash
# Cek apakah Redis berjalan
sudo systemctl status redis-server

# Start Redis jika belum berjalan
sudo systemctl start redis-server

# Enable Redis untuk auto-start
sudo systemctl enable redis-server
```

### PHP Redis Extension Not Found

Jika `php -m | grep redis` tidak menampilkan output:

```bash
# Install PHP Redis extension
sudo apt install php8.5-redis -y

# Restart PHP-FPM
sudo systemctl restart php8.5-fpm

# Cek lagi
php -m | grep redis
```

### Queue Jobs Not Processing

Jika queue jobs tidak diproses:

```bash
# Cek queue worker status
sudo supervisorctl status

# Cek logs
tail -f storage/logs/worker.log

# Cek Redis queue
redis-cli
LLEN queues:default
```

### Session Not Persisting

Jika session tidak tersimpan:

```bash
# Verify session driver
php artisan tinker
echo config('session.driver');
# Expected output: redis

# Cek Redis untuk session keys
redis-cli
KEYS laravel_database_*
```

## Monitoring Redis

### Cek Redis Status

```bash
# Cek Redis info
redis-cli INFO

# Cek memory usage
redis-cli INFO memory

# Cek stats
redis-cli INFO stats

# Cek jumlah keys
redis-cli DBSIZE

# Cek semua keys
redis-cli KEYS *
```

### Monitor Redis Real-time

```bash
# Monitor Redis commands
redis-cli MONITOR
```

## Optimasi Redis untuk Production

### Konfigurasi Redis untuk Production

Edit file `/etc/redis/redis.conf`:

```bash
sudo nano /etc/redis/redis.conf
```

Tambahkan atau ubah konfigurasi berikut:

```conf
# Set password untuk keamanan
requirepass your_strong_password_here

# Set max memory (sesuaikan dengan RAM server)
maxmemory 256mb

# Set eviction policy
maxmemory-policy allkeys-lru

# Enable persistence (opsional)
save 900 1
save 300 10
save 60 10000
```

Update `.env`:

```env
REDIS_PASSWORD=your_strong_password_here
```

Restart Redis:

```bash
sudo systemctl restart redis-server
```

## Backup Redis Data (Opsional)

Jika Anda menggunakan persistence:

```bash
# Manual backup
redis-cli SAVE

# Copy dump file
sudo cp /var/lib/redis/dump.rdb /backup/redis-backup-$(date +%Y%m%d).rdb
```

## Checklist Final

- [ ] PHP Redis extension terinstall
- [ ] PHP-FPM direstart
- [ ] PHP Redis extension terverifikasi (`php -m | grep redis`)
- [ ] Laravel cache dibersihkan
- [ ] Koneksi Redis berhasil ditest dengan Tinker
- [ ] Cache operations berfungsi
- [ ] Queue jobs dapat diproses
- [ ] Session tersimpan di Redis
- [ ] Broadcasting berfungsi (opsional)
- [ ] Queue worker dikonfigurasi (opsional, untuk production)
- [ ] Semua fitur Redis berhasil ditest

## File yang Telah Dimodifikasi

- ✅ `.env` - Konfigurasi Redis dan driver settings
- ✅ `config/database.php` - Redis connection (sudah terkonfigurasi)
- ✅ `config/cache.php` - Redis cache store (sudah terkonfigurasi)
- ✅ `config/queue.php` - Redis queue connection (sudah terkonfigurasi)
- ✅ `config/session.php` - Redis session driver (sudah terkonfigurasi)
- ✅ `config/broadcasting.php` - Redis broadcasting connection (sudah terkonfigurasi)

## File yang Akan Dibuat

- 📝 `app/Jobs/TestJob.php` - Test job untuk testing queue
- 📝 `docs/redis-setup-instructions.md` - Dokumentasi ini

## Catatan Penting

1. **Security**: Selalu set password untuk Redis di production environment
2. **Memory**: Sesuaikan `maxmemory` dengan kapasitas RAM server
3. **Persistence**: Pertimbangkan untuk enable persistence (RDB/AOF) untuk production
4. **Monitoring**: Setup monitoring untuk Redis dan queue workers
5. **Backup**: Backup Redis data secara berkala jika menggunakan persistence
6. **Queue Workers**: Gunakan Supervisor untuk memastikan queue workers selalu berjalan
7. **Testing**: Test semua fitur Redis sebelum deploy ke production

## Referensi

- [Laravel Redis Documentation](https://laravel.com/docs/9.x/redis)
- [Redis Documentation](https://redis.io/documentation)
- [Laravel Queues Documentation](https://laravel.com/docs/9.x/queues)
- [Laravel Broadcasting Documentation](https://laravel.com/docs/9.x/broadcasting)
