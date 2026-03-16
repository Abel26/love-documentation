# Perbaikan Tombol Lihat, Edit, dan Hapus - Image CRUD

## Ringkasan

Perbaikan untuk tombol lihat, edit, dan hapus pada halaman galeri foto agar berfungsi dengan benar.

## Masalah yang Ditemukan

### 1. Method `openImageModal` di `ImageIndexComponent.php`

**Lokasi:** `app/Http/Livewire/ImageIndexComponent.php` baris 106-112

**Masalah:**
- Method menerima parameter `Image $image` (object), padahal dikirim string UUID
- Tidak ada emit event ke `ImageModalComponent` untuk membuka modal

**Sebelum:**
```php
public function openImageModal(Image $image): void
{
    $this->selectedImage = $image;
    $this->showImageModal = true;
}
```

**Sesudah:**
```php
public function openImageModal($imageUuid): void
{
    $this->selectedImage = Image::where('uuid', $imageUuid)->first();
    if ($this->selectedImage) {
        $this->showImageModal = true;
        $this->emitTo('image-modal-component', 'openImageModal', $imageUuid);
    }
}
```

**Perubahan:**
- Parameter diubah dari `Image $image` menjadi `$imageUuid` (string)
- Menambahkan query untuk mencari image berdasarkan UUID
- Menambahkan emit event ke `ImageModalComponent` dengan `emitTo()`

---

### 2. Tombol Edit di `resources/views/images/index.blade.php`

**Lokasi:** `resources/views/images/index.blade.php` baris 133-141

**Masalah:**
- Tombol Edit memanggil `openImageModal` seharusnya `openEditModal`

**Sebelum:**
```blade
<button wire:click="openImageModal('{{ $image->uuid }}')" ...>
```

**Sesudah:**
```blade
<button wire:click="openEditModal('{{ $image->uuid }}')" ...>
```

---

### 3. Tombol Hapus di `resources/views/images/index.blade.php`

**Lokasi:** `resources/views/images/index.blade.php` baris 142-150

**Masalah:**
- Tombol Hapus memanggil `openImageModal` seharusnya `openDeleteModal`

**Sebelum:**
```blade
<button wire:click="openImageModal('{{ $image->uuid }}')" ...>
```

**Sesudah:**
```blade
<button wire:click="openDeleteModal('{{ $image->uuid }}')" ...>
```

---

## Alur Event Setelah Perbaikan

### Tombol Lihat (View)
```
User klik tombol Lihat
    ↓
ImageIndexComponent.openImageModal($imageUuid)
    ↓
Cari image berdasarkan UUID
    ↓
Emit 'openImageModal' ke ImageModalComponent
    ↓
ImageModalComponent.openModal($imageUuid)
    ↓
Modal terbuka dengan gambar
```

### Tombol Edit
```
User klik tombol Edit
    ↓
ImageIndexComponent.openEditModal($imageUuid)
    ↓
Cari image berdasarkan UUID
    ↓
Emit 'enableEditMode' ke ImageModalComponent
    ↓
ImageModalComponent.enableEditMode()
    ↓
Modal terbuka dalam mode edit
```

### Tombol Hapus
```
User klik tombol Hapus
    ↓
ImageIndexComponent.openDeleteModal($imageUuid)
    ↓
Cari image berdasarkan UUID
    ↓
Emit 'enableDeleteMode' ke ImageModalComponent
    ↓
ImageModalComponent.enableDeleteMode()
    ↓
Modal terbuka dengan konfirmasi hapus
```

---

## File yang Dimodifikasi

1. **`app/Http/Livewire/ImageIndexComponent.php`**
   - Method `openImageModal()` diperbaiki

2. **`resources/views/images/index.blade.php`**
   - Tombol Edit diperbaiki (baris 133-141)
   - Tombol Hapus diperbaiki (baris 142-150)

---

## File yang Tidak Perlu Dimodifikasi (Sudah Benar)

1. **`app/Http/Livewire/ImageModalComponent.php`**
   - Event listeners sudah benar:
     - `openImageModal` → `openModal`
     - `enableEditMode` → `enableEditMode`
     - `enableDeleteMode` → `enableDeleteMode`

2. **`resources/views/livewire/image-modal-component.blade.php`**
   - Template modal sudah benar

3. **`resources/views/livewire/image-grid-component.blade.php`**
   - Tombol di grid sudah menggunakan event yang benar

4. **`app/Http/Livewire/ImageGridComponent.php`**
   - Event emission sudah benar

---

## Testing

### Test Tombol Lihat
1. Buka halaman galeri foto
2. Klik tombol Lihat (ikon mata) pada salah satu gambar
3. Modal harus terbuka menampilkan gambar full size
4. Informasi gambar (nama, ukuran, tanggal) harus tampil
5. Tombol Download harus berfungsi

### Test Tombol Edit
1. Buka halaman galeri foto
2. Klik tombol Edit (ikon pensil) pada salah satu gambar
3. Modal harus terbuka dalam mode edit
4. Field caption harus bisa diedit
5. Klik tombol Simpan
6. SweetAlert success harus muncul
7. Caption harus terupdate

### Test Tombol Hapus
1. Buka halaman galeri foto
2. Klik tombol Hapus (ikon sampah) pada salah satu gambar
3. Modal harus terbuka dengan konfirmasi hapus
4. Klik tombol "Ya, Hapus"
5. SweetAlert success harus muncul
6. Gambar harus hilang dari grid
7. File gambar dan thumbnail harus dihapus dari storage

---

## Catatan Penting

1. **UUID vs ID**: Menggunakan UUID sebagai primary key untuk security dan uniqueness
2. **Event Flow**: Menggunakan `emitTo()` untuk mengirim event ke komponen spesifik (`image-modal-component`)
3. **SweetAlert**: Menggunakan SweetAlert untuk konfirmasi dan notifikasi
4. **Cache Invalidation**: Setelah update/delete, cache di-refresh melalui event `refreshGrid`

---

## Troubleshooting

### Tombol tidak berfungsi
- Pastikan `php artisan serve` sedang berjalan
- Cek browser console untuk error JavaScript
- Pastikan Livewire ter-load dengan benar

### Modal tidak terbuka
- Cek event listeners di `ImageModalComponent`
- Pastikan event yang dikirim sesuai dengan yang didengarkan
- Cek browser console untuk error Livewire

### Edit/Hapus tidak berfungsi
- Pastikan method `openEditModal` dan `openDeleteModal` ada di `ImageIndexComponent`
- Cek apakah image ditemukan berdasarkan UUID
- Pastikan emit event ke `ImageModalComponent` berhasil

---

## Referensi

- Plan detail: [`plans/image-crud-buttons-fix-plan.md`](../plans/image-crud-buttons-fix-plan.md)
- Dokumentasi implementasi: [`docs/image-crud-implementation-summary.md`](image-crud-implementation-summary.md)
