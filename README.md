# 📄 Report Generator - Berita Acara Pemasangan PDF

## Deskripsi

**Report Generator** adalah aplikasi web yang dikembangkan untuk membantu petugas lapangan dalam membuat **Berita Acara Pemasangan** dalam bentuk PDF secara cepat dan efisien. Aplikasi ini dibuat atas permintaan perusahaan, guna mendigitalisasi proses pencatatan yang sebelumnya dilakukan secara manual.

Dengan aplikasi ini, petugas hanya perlu login dan mengisi form yang tersedia. Hasil inputan tersebut secara otomatis akan diubah menjadi file PDF yang siap untuk dicetak atau dibagikan.

## 🎯 Tujuan

Meningkatkan efisiensi dan akurasi pencatatan laporan pemasangan di lapangan, serta memudahkan proses dokumentasi dan verifikasi oleh pengawas pekerjaan.

## ✨ Fitur Utama

- 🔐 **Login Otentikasi**: Petugas dapat login menggunakan username/email dan password yang telah diset oleh Admin.
- 📝 **Form Input Dinamis**: Formulir pengisian data Berita Acara Pemasangan yang mudah digunakan.
- 📄 **Otomatisasi PDF**: Data dari form akan secara otomatis di-generate menjadi file PDF sesuai format resmi.
- 👨‍💼 **Role-Based Access**: Hanya petugas yang memiliki akun yang dapat mengakses dan mengisi form.
- 📦 **Export Laporan**: PDF dapat langsung diunduh atau dicetak sebagai dokumen resmi.

## 🛠️ Teknologi yang Digunakan

- **Frontend**: HTML, CSS, JavaScript / Blade
- **Backend**: Laravel 12 / PHP
- **PDF Generator**: Laravel DOMPDF
- **Database**: MySQL

## 🚀 Cara Menggunakan

1. Akses halaman login di browser.
2. Masukkan **email/username** dan **password**.
3. Setelah login berhasil, pilih menu **Create Berita Acara Pemasangan**.
4. Isi form sesuai kondisi lapangan.
5. Klik tombol **Generate PDF** untuk membuat dan mengunduh laporan.

## 🔐 Akses Admin

Admin bertanggung jawab dalam:
- Membuat dan mengelola akun petugas
- Melihat data laporan yang telah dibuat
- Melakukan update jika format PDF mengalami perubahan

## 📌 Catatan Tambahan

- Pastikan setiap petugas telah memiliki akun aktif sebelum menggunakan aplikasi.
- Format PDF dapat disesuaikan berdasarkan permintaan perusahaan.
- Aplikasi ini dapat dikembangkan lebih lanjut untuk jenis laporan lainnya (mis. perbaikan, pengecekan berkala, dll.)

## 👨‍💻 Pengembang

Project ini dikembangkan oleh **E. Andhika Alfira Damara**, freelance programmer yang ditugaskan oleh perusahaan Prisan Artha Lestari dalam rangka digitalisasi proses dokumentasi lapangan.

---

Terima kasih telah menggunakan Report Generator! 🎉
