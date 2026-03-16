# Perbaikan Tombol Lihat, Edit, dan Hapus - Image CRUD

## Ringkasan Masalah

Tombol lihat, edit, dan hapus pada halaman galeri foto tidak berfungsi dengan benar. Masalah terletak pada:

1. **Tombol Edit dan Hapus di `resources/views/images/index.blade.php`** salah memanggil method
2. **Method `openImageModal` di `ImageIndexComponent`** menerima parameter yang salah (object Image bukan string UUID)
3. **Event flow** tidak lengkap dari `ImageIndexComponent` ke `ImageModalComponent`

## Detail Masalah

### Masalah 1: resources/views/images/index.blade.php

**Lokasi:** Baris 134 dan 143

**Isu:**
- Tombol Edit (baris 134) memanggil `openImageModal` seharusnya `openEditModal`
- Tombol Hapus (baris 143) memanggil `openImageModal` seharusnya `openDeleteModal`

**Kode Saat Ini:**
```blade
<!-- Tombol Edit (SALAH) -->
<button wire:click="openImageModal('{{ $image->uuid }}')" ...>

<!-- Tombol Hapus (SALAH) -->
<button wire:click="openImageModal('{{ $image->uuid }}')" ...>
```

**Kode yang Diharapkan:**
```blade
<!-- Tombol Edit (BENAR) -->
<button wire:click="openEditModal('{{ $image->uuid }}')" ...>

<!-- Tombol Hapus (BENAR) -->
<button wire:click="openDeleteModal('{{ $image->uuid }}')" ...>
```

---

### Masalah 2: app/Http/Livewire/ImageIndexComponent.php

**Lokasi:** Baris 106-112

**Isu:**
- Method `openImageModal` menerima parameter `Image $image` (object)
- Di view dikirim string UUID, sehingga Laravel tidak bisa auto-resolve
- Method tidak mengirim event ke `ImageModalComponent`

**Kode Saat Ini:**
```php
public function openImageModal(Image $image): void
{
    $this->selectedImage = $image;
    $this->showImageModal = true;
}
```

**Kode yang Diharapkan:**
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

---

### Masalah 3: Event Flow

**Alur yang Diharapkan:**

```
User Click Button (index.blade.php)
    ↓
ImageIndexComponent Method
    ↓
Emit to ImageModalComponent
    ↓
ImageModalComponent Listener
    ↓
Modal Opens with Image Data
```

**Alur Saat Ini (View Button):**
```
User Click Button
    ↓
ImageIndexComponent.openImageModal(Image $image) ← ERROR: Type mismatch
    ↓
Tidak ada emit ke modal
    ↓
Modal tidak terbuka
```

**Alur Saat Ini (Grid Button - Sudah Benar):**
```
User Click Button (grid.blade.php)
    ↓
ImageGridComponent.openImageModal($uuid)
    ↓
Emit 'openImageModal' ke parent
    ↓
ImageModalComponent Listener (openModal)
    ↓
Modal Opens
```

---

## Rencana Perbaikan

### File 1: app/Http/Livewire/ImageIndexComponent.php

**Perubahan:**
1. Ubah parameter method `openImageModal` dari `Image $image` menjadi `$imageUuid`
2. Tambahkan logika untuk mencari image berdasarkan UUID
3. Tambahkan emit event ke `ImageModalComponent`

**Kode Lengkap Method yang Diperbaiki:**
```php
/**
 * Open image modal
 */
public function openImageModal($imageUuid): void
{
    $this->selectedImage = Image::where('uuid', $imageUuid)->first();
    if ($this->selectedImage) {
        $this->showImageModal = true;
        $this->emitTo('image-modal-component', 'openImageModal', $imageUuid);
    }
}
```

---

### File 2: resources/views/images/index.blade.php

**Perubahan:**
1. Ubah tombol Edit (baris 134) dari `openImageModal` menjadi `openEditModal`
2. Ubah tombol Hapus (baris 143) dari `openImageModal` menjadi `openDeleteModal`

**Kode Tombol Edit yang Diperbaiki (Baris 133-141):**
```blade
<button 
    wire:click="openEditModal('{{ $image->uuid }}')"
    class="px-3 py-1 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors"
    title="Edit"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
    </svg>
</button>
```

**Kode Tombol Hapus yang Diperbaiki (Baris 142-150):**
```blade
<button 
    wire:click="openDeleteModal('{{ $image->uuid }}')"
    class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors"
    title="Hapus"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
    </svg>
</button>
```

---

## Verifikasi Event Listeners

### ImageModalComponent.php (Sudah Benar)

Event listeners yang terdaftar:
```php
protected $listeners = [
    'openImageModal' => 'openModal',
    'closeImageModal' => 'closeModal',
    'enableEditMode' => 'enableEditMode',
    'enableDeleteMode' => 'enableDeleteMode',
];
```

**Mapping Event:**
| Event yang Dikirim | Method yang Dipanggil | Keterangan |
|-------------------|----------------------|------------|
| `openImageModal` | `openModal($imageUuid)` | Membuka modal view |
| `enableEditMode` | `enableEditMode()` | Mengaktifkan mode edit |
| `enableDeleteMode` | `enableDeleteMode()` | Mengaktifkan mode hapus |

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

## Checklist Perbaikan

- [ ] Perbaiki method `openImageModal` di `ImageIndexComponent.php`
  - Ubah parameter dari `Image $image` menjadi `$imageUuid`
  - Tambahkan query untuk mencari image berdasarkan UUID
  - Tambahkan emit event ke `ImageModalComponent`

- [ ] Perbaiki tombol Edit di `resources/views/images/index.blade.php`
  - Ubah `wire:click="openImageModal"` menjadi `wire:click="openEditModal"`

- [ ] Perbaiki tombol Hapus di `resources/views/images/index.blade.php`
  - Ubah `wire:click="openImageModal"` menjadi `wire:click="openDeleteModal"`

- [ ] Verifikasi event listeners di `ImageModalComponent.php` sudah benar

---

## Testing Setelah Perbaikan

### Test Tombol Lihat
1. Klik tombol Lihat pada salah satu gambar
2. Modal harus terbuka menampilkan gambar full size
3. Informasi gambar (nama, ukuran, tanggal) harus tampil
4. Tombol Download harus berfungsi

### Test Tombol Edit
1. Klik tombol Edit pada salah satu gambar
2. Modal harus terbuka dalam mode edit
3. Field caption harus bisa diedit
4. Tombol Simpan harus mengupdate caption
5. SweetAlert success harus muncul setelah update

### Test Tombol Hapus
1. Klik tombol Hapus pada salah satu gambar
2. Modal harus terbuka dengan konfirmasi hapus
3. Tombol "Ya, Hapus" harus menghapus gambar
4. SweetAlert success harus muncul setelah hapus
5. Gambar harus hilang dari grid

---

## Catatan Tambahan

1. **UUID vs ID**: Menggunakan UUID sebagai primary key untuk security dan uniqueness
2. **Event Flow**: Menggunakan `emitTo()` untuk mengirim event ke komponen spesifik
3. **SweetAlert**: Menggunakan SweetAlert untuk konfirmasi dan notifikasi
4. **Cache Invalidation**: Setelah update/delete, cache harus di-refresh melalui event `refreshGrid`

---

## File yang Perlu Dimodifikasi

1. `app/Http/Livewire/ImageIndexComponent.php`
2. `resources/views/images/index.blade.php`

## File yang Tidak Perlu Dimodifikasi (Sudah Benar)

1. `app/Http/Livewire/ImageModalComponent.php`
2. `resources/views/livewire/image-modal-component.blade.php`
3. `resources/views/livewire/image-grid-component.blade.php`
4. `app/Http/Livewire/ImageGridComponent.php`
