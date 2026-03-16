# Perbaikan Event Flow untuk Tombol Edit dan Hapus

## Ringkasan

Perbaikan tambahan untuk memastikan tombol Edit dan Hapus berfungsi dengan benar. Masalah utama adalah modal tidak terbuka saat tombol Edit atau Hapus diklik karena event flow tidak lengkap.

## Masalah yang Ditemukan

### Masalah Utama
Di `ImageIndexComponent`, method `openEditModal` dan `openDeleteModal` hanya mengirim event untuk mengaktifkan mode edit/hapus (`enableEditMode` / `enableDeleteMode`), tapi tidak mengirim event untuk membuka modal (`openImageModal`).

**Akibat:**
- Modal tidak terbuka saat tombol Edit/Hapus diklik
- Flag `$isEditing` atau `$showDeleteConfirm` diubah, tapi `$showModal` tetap false
- User tidak melihat modal sama sekali

---

## Perbaikan yang Dilakukan

### File: `app/Http/Livewire/ImageIndexComponent.php`

#### Method `openEditModal()`

**Sebelum:**
```php
public function openEditModal($imageUuid): void
{
    $this->selectedImage = Image::where('uuid', $imageUuid)->first();
    if ($this->selectedImage) {
        $this->showImageModal = true;
        $this->emitTo('image-modal-component', 'enableEditMode');
    }
}
```

**Sesudah:**
```php
public function openEditModal($imageUuid): void
{
    $this->selectedImage = Image::where('uuid', $imageUuid)->first();
    if ($this->selectedImage) {
        $this->showImageModal = true;
        // Buka modal terlebih dahulu, lalu aktifkan mode edit
        $this->emitTo('image-modal-component', 'openImageModal', $imageUuid);
        $this->emitTo('image-modal-component', 'enableEditMode');
    }
}
```

**Perubahan:**
- Menambahkan emit `openImageModal` sebelum `enableEditMode`
- Mengirim UUID gambar bersama event `openImageModal`

---

#### Method `openDeleteModal()`

**Sebelum:**
```php
public function openDeleteModal($imageUuid): void
{
    $this->selectedImage = Image::where('uuid', $imageUuid)->first();
    if ($this->selectedImage) {
        $this->showImageModal = true;
        $this->emitTo('image-modal-component', 'enableDeleteMode');
    }
}
```

**Sesudah:**
```php
public function openDeleteModal($imageUuid): void
{
    $this->selectedImage = Image::where('uuid', $imageUuid)->first();
    if ($this->selectedImage) {
        $this->showImageModal = true;
        // Buka modal terlebih dahulu, lalu aktifkan mode hapus
        $this->emitTo('image-modal-component', 'openImageModal', $imageUuid);
        $this->emitTo('image-modal-component', 'enableDeleteMode');
    }
}
```

**Perubahan:**
- Menambahkan emit `openImageModal` sebelum `enableDeleteMode`
- Mengirim UUID gambar bersama event `openImageModal`

---

## Alur Event Setelah Perbaikan

### Tombol Lihat (View)
```
User klik tombol Lihat
    ↓
ImageIndexComponent.openImageModal($imageUuid)
    ↓
Emit 'openImageModal' ke ImageModalComponent
    ↓
ImageModalComponent.openModal($imageUuid)
    ↓
  - Cari image berdasarkan UUID
  - Set $showModal = true
  - Set $isEditing = false
  - Set $showDeleteConfirm = false
    ↓
Modal terbuka dalam mode view
```

### Tombol Edit
```
User klik tombol Edit
    ↓
ImageIndexComponent.openEditModal($imageUuid)
    ↓
Emit 'openImageModal' ke ImageModalComponent
    ↓
ImageModalComponent.openModal($imageUuid)
    ↓
  - Cari image berdasarkan UUID
  - Set $showModal = true
  - Set $isEditing = false
  - Set $showDeleteConfirm = false
    ↓
Emit 'enableEditMode' ke ImageModalComponent
    ↓
ImageModalComponent.enableEditMode()
    ↓
  - Set $isEditing = true
    ↓
Modal terbuka dalam mode edit
```

### Tombol Hapus
```
User klik tombol Hapus
    ↓
ImageIndexComponent.openDeleteModal($imageUuid)
    ↓
Emit 'openImageModal' ke ImageModalComponent
    ↓
ImageModalComponent.openModal($imageUuid)
    ↓
  - Cari image berdasarkan UUID
  - Set $showModal = true
  - Set $isEditing = false
  - Set $showDeleteConfirm = false
    ↓
Emit 'enableDeleteMode' ke ImageModalComponent
    ↓
ImageModalComponent.enableDeleteMode()
    ↓
  - Set $showDeleteConfirm = true
    ↓
Modal terbuka dengan konfirmasi hapus
```

---

## Penting: Urutan Event

Urutan event sangat penting untuk memastikan modal berfungsi dengan benar:

1. **Event `openImageModal` harus dikirim PERTAMA**
   - Membuka modal
   - Mengambil data gambar berdasarkan UUID
   - Reset semua flag ke default

2. **Event mode (`enableEditMode` / `enableDeleteMode`) dikirim KEDUA**
   - Mengubah flag mode sesuai kebutuhan
   - Modal sudah terbuka, jadi perubahan flag akan terlihat

---

## Event Listeners di ImageModalComponent

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
| `openImageModal` | `openModal($imageUuid)` | Membuka modal dengan gambar |
| `enableEditMode` | `enableEditMode()` | Mengaktifkan mode edit |
| `enableDeleteMode` | `enableDeleteMode()` | Mengaktifkan mode hapus |

---

## Testing

### Test Tombol Edit
1. Buka halaman galeri foto
2. Klik tombol Edit (ikon pensil) pada salah satu gambar
3. Modal harus terbuka menampilkan gambar
4. Field caption harus dalam mode edit (textarea)
5. Tombol Simpan dan Batal harus terlihat
6. Edit caption dan klik Simpan
7. SweetAlert success harus muncul
8. Caption harus terupdate

### Test Tombol Hapus
1. Buka halaman galeri foto
2. Klik tombol Hapus (ikon sampah) pada salah satu gambar
3. Modal harus terbuka menampilkan gambar
4. Tombol konfirmasi hapus harus terlihat ("Ya, Hapus" dan "Batal")
5. Klik "Ya, Hapus"
6. SweetAlert success harus muncul
7. Gambar harus hilang dari grid
8. File gambar dan thumbnail harus dihapus dari storage

---

## Catatan Penting

1. **Urutan Event**: `openImageModal` harus dikirim sebelum event mode
2. **UUID Parameter**: UUID gambar dikirim bersama event `openImageModal`
3. **Livewire Event**: Menggunakan `emitTo()` untuk mengirim event ke komponen spesifik
4. **Modal State**: Modal state (`$showModal`) dikontrol oleh event `openImageModal`

---

## Referensi

- Perbaikan sebelumnya: [`docs/image-crud-buttons-fix-summary.md`](image-crud-buttons-fix-summary.md)
- Plan detail: [`plans/image-crud-buttons-fix-plan.md`](../plans/image-crud-buttons-fix-plan.md)
- Dokumentasi implementasi: [`docs/image-crud-implementation-summary.md`](image-crud-implementation-summary.md)
