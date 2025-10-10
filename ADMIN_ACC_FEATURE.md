# Fitur ACC (Approve/Reject) untuk Admin - Kunjungan Auditor

## Deskripsi
Fitur ini memungkinkan admin untuk menyetujui (ACC) atau menolak laporan kunjungan yang telah diselesaikan oleh auditor dengan status "Menunggu ACC".

## Fitur yang Ditambahkan

### 1. **Action Buttons untuk Status "Menunggu ACC"**
- Tombol **ACC / Setujui** (hijau) dengan icon checklist
- Tombol **Tolak / Reject** (merah) dengan icon X
- Hanya muncul pada kunjungan dengan status `menunggu_acc`

### 2. **Status Display yang Diperbarui**
- Status `menunggu_acc` ditampilkan dengan badge ungu dan animasi pulse
- Icon khusus untuk menunjukkan status perlu perhatian
- Teks: "Menunggu ACC Admin"

### 3. **Modal Konfirmasi ACC**
- Modal konfirmasi untuk approve laporan
- Peringatan bahwa status akan berubah menjadi "Selesai"
- Button "Ya, ACC Laporan" untuk konfirmasi

### 4. **Modal Reject dengan Form**
- Form input untuk alasan penolakan
- Validasi minimal 10 karakter, maksimal 500 karakter
- Character counter real-time dengan color coding
- Peringatan bahwa status akan kembali ke "Belum Dikunjungi"

### 5. **Stats Card "Menunggu ACC"**
- Card statistik khusus untuk menampilkan jumlah kunjungan yang menunggu ACC
- Warna ungu dengan animasi pulse untuk menarik perhatian
- Label "Perlu Perhatian"

### 6. **Filter Status**
- Opsi filter `menunggu_acc` ditambahkan ke dropdown status
- Statistik untuk `menunggu_acc` ditambahkan ke controller

## Implementasi Technical

### **Controller Changes**
File: `app/Http/Controllers/Admin/AdminVisitController.php`

1. **Status Array Update**:
   ```php
   $statuses = ['belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai', ...];
   ```

2. **Statistics Update**:
   ```php
   $stats = [
       // ... existing stats
       'menunggu_acc' => Visit::where('status', 'menunggu_acc')->count(),
       // ...
   ];
   ```

3. **Enhanced Approve Method**:
   - Supports both AJAX and regular requests
   - JSON responses for AJAX calls
   - Validation for correct status

4. **Enhanced Reject Method**:
   - Added validation for rejection notes (min 10 chars)
   - Supports both AJAX and regular requests
   - Clears report data when rejecting

### **Frontend Changes**
File: `resources/views/admin/visits/index.blade.php`

1. **Action Buttons**:
   ```php
   @if($visit->status === 'menunggu_acc')
       <!-- ACC and Reject buttons -->
   @endif
   ```

2. **Status Display**:
   ```php
   @elseif($visit->status === 'menunggu_acc')
       <span class="... bg-purple-50 text-purple-700 ... animate-pulse">
           <svg>...</svg>
           Menunggu ACC Admin
       </span>
   @endif
   ```

3. **Modal Components**:
   - Approve confirmation modal
   - Reject form modal with character counter
   - Proper styling with Tailwind CSS

4. **JavaScript Functions**:
   - `showApproveModal(visitId)`
   - `confirmApprove()`
   - `showRejectModal(visitId)`
   - `confirmReject(event)`
   - `updateRejectCharCount()`
   - AJAX handling with error management
   - Notification system

## Workflow

### **Flow Kunjungan dengan ACC**
1. **Auditor** melakukan kunjungan → Status: `dalam_perjalanan`
2. **Auditor** menyelesaikan kunjungan → Status: `menunggu_acc`
3. **Admin** melihat laporan dengan badge "Menunggu ACC Admin"
4. **Admin** memilih salah satu:
   
   **Option A - ACC:**
   - Klik "ACC / Setujui"
   - Konfirmasi di modal
   - Status berubah → `selesai`
   - `completed_at` diisi dengan timestamp
   
   **Option B - Reject:**
   - Klik "Tolak / Reject"
   - Isi alasan penolakan (min 10 karakter)
   - Submit form
   - Status berubah → `belum_dikunjungi`
   - Data laporan dihapus (selfie, koordinat, notes)
   - Auditor harus melakukan kunjungan ulang

### **Validasi dan Error Handling**
- Validasi status sebelum ACC/Reject
- Validasi panjang alasan penolakan
- AJAX error handling dengan notifikasi
- Loading states pada buttons
- Auto-refresh setelah sukses

## Routes yang Digunakan
- `POST /admin/visits/{visit}/approve` - Approve laporan
- `POST /admin/visits/{visit}/reject` - Reject laporan dengan alasan

## Security Features
- CSRF token protection untuk semua AJAX requests
- Validasi status kunjungan sebelum action
- Sanitasi input untuk rejection notes
- Role-based access (hanya admin)

## UI/UX Enhancements
- Real-time character counter dengan color coding
- Animasi pulse untuk status menunggu ACC
- Loading states pada form submission
- Toast notifications untuk feedback
- Responsive design untuk mobile
- Icon-based visual hierarchy

## Testing Checklist
- [ ] ACC laporan dengan status menunggu_acc berhasil
- [ ] Reject laporan dengan alasan valid berhasil  
- [ ] Validasi status sebelum ACC/Reject
- [ ] Validasi minimal 10 karakter untuk rejection notes
- [ ] AJAX error handling bekerja
- [ ] Notification system menampilkan pesan yang tepat
- [ ] Loading states berfungsi
- [ ] Auto-refresh setelah action
- [ ] Filter menunggu_acc berfungsi
- [ ] Stats card menampilkan jumlah yang benar
- [ ] Responsive design pada mobile

## Browser Support
- Modern browsers dengan ES6 support
- Fetch API compatibility
- CSS Grid dan Flexbox support

Fitur ACC telah berhasil diimplementasikan dan siap untuk digunakan oleh admin untuk mengelola persetujuan laporan kunjungan auditor.